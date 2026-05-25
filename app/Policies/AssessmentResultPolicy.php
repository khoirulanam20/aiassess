<?php

namespace App\Policies;

use App\Models\AssessmentResult;
use App\Models\User;

class AssessmentResultPolicy
{
    public function view(User $user, AssessmentResult $result): bool
    {
        if ($user->can('results.view_any')) {
            return true;
        }

        if ($user->can('results.view_org')) {
            return $result->user->organization_id === $user->organization_id;
        }

        return $result->user_id === $user->id;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('results.view_any') || $user->can('results.view_org');
    }

    public function viewOrg(User $user, AssessmentResult $result): bool
    {
        if ($user->can('results.view_any')) {
            return true;
        }

        return $user->can('results.view_org')
            && $result->user->organization_id === $user->organization_id;
    }

    public function shareCreate(User $user, AssessmentResult $result): bool
    {
        if ($user->can('results.view_any')) {
            return true;
        }

        return $user->can('results.share_create')
            && $result->user->organization_id === $user->organization_id;
    }

    public function shareRevoke(User $user, AssessmentResult $result): bool
    {
        if ($user->can('results.view_any')) {
            return true;
        }

        return $user->can('results.share_revoke')
            && $result->user->organization_id === $user->organization_id;
    }

    public function delete(User $user, AssessmentResult $result): bool
    {
        return $user->can('results.delete');
    }
}
