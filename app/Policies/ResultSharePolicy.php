<?php

namespace App\Policies;

use App\Models\ResultShare;
use App\Models\User;

class ResultSharePolicy
{
    public function revoke(User $user, ResultShare $share): bool
    {
        $result = $share->assessmentResult;

        if (! $result) {
            return false;
        }

        if ($user->can('results.view_any')) {
            return true;
        }

        return $user->can('results.share_revoke')
            && $result->user->organization_id === $user->organization_id;
    }

    public function regenerate(User $user, ResultShare $share): bool
    {
        return $this->revoke($user, $share);
    }
}
