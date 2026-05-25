<?php

namespace App\Support;

use App\Models\User;

class MasterDataAccess
{
    /**
     * Master data (departemen & posisi) hanya untuk HR/Admin per tenant, bukan superadmin.
     */
    public static function isHrAdmin(User $user): bool
    {
        return $user->hasRole('admin') && $user->organization_id !== null;
    }

    public static function canView(User $user): bool
    {
        return self::isHrAdmin($user) && $user->can('master_data.view');
    }

    public static function canManage(User $user): bool
    {
        return self::isHrAdmin($user) && $user->can('master_data.manage');
    }
}
