<?php

namespace App\Policies;

use App\Models\AssessmentBatch;
use App\Models\User;

class AssessmentBatchPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('batches.view');
    }

    public function view(User $user, AssessmentBatch $batch): bool
    {
        if (! $user->can('batches.view')) {
            return false;
        }

        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->organization_id === $batch->organization_id;
    }

    public function create(User $user): bool
    {
        return $user->can('batches.create');
    }

    public function update(User $user, AssessmentBatch $batch): bool
    {
        if (! $user->can('batches.update')) {
            return false;
        }

        return $user->hasRole('superadmin')
            || $user->organization_id === $batch->organization_id;
    }

    public function delete(User $user, AssessmentBatch $batch): bool
    {
        if (! $user->can('batches.delete')) {
            return false;
        }

        return $user->hasRole('superadmin')
            || $user->organization_id === $batch->organization_id;
    }
}
