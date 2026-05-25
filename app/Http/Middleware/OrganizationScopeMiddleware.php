<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrganizationScopeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->hasRole('superadmin')) {
            return $next($request);
        }

        if ($user && $user->hasRole('admin')) {
            if (! $user->organization_id) {
                abort(403, __('Admin tidak memiliki organisasi yang ditentukan.'));
            }

            $request->attributes->set('scope_organization_id', $user->organization_id);
        }

        return $next($request);
    }
}
