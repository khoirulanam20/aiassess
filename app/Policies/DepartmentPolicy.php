<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;
use App\Support\MasterDataAccess;

class DepartmentPolicy
{
    public function viewAny(User $user): bool
    {
        return MasterDataAccess::canView($user);
    }

    public function view(User $user, Department $department): bool
    {
        return $this->belongsToUserOrg($user, $department);
    }

    public function create(User $user): bool
    {
        return MasterDataAccess::canManage($user);
    }

    public function update(User $user, Department $department): bool
    {
        return MasterDataAccess::canManage($user) && $this->belongsToUserOrg($user, $department);
    }

    public function delete(User $user, Department $department): bool
    {
        return MasterDataAccess::canManage($user) && $this->belongsToUserOrg($user, $department);
    }

    private function belongsToUserOrg(User $user, Department $department): bool
    {
        if (! MasterDataAccess::canView($user)) {
            return false;
        }

        return $user->organization_id === $department->organization_id;
    }
}
