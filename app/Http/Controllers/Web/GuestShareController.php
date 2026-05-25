<?php

namespace App\Http\Controllers\Web;

use App\Http\Middleware\ShareGuestMiddleware;
use App\Models\AssessmentResult;
use App\Models\ResultShare;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class GuestShareController extends BaseController
{
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 30;

    public function __construct(
        private readonly ActivityLogService $activityLog
    ) {}

    public function showForm(string $token): View
    {
        $share = ResultShare::where('share_token', $token)->first();

        if (! $share) {
            abort(404);
        }

        return view('shared.guest-form', compact('token', 'share'));
    }

    public function verify(Request $request, string $token): RedirectResponse
    {
        $request->validate([
            'access_code' => ['required', 'string', 'min:4', 'max:16'],
        ]);

        $share = ResultShare::where('share_token', $token)->first();

        if (! $share) {
            return back()->withErrors(['access_code' => __('Share link tidak valid.')]);
        }

        if (! $share->isValid()) {
            return back()->withErrors(['access_code' => __('Share ini sudah tidak aktif.')]);
        }

        if ($share->isLocked()) {
            $minutes = now()->diffInMinutes($share->locked_until);
            return back()->withErrors([
                'access_code' => __('Terlalu banyak percobaan. Coba lagi dalam :minutes menit.', ['minutes' => $minutes]),
            ]);
        }

        if (! Hash::check($request->access_code, $share->access_code_hash)) {
            $share->increment('failed_attempts');

            if ($share->failed_attempts >= self::MAX_ATTEMPTS) {
                $share->update([
                    'locked_until' => now()->addMinutes(self::LOCKOUT_MINUTES),
                    'failed_attempts' => 0,
                ]);
            }

            $remaining = self::MAX_ATTEMPTS - ($share->failed_attempts % self::MAX_ATTEMPTS);

            return back()->withErrors([
                'access_code' => __('Kode akses tidak valid. Sisa percobaan: :count.', ['count' => max(0, $remaining)]),
            ]);
        }

        $share->update([
            'failed_attempts' => 0,
            'locked_until' => null,
        ]);

        $share->increment('view_count');

        $cookiePayload = ShareGuestMiddleware::buildCookiePayload($share);
        $encrypted = Crypt::encrypt($cookiePayload);

        $this->activityLog->log('share.verified', "Guest verified share: {$share->share_token}", [
            'share_id' => $share->id,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('shared.result.view', $token)
            ->cookie(
                ShareGuestMiddleware::cookieName(),
                $encrypted,
                ShareGuestMiddleware::sessionTtl(),
                '/',
                null,
                true,
                true,
                false,
                'lax'
            );
    }

    public function result(Request $request): View
    {
        $share = $request->attributes->get('guest_share');

        $result = AssessmentResult::with(['user.userDetail', 'assessment'])
            ->findOrFail($share->assessment_result_id);

        return view('shared.result', compact('result'));
    }

    public function download(Request $request)
    {
        $share = $request->attributes->get('guest_share');

        $result = AssessmentResult::with(['assessment', 'user'])
            ->findOrFail($share->assessment_result_id);

        $decodedResult = json_decode($result->result, true) ?? [];
        $assessmentName = $result->assessment?->name ?? $result->test_name;

        $pdf = app('dompdf.wrapper')->loadView('shared.result-pdf', compact('result', 'decodedResult', 'assessmentName'));

        return $pdf->download("hasil-{$assessmentName}.pdf");
    }
}
