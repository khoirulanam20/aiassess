<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->can('users.view_any') || $actor->can('candidates.view');
    }

    public function view(User $actor, User $target): bool
    {
        if ($actor->can('users.view_any')) {
            return true;
        }

        if ($actor->can('users.view') && $actor->hasRole('admin')) {
            return $target->organization_id === $actor->organization_id;
        }

        if ($actor->can('candidates.view')) {
            return $target->organization_id === $actor->organization_id
                && $target->hasRole('candidate');
        }

        return $actor->id === $target->id;
    }

    public function create(User $actor): bool
    {
        return $actor->can('users.create') || $actor->can('candidates.create');
    }

    public function createHr(User $actor): bool
    {
        return $actor->can('users.create') && $actor->can('users.assign_role');
    }

    public function createCandidate(User $actor): bool
    {
        return $actor->can('candidates.create') && $actor->hasRole('admin');
    }

    public function update(User $actor, User $target): bool
    {
        if ($actor->can('users.update')) {
            if ($actor->hasRole('superadmin')) {
                return true;
            }

            return $target->organization_id === $actor->organization_id;
        }

        if ($actor->can('candidates.update')) {
            return $target->organization_id === $actor->organization_id
                && $target->hasRole('candidate');
        }

        return $actor->id === $target->id;
    }

    public function delete(User $actor, User $target): bool
    {
        if ($actor->can('users.delete')) {
            return ! $target->hasRole('superadmin');
        }

        if ($actor->can('candidates.delete')) {
            return $target->organization_id === $actor->organization_id
                && $target->hasRole('candidate');
        }

        return false;
    }

    public function assignRole(User $actor, User $target): bool
    {
        if (! $actor->can('users.assign_role')) {
            return false;
        }

        return ! $target->hasRole('superadmin');
    }
}
