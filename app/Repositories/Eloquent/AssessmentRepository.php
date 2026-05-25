<?php

namespace App\Repositories\Eloquent;

use App\Models\Assessment;
use App\Repositories\Contracts\AssessmentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AssessmentRepository extends BaseRepository implements AssessmentRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Assessment);
    }

    public function findBySlug(string $slug): ?Assessment
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function getActiveAssessments(): Collection
    {
        return $this->model->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function getActivePaginated(int $perPage = 12): LengthAwarePaginator
    {
        return $this->model->where('is_active', true)
            ->orderBy('sort_order')
            ->paginate($perPage);
    }

    public function getByType(string $type): Collection
    {
        return $this->model->where('is_active', true)
            ->where('type', $type)
            ->orderBy('sort_order')
            ->get();
    }
}
