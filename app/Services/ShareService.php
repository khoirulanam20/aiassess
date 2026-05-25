<?php

namespace App\Services;

use App\Models\AssessmentResult;
use Illuminate\Support\Str;

class ShareService extends BaseService
{
    public function generateShareToken(): string
    {
        return Str::random(32);
    }

    public function createShareLink(AssessmentResult $result): AssessmentResult
    {
        if (! $result->share_token) {
            $result->update([
                'share_token' => $this->generateShareToken(),
            ]);
        }

        return $result->fresh();
    }

    public function getSharedResult(string $token): ?AssessmentResult
    {
        $result = AssessmentResult::with(['user.userDetail', 'assessment'])
            ->where('share_token', $token)
            ->first();

        if ($result) {
            $result->increment('share_views');
        }

        return $result;
    }
}
