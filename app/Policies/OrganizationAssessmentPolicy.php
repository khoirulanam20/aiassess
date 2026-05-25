<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\OrganizationAssessment;
use App\Models\User;

class OrganizationAssessmentPolicy
{
    public function manageGlobal(User $user): bool
    {
        return $user->can('assessments.manage_global');
    }

    public function manageOrg(User $user, ?Organization $organization = null): bool
    {
        if (! $user->can('assessments.manage_org')) {
            return false;
        }

        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $organization === null
            || $user->organization_id === $organization->id;
    }

    public function update(User $user, OrganizationAssessment $orgAssessment): bool
    {
        if ($user->can('assessments.manage_global')) {
            return true;
        }

        return $user->can('assessments.manage_org')
            && $user->organization_id === $orgAssessment->organization_id;
    }
}
