<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\Request;

class OrganizationContext
{
    public static function resolveOrganizationId(?User $user, ?Request $request = null): ?int
    {
        if (! $user) {
            return null;
        }

        if ($user->hasRole('superadmin')) {
            $request ??= request();

            return $request?->attributes->get('scope_organization_id')
                ?? ($request?->integer('organization_id') ?: null);
        }

        return $user->organization_id;
    }

    public static function requireOrganizationId(?User $user, ?Request $request = null): int
    {
        $id = self::resolveOrganizationId($user, $request);

        if (! $id) {
            abort(403, __('Organisasi tidak ditentukan.'));
        }

        return $id;
    }
}
