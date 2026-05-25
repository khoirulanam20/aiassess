<?php

namespace App\Policies;

use App\Models\Position;
use App\Models\User;
use App\Support\MasterDataAccess;

class PositionPolicy
{
    public function viewAny(User $user): bool
    {
        return MasterDataAccess::canView($user);
    }

    public function view(User $user, Position $position): bool
    {
        return $this->belongsToUserOrg($user, $position);
    }

    public function create(User $user): bool
    {
        return MasterDataAccess::canManage($user);
    }

    public function update(User $user, Position $position): bool
    {
        return MasterDataAccess::canManage($user) && $this->belongsToUserOrg($user, $position);
    }

    public function delete(User $user, Position $position): bool
    {
        return MasterDataAccess::canManage($user) && $this->belongsToUserOrg($user, $position);
    }

    private function belongsToUserOrg(User $user, Position $position): bool
    {
        if (! MasterDataAccess::canView($user)) {
            return false;
        }

        return $user->organization_id === $position->organization_id;
    }
}
