<?php

namespace App\Repositories\Contracts;

use App\Models\Assessment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface AssessmentRepositoryInterface extends BaseRepositoryInterface
{
    public function findBySlug(string $slug): ?Assessment;

    public function getActiveAssessments(): Collection;

    public function getActivePaginated(int $perPage = 12): LengthAwarePaginator;

    public function getByType(string $type): Collection;
}
