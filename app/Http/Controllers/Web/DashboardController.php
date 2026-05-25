<?php

namespace App\Http\Controllers\Web;

use App\Repositories\Contracts\AssessmentRepositoryInterface;
use App\Repositories\Contracts\AssessmentResultRepositoryInterface;
use App\Services\AssessmentBatchService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends BaseController
{
    public function __construct(
        private readonly AssessmentRepositoryInterface $assessmentRepo,
        private readonly AssessmentResultRepositoryInterface $resultRepo,
        private readonly AssessmentBatchService $batchService,
    ) {}

    public function index()
    {
        $user = Auth::user();
        $assessments = $this->assessmentRepo->getActiveAssessments()
            ->map(function ($assessment) use ($user) {
                $latestResult = $this->resultRepo->getLatestResult($user->id, $assessment->id);

                $assessment->has_completed = ! is_null($latestResult);
                $assessment->can_retake = is_null($latestResult) || $latestResult->created_at->addDays($assessment->cooldown_days)->isPast();

                return $assessment;
            });

        $completedCount = $this->resultRepo->countUserCompleted($user->id);
        $batchAssignments = $user->hasRole('user')
            ? $this->batchService->pendingAssignmentsForUser($user->id)
            : collect();

        return view('dashboard.index', compact('assessments', 'completedCount', 'batchAssignments'));
    }
}
