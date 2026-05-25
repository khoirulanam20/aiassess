<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentBatch;
use App\Models\AssessmentBatchAssignment;
use App\Models\AssessmentResult;
use App\Models\OrganizationAssessment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AssessmentBatchService extends BaseService
{
    /**
     * @param  array<int>  $assessmentIds
     * @param  array<int>  $userIds
     */
    public function createBatch(
        int $organizationId,
        int $createdBy,
        string $name,
        ?string $description,
        array $assessmentIds,
        array $userIds,
        ?string $startsAt = null,
        ?string $endsAt = null,
    ): AssessmentBatch {
        $enabledAssessmentIds = OrganizationAssessment::query()
            ->where('organization_id', $organizationId)
            ->where('is_enabled', true)
            ->pluck('assessment_id')
            ->all();

        $validAssessmentIds = array_values(array_intersect(
            array_map('intval', $assessmentIds),
            $enabledAssessmentIds
        ));

        if ($validAssessmentIds === []) {
            throw new \InvalidArgumentException(__('Pilih minimal satu assessment yang aktif untuk perusahaan.'));
        }

        $validUserIds = User::role('candidate')
            ->where('organization_id', $organizationId)
            ->whereIn('id', array_map('intval', $userIds))
            ->pluck('id')
            ->all();

        if ($validUserIds === []) {
            throw new \InvalidArgumentException(__('Pilih minimal satu kandidat/karyawan.'));
        }

        return DB::transaction(function () use (
            $organizationId,
            $createdBy,
            $name,
            $description,
            $validAssessmentIds,
            $validUserIds,
            $startsAt,
            $endsAt,
        ) {
            $batch = AssessmentBatch::create([
                'organization_id' => $organizationId,
                'created_by' => $createdBy,
                'name' => $name,
                'description' => $description,
                'status' => AssessmentBatch::STATUS_ACTIVE,
                'guest_share_token' => Str::random(48),
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
            ]);

            foreach ($validUserIds as $userId) {
                foreach ($validAssessmentIds as $assessmentId) {
                    AssessmentBatchAssignment::create([
                        'assessment_batch_id' => $batch->id,
                        'user_id' => $userId,
                        'assessment_id' => $assessmentId,
                        'status' => AssessmentBatchAssignment::STATUS_PENDING,
                    ]);
                }
            }

            return $batch->load(['assignments.user', 'assignments.assessment', 'creator']);
        });
    }

    public function markAssignmentCompleted(int $userId, int $assessmentId, AssessmentResult $result, ?int $batchId = null): void
    {
        $query = AssessmentBatchAssignment::query()
            ->where('user_id', $userId)
            ->where('assessment_id', $assessmentId)
            ->where('status', AssessmentBatchAssignment::STATUS_PENDING);

        if ($batchId !== null) {
            $query->where('assessment_batch_id', $batchId);
        } else {
            $query->whereHas('batch', function ($query) {
                $query->where('status', AssessmentBatch::STATUS_ACTIVE)
                    ->where(function ($q) {
                        $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
                    });
            });
        }

        $query->update([
            'status' => AssessmentBatchAssignment::STATUS_COMPLETED,
            'assessment_result_id' => $result->id,
            'completed_at' => now(),
        ]);
    }

    /**
     * Sinkronkan penugasan yang masih pending padahal hasil sudah ada (data lama / submit gagal update).
     */
    public function reconcilePendingAssignments(int $batchId, int $userId): void
    {
        AssessmentBatchAssignment::query()
            ->where('assessment_batch_id', $batchId)
            ->where('user_id', $userId)
            ->where('status', AssessmentBatchAssignment::STATUS_PENDING)
            ->each(function (AssessmentBatchAssignment $assignment) {
                $result = AssessmentResult::query()
                    ->where('user_id', $assignment->user_id)
                    ->where('assessment_id', $assignment->assessment_id)
                    ->where('created_at', '>=', $assignment->created_at)
                    ->latest('id')
                    ->first();

                if ($result) {
                    $assignment->update([
                        'status' => AssessmentBatchAssignment::STATUS_COMPLETED,
                        'assessment_result_id' => $result->id,
                        'completed_at' => $result->created_at,
                    ]);
                }
            });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, AssessmentBatchAssignment>
     */
    public function pendingAssignmentsForUser(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return AssessmentBatchAssignment::query()
            ->with(['batch', 'assessment'])
            ->where('user_id', $userId)
            ->where('status', AssessmentBatchAssignment::STATUS_PENDING)
            ->whereHas('batch', function ($query) {
                $query->where('status', AssessmentBatch::STATUS_ACTIVE)
                    ->where(function ($q) {
                        $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
                    });
            })
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * @return \Illuminate\Support\Collection<int, Assessment>
     */
    public function enabledAssessmentsForOrganization(int $organizationId): \Illuminate\Support\Collection
    {
        return OrganizationAssessment::query()
            ->where('organization_id', $organizationId)
            ->where('is_enabled', true)
            ->with('assessment')
            ->get()
            ->map(fn (OrganizationAssessment $oa) => $oa->assessment)
            ->filter(fn (?Assessment $a) => $a && $a->is_active)
            ->values();
    }
}
