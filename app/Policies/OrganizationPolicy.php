<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;

class OrganizationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('organizations.manage');
    }

    public function view(User $user, Organization $organization): bool
    {
        if ($user->can('organizations.manage')) {
            return true;
        }

        return $user->can('organizations.view_own')
            && $user->organization_id === $organization->id;
    }

    public function create(User $user): bool
    {
        return $user->can('organizations.manage');
    }

    public function update(User $user, Organization $organization): bool
    {
        if ($user->can('organizations.manage')) {
            return true;
        }

        return $user->can('organizations.update_own')
            && $user->organization_id === $organization->id;
    }

    public function delete(User $user, Organization $organization): bool
    {
        return $user->can('organizations.manage');
    }

    public function manageOrg(User $user, Organization $organization): bool
    {
        if (! $user->can('assessments.manage_org')) {
            return false;
        }

        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->organization_id === $organization->id;
    }
}
