<?php

namespace App\Http\Controllers\Web;

use App\Models\AssessmentResult;
use App\Models\ResultShare;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ShareController extends BaseController
{
    public function __construct(
        private readonly ActivityLogService $activityLog
    ) {}

    public function store(Request $request, AssessmentResult $result): RedirectResponse
    {
        $this->authorize('shareCreate', $result);

        $shareToken = Str::random(48);
        $accessCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $share = ResultShare::create([
            'assessment_result_id' => $result->id,
            'created_by' => $request->user()->id,
            'share_token' => $shareToken,
            'access_code_hash' => Hash::make($accessCode, ['cost' => 12]),
        ]);

        $this->activityLog->log('share.created', "Share created for result #{$result->id}", [
            'result_id' => $result->id,
            'share_id' => $share->id,
        ]);

        return back()->with([
            'share_created' => true,
            'share_url' => route('shared.result.form', $shareToken),
            'access_code' => $accessCode,
            'share_id' => $share->id,
        ]);
    }

    public function revoke(Request $request, ResultShare $share): RedirectResponse
    {
        $this->authorize('revoke', $share);

        $share->update([
            'is_active' => false,
            'revoked_at' => now(),
            'revoked_by' => $request->user()->id,
        ]);

        $this->activityLog->log('share.revoked', "Share #{$share->id} revoked", [
            'share_id' => $share->id,
        ]);

        return back()->with('success', __('Share link berhasil dicabut.'));
    }

    public function regenerate(Request $request, ResultShare $share): RedirectResponse
    {
        $this->authorize('regenerate', $share);

        $newCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $share->update([
            'access_code_hash' => Hash::make($newCode, ['cost' => 12]),
            'failed_attempts' => 0,
            'locked_until' => null,
        ]);

        $this->activityLog->log('share.code_regenerated', "Access code regenerated for share #{$share->id}", [
            'share_id' => $share->id,
        ]);

        return back()->with([
            'share_created' => true,
            'share_url' => route('shared.result.form', $share->share_token),
            'access_code' => $newCode,
            'share_id' => $share->id,
        ]);
    }
}
