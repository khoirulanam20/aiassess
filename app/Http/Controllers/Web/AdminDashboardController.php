<?php

namespace App\Http\Controllers\Web;

use App\Models\AssessmentResult;
use App\Models\User;
use App\Repositories\Contracts\AssessmentRepositoryInterface;
use App\Repositories\Contracts\AssessmentResultRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends BaseController
{
    public function __construct(
        private readonly AssessmentRepositoryInterface $assessmentRepo,
        private readonly AssessmentResultRepositoryInterface $resultRepo,
    ) {}

    public function index()
    {
        $this->authorize('viewAny', AssessmentResult::class);

        $user = Auth::user();

        if ($user->hasRole('superadmin')) {
            $totalUsers = User::count();
            $totalResults = $this->resultRepo->countAll();
            $recentResults = $this->resultRepo->getLatest(10);
        } else {
            $totalUsers = User::where('organization_id', $user->organization_id)->count();
            $totalResults = $this->resultRepo->countByOrganization($user->organization_id);
            $recentResults = $this->resultRepo->getLatestByOrganization($user->organization_id, 10);
        }

        $assessments = $this->assessmentRepo->getActiveAssessments();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalResults',
            'recentResults',
            'assessments'
        ));
    }
}
