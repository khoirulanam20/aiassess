<?php

namespace App\Repositories\Contracts;

use App\Models\AssessmentResult;
use Illuminate\Pagination\LengthAwarePaginator;

interface AssessmentResultRepositoryInterface extends BaseRepositoryInterface
{
    public function getUserResults(int $userId, int $perPage = 20): LengthAwarePaginator;

    public function getLatestResult(int $userId, int $assessmentId): ?AssessmentResult;

    public function getByShareToken(string $token): ?AssessmentResult;

    public function getUserResultsByType(int $userId, string $testName): LengthAwarePaginator;

    public function countUserCompleted(int $userId): int;

    public function countAll(): int;

    public function countByOrganization(?int $organizationId): int;

    public function getLatest(int $limit = 10): array;

    public function getLatestByOrganization(?int $organizationId, int $limit = 10): array;
}
