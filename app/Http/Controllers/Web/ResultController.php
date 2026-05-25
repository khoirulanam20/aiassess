<?php

namespace App\Http\Controllers\Web;

use App\Repositories\Contracts\AssessmentResultRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class ResultController extends BaseController
{
    public function __construct(
        private readonly AssessmentResultRepositoryInterface $resultRepo,
    ) {}

    public function history()
    {
        $results = $this->resultRepo->getUserResults(Auth::id(), 20);

        return view('assessments.history', compact('results'));
    }
}
