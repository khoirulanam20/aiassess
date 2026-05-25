<?php

namespace App\Repositories\Eloquent;

use App\Models\AssessmentResult;
use App\Repositories\Contracts\AssessmentResultRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class AssessmentResultRepository extends BaseRepository implements AssessmentResultRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new AssessmentResult);
    }

    public function getUserResults(int $userId, int $perPage = 20): LengthAwarePaginator
    {
        return AssessmentResult::with('assessment')
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }

    public function getLatestResult(int $userId, int $assessmentId): ?AssessmentResult
    {
        return AssessmentResult::where('user_id', $userId)
            ->where('assessment_id', $assessmentId)
            ->latest()
            ->first();
    }

    public function getByShareToken(string $token): ?AssessmentResult
    {
        return AssessmentResult::with(['user', 'assessment'])
            ->where('share_token', $token)
            ->first();
    }

    public function getUserResultsByType(int $userId, string $testName): LengthAwarePaginator
    {
        return AssessmentResult::with('assessment')
            ->where('user_id', $userId)
            ->where('test_name', $testName)
            ->latest()
            ->paginate(20);
    }

    public function countUserCompleted(int $userId): int
    {
        return AssessmentResult::where('user_id', $userId)->count();
    }

    public function countAll(): int
    {
        return AssessmentResult::count();
    }

    public function countByOrganization(?int $organizationId): int
    {
        return AssessmentResult::whereHas('user', function ($q) use ($organizationId) {
            $q->where('organization_id', $organizationId);
        })->count();
    }

    public function getLatest(int $limit = 10): array
    {
        return AssessmentResult::with(['user', 'assessment'])
            ->latest()
            ->take($limit)
            ->get()
            ->toArray();
    }

    public function getLatestByOrganization(?int $organizationId, int $limit = 10): array
    {
        return AssessmentResult::with(['user', 'assessment'])
            ->whereHas('user', function ($q) use ($organizationId) {
                $q->where('organization_id', $organizationId);
            })
            ->latest()
            ->take($limit)
            ->get()
            ->toArray();
    }
}
