<?php

namespace App\Services;

use App\Models\AssessmentBatch;
use App\Models\AssessmentBatchShare;
use Illuminate\Support\Facades\Hash;

class BatchGuestShareService extends BaseService
{
    public function findActiveShareByAccessCode(string $accessCode, ?AssessmentBatch $batch = null): ?AssessmentBatchShare
    {
        $query = AssessmentBatchShare::query()
            ->where('is_active', true)
            ->whereNull('revoked_at')
            ->with(['batch', 'user'])
            ->whereHas('batch', fn ($q) => $q->where('status', AssessmentBatch::STATUS_ACTIVE));

        if ($batch) {
            $query->where('assessment_batch_id', $batch->id);
        }

        return $query->get()->first(function (AssessmentBatchShare $share) use ($accessCode) {
            if (! $share->isValid() || $share->isLocked()) {
                return false;
            }

            return Hash::check($accessCode, $share->access_code_hash);
        });
    }

    public function resolveBatchByGuestToken(string $token): ?AssessmentBatch
    {
        return AssessmentBatch::query()
            ->where('guest_share_token', $token)
            ->where('status', AssessmentBatch::STATUS_ACTIVE)
            ->first();
    }
}
