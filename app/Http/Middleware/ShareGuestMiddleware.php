<?php

namespace App\Http\Middleware;

use App\Models\ResultShare;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

class ShareGuestMiddleware
{
    private const COOKIE_NAME = 'guest_share_session';
    private const SESSION_TTL = 120;

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->route('token');

        $share = ResultShare::where('share_token', $token)
            ->where('is_active', true)
            ->whereNull('revoked_at')
            ->first();

        if (! $share || ! $share->isValid()) {
            return redirect()->route('shared.result.form', $token);
        }

        $cookie = $request->cookie(self::COOKIE_NAME);

        if (! $cookie) {
            return redirect()->route('shared.result.form', $token);
        }

        try {
            $payload = Crypt::decrypt($cookie);

            $parts = explode('|', $payload);
            $cookieToken = $parts[0] ?? '';
            $cookieResultId = $parts[1] ?? '';

            if ($cookieToken !== $share->share_token || (int) $cookieResultId !== $share->assessment_result_id) {
                return redirect()->route('shared.result.form', $token);
            }
        } catch (\Exception) {
            return redirect()->route('shared.result.form', $token);
        }

        $request->attributes->set('guest_share', $share);
        $request->attributes->set('guest_result_id', $share->assessment_result_id);

        return $next($request);
    }

    public static function buildCookiePayload(ResultShare $share): string
    {
        return implode('|', [
            $share->share_token,
            (string) $share->assessment_result_id,
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
