<?php

namespace App\Http\Middleware;

use App\Models\AssessmentBatch;
use App\Models\AssessmentBatchShare;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

class BatchGuestMiddleware
{
    private const COOKIE_NAME = 'guest_batch_session';

    private const SESSION_TTL = 120;

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->route('token');

        $batch = AssessmentBatch::query()
            ->where('guest_share_token', $token)
            ->where('status', AssessmentBatch::STATUS_ACTIVE)
            ->first();

        if (! $batch) {
            return redirect()->route('shared.batch.entry');
        }

        $cookie = $request->cookie(self::COOKIE_NAME);

        if (! $cookie) {
            return redirect()->route('shared.batch.form', $token);
        }

        try {
            $payload = Crypt::decrypt($cookie);
            $parts = explode('|', $payload);
            $cookieToken = $parts[0] ?? '';
            $cookieUserId = (int) ($parts[1] ?? 0);
            $cookieBatchId = (int) ($parts[2] ?? 0);

            if ($cookieToken !== $batch->guest_share_token
                || $cookieBatchId !== $batch->id) {
                return redirect()->route('shared.batch.form', $token);
            }

            $share = AssessmentBatchShare::query()
                ->where('assessment_batch_id', $batch->id)
                ->where('user_id', $cookieUserId)
                ->where('is_active', true)
                ->whereNull('revoked_at')
                ->with('batch', 'user')
                ->first();

            if (! $share || ! $share->isValid()) {
                return redirect()->route('shared.batch.form', $token);
            }
        } catch (\Exception) {
            return redirect()->route('shared.batch.form', $token);
        }

        $request->attributes->set('guest_batch_share', $share);

        return $next($request);
    }

    public static function buildCookiePayload(AssessmentBatchShare $share): string
    {
        $share->loadMissing('batch');

        return implode('|', [
            $share->batch->guest_share_token,
            (string) $share->user_id,
            (string) $share->assessment_batch_id,
            now()->toDateTimeString(),
        ]);
    }

    public static function cookieName(): string
    {
        return self::COOKIE_NAME;
    }

    public static function sessionTtl(): int
    {
        return self::SESSION_TTL;
    }
}
