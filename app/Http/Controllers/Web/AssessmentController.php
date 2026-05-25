<?php

namespace App\Http\Controllers\Web;

use App\Models\AssessmentResult;
use App\Repositories\Contracts\AssessmentRepositoryInterface;
use App\Repositories\Contracts\AssessmentResultRepositoryInterface;
use App\Services\AssessmentService;
use App\Services\QuestionService;
use App\Services\ShareService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssessmentController extends BaseController
{
    public function __construct(
        private readonly AssessmentRepositoryInterface $assessmentRepo,
        private readonly AssessmentResultRepositoryInterface $resultRepo,
        private readonly AssessmentService $assessmentService,
        private readonly QuestionService $questionService,
        private readonly ShareService $shareService,
    ) {}

    public function index()
    {
        $assessments = $this->assessmentRepo->getActivePaginated(12);
        $allAssessments = $this->assessmentRepo->getActiveAssessments();

        return view('assessments.index', compact('assessments', 'allAssessments'));
    }

    public function show(string $slug)
    {
        $assessment = $this->assessmentRepo->findBySlug($slug);

        if (! $assessment || ! $assessment->is_active) {
            abort(404);
        }

        $latestResult = $this->resultRepo->getLatestResult(Auth::id(), $assessment->id);

        return view('assessments.show', compact('assessment', 'latestResult'));
    }

    public function take(string $slug)
    {
        $assessment = $this->assessmentRepo->findBySlug($slug);

        if (! $assessment || ! $assessment->is_active) {
            abort(404);
        }

        $check = $this->assessmentService->canTakeAssessment(Auth::id(), $assessment->id);

        if (! ($check['success'] ?? false)) {
            return redirect()
                ->route('assessments.show', $slug)
                ->with('error', $check['message'] ?? __('You have already taken this assessment. Please wait for the cooldown period.'));
        }

        $questions = $slug === 'agility'
            ? $this->questionService->loadAgilityQuestions(app()->getLocale())
            : $this->questionService->loadQuestions($slug, app()->getLocale());
        $questions = $this->questionService->shuffleQuestions($questions);
        $questions = $this->questionService->formatForFrontend($questions);

        return view('assessments.take', compact('assessment', 'questions'));
    }

    public function submit(Request $request, string $slug)
    {
        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required',
        ]);

        $result = $this->assessmentService->submitAssessment(
            $slug,
            $validated['answers'],
            app()->getLocale()
        );

        if (! ($result['success'] ?? false)) {
            return redirect()
                ->route('assessments.show', $slug)
                ->with('error', $result['message'] ?? __('Failed to submit assessment.'));
        }

        return redirect()
            ->route('assessments.result', [$slug, $result['data']['result_id']])
            ->with('success', __('Assessment completed successfully!'));
    }

    public function result(string $slug, int $id)
    {
        $assessment = $this->assessmentRepo->findBySlug($slug);

        if (! $assessment || ! $assessment->is_active) {
            abort(404);
        }

        $result = AssessmentResult::where('id', $id)
            ->where('assessment_id', $assessment->id)
            ->firstOrFail();

        $this->authorize('view', $result);

        $resultData = $this->assessmentService->getResult(Auth::id(), $slug, $id);

        if (! ($resultData['success'] ?? false)) {
            abort(404);
        }

        return view('assessments.result', compact('assessment', 'result'));
    }

    public function questions(string $slug)
    {
        $questions = $slug === 'agility'
            ? $this->questionService->loadAgilityQuestions(app()->getLocale())
            : $this->questionService->loadQuestions($slug, app()->getLocale());
        $questions = $this->questionService->shuffleQuestions($questions);
        $questions = $this->questionService->formatForFrontend($questions);

        return response()->json($questions);
    }
}
