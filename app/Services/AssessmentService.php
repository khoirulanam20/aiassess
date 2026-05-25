<?php

namespace App\Services;

use App\Models\AssessmentResult;
use App\Repositories\Contracts\AssessmentRepositoryInterface;
use App\Repositories\Contracts\AssessmentResultRepositoryInterface;
use App\Services\Scorers\DiscScorerService;
use App\Services\Scorers\MbtiScorerService;
use App\Services\Scorers\AgilityScorerService;
use App\Services\Scorers\BusinessInsightScorerService;
use App\Services\Scorers\MsdtScorerService;
use App\Services\Scorers\PapiKostickScorerService;
use App\Services\Scorers\SpmScorerService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssessmentService extends BaseService
{
    public function __construct(
        private readonly AssessmentRepositoryInterface $assessmentRepo,
        private readonly AssessmentResultRepositoryInterface $resultRepo,
        private readonly QuestionService $questionService,
        private readonly ShareService $shareService,
        private readonly ActivityLogService $activityLog,
        private readonly MbtiScorerService $mbtiScorer,
        private readonly DiscScorerService $discScorer,
        private readonly PapiKostickScorerService $papiKostickScorer,
        private readonly MsdtScorerService $msdtScorer,
        private readonly SpmScorerService $spmScorer,
        private readonly BusinessInsightScorerService $businessInsightScorer,
        private readonly AgilityScorerService $agilityScorer,
        private readonly AssessmentBatchService $batchService,
    ) {}

    public function getAvailableAssessments(): array
    {
        $assessments = $this->assessmentRepo->getActiveAssessments();
        $userId = Auth::id();

        return $assessments->map(function ($assessment) use ($userId) {
            $latestResult = $this->resultRepo->getLatestResult($userId, $assessment->id);

            return [
                'id' => $assessment->id,
                'slug' => $assessment->slug,
                'name' => $assessment->name,
                'type' => $assessment->type,
                'description' => $assessment->description,
                'question_count' => $assessment->question_count,
                'cooldown_days' => $assessment->cooldown_days,
                'color' => $assessment->color,
                'has_completed' => ! is_null($latestResult),
                'can_retake' => is_null($latestResult) || $latestResult->created_at->addDays($assessment->cooldown_days)->isPast(),
            ];
        })->toArray();
    }

    public function canTakeAssessment(int $userId, int $assessmentId, ?int $batchId = null): array
    {
        $assessment = $this->assessmentRepo->findById($assessmentId);

        if (! $assessment || ! $assessment->is_active) {
            return $this->errorResponse('Assessment not found or inactive', code: 404);
        }

        if ($batchId !== null) {
            $assignment = \App\Models\AssessmentBatchAssignment::query()
                ->where('assessment_batch_id', $batchId)
                ->where('user_id', $userId)
                ->where('assessment_id', $assessmentId)
                ->first();

            if (! $assignment) {
                return $this->errorResponse(__('Assessment tidak ditugaskan dalam batch ini.'), code: 403);
            }

            if ($assignment->status === \App\Models\AssessmentBatchAssignment::STATUS_COMPLETED) {
                return $this->errorResponse(__('Assessment ini sudah selesai dikerjakan.'), code: 403);
            }

            return $this->successResponse([
                'can_take' => true,
                'assessment' => $assessment,
            ]);
        }

        $latestResult = $this->resultRepo->getLatestResult($userId, $assessmentId);

        if ($latestResult && $latestResult->created_at->addDays($assessment->cooldown_days)->isFuture()) {
            $nextAvailable = $latestResult->created_at->addDays($assessment->cooldown_days);

            return $this->errorResponse(
                'You have already taken this assessment',
                data: [
                    'error_type' => 'HAS_DONE_TEST',
                    'next_available_at' => $nextAvailable->toIso8601String(),
                ],
                code: 400,
            );
        }

        return $this->successResponse([
            'can_take' => true,
            'assessment' => $assessment,
        ]);
    }

    public function getQuestions(string $slug, string $locale = 'id'): array
    {
        $assessment = $this->assessmentRepo->findBySlug($slug);

        if (! $assessment || ! $assessment->is_active) {
            return $this->errorResponse('Assessment not found', code: 404);
        }

        $questions = $this->questionService->loadQuestions($slug, $locale);

        if (empty($questions)) {
            return $this->successResponse([
                'assessment' => $assessment->only(['slug', 'name', 'question_count']),
                'questions' => [],
                'total_questions' => 0,
            ]);
        }

        $shuffled = $this->questionService->shuffleQuestions($questions);

        return $this->successResponse([
            'assessment' => $assessment->only(['slug', 'name', 'question_count']),
            'questions' => $this->questionService->formatForFrontend($shuffled),
            'total_questions' => $assessment->question_count,
        ]);
    }

    public function submitAssessment(string $slug, array $answers, string $locale = 'id', ?int $userId = null, ?int $batchId = null): array
    {
        $assessment = $this->assessmentRepo->findBySlug($slug);

        if (! $assessment || ! $assessment->is_active) {
            return $this->errorResponse('Assessment not found', code: 404);
        }

        $userId = $userId ?? Auth::id();

        if (! $userId) {
            return $this->errorResponse('Unauthorized', code: 401);
        }

        $check = $this->canTakeAssessment($userId, $assessment->id, $batchId);

        if (! ($check['success'] ?? false)) {
            return $check;
        }

        $result = DB::transaction(function () use ($userId, $assessment, $answers, $locale, $batchId) {
            $scores = [];
            $finalResult = [];

            if ($assessment->slug === 'mbti') {
                $scored = $this->mbtiScorer->calculate($answers, $locale);
                $scores = $scored['scores'];
                $finalResult = [
                    'type' => $scored['type'],
                    'type_name' => $scored['type_name'],
                    'description' => $scored['description'],
                    'strengths' => $scored['strengths'],
                    'weaknesses' => $scored['weaknesses'],
                    'career_paths' => $scored['career_paths'],
                ];
            } elseif ($assessment->slug === 'disc') {
                $scored = $this->discScorer->calculate($answers, $locale);
                $scores = $scored['scores'];
                $finalResult = [
                    'type' => $scored['primary_type'],
                    'type_name' => $scored['type_name'],
                    'description' => $scored['description'],
                    'strengths' => $scored['strengths'],
                    'weaknesses' => $scored['weaknesses'],
                    'communication_style' => $scored['communication_style'],
                    'motivations' => $scored['motivations'],
                    'fears' => $scored['fears'],
                    'career_paths' => $scored['career_paths'],
                ];
            } elseif ($assessment->slug === 'papikostick') {
                $scored = $this->papiKostickScorer->calculate($answers, $locale);
                $scores = $scored['scores'];
                $finalResult = [
                    'type' => 'papikostick',
                    'scores' => $scored['scores'],
                    'dimensions' => $scored['dimensions'],
                    'profile_graph' => $scored['profile_graph'],
                    'highest_dimension' => $scored['highest_dimension'],
                    'lowest_dimension' => $scored['lowest_dimension'],
                ];
            } elseif ($assessment->slug === 'msdt') {
                $scored = $this->msdtScorer->calculate($answers, $locale);
                $scores = $scored['averages'];
                $finalResult = [
                    'type' => 'msdt',
                    'scores' => $scored['scores'],
                    'averages' => $scored['averages'],
                    'overall' => $scored['overall'],
                    'highest_dimension' => $scored['highest_dimension'],
                    'lowest_dimension' => $scored['lowest_dimension'],
                ];
            } elseif ($assessment->slug === 'spm') {
                $scored = $this->spmScorer->calculate($answers, $locale);
                $scores = $scored['scores'];
                $finalResult = [
                    'type' => 'spm',
                    'total_correct' => $scored['scores']['total_correct'],
                    'percentage' => $scored['scores']['percentage'],
                    'set_scores' => $scored['set_scores'],
                    'iq' => $scored['iq'],
                    'percentile' => $scored['percentile'],
                    'level' => $scored['level'],
                    'level_title' => $scored['level_title'],
                    'description' => $scored['description'],
                    'advice' => $scored['advice'],
                ];
            } elseif ($assessment->slug === 'business-insight') {
                $scored = $this->businessInsightScorer->calculate($answers, $locale);
                $scores = $scored['scores'];
                $finalResult = [
                    'type' => 'business-insight',
                    'scores' => $scored['scores'],
                    'overall' => $scored['overall'],
                    'raw_scores' => $scored['raw_scores'],
                    'highest_dimension' => $scored['highest_dimension'],
                    'lowest_dimension' => $scored['lowest_dimension'],
                ];
            } elseif ($assessment->slug === 'agility') {
                $scored = $this->agilityScorer->calculate($answers, $locale);
                $scores = $scored['sub_tests'];
                $finalResult = [
                    'type' => 'agility',
                    'sub_tests' => $scored['sub_tests'],
                    'dimensions' => $scored['dimensions'],
                    'overall' => $scored['overall'],
                    'highest_sub_test' => $scored['highest_sub_test'],
                    'lowest_sub_test' => $scored['lowest_sub_test'],
                ];
            }

            $result = AssessmentResult::create([
                'user_id' => $userId,
                'assessment_id' => $assessment->id,
                'test_name' => $assessment->slug,
                'result' => json_encode($finalResult),
                'scores' => json_encode($scores),
                'answers' => json_encode($answers),
                'status' => 'completed',
            ]);

            $this->shareService->createShareLink($result);
            $this->activityLog->logAssessmentComplete($assessment->name);
            $this->batchService->markAssignmentCompleted($userId, $assessment->id, $result, $batchId);

            return $result;
        });

        return $this->successResponse([
            'result_id' => $result->id,
            'share_token' => $result->share_token,
        ], 'Assessment completed successfully');
    }

    public function getResult(int $userId, string $slug, int $resultId): array
    {
        $assessment = $this->assessmentRepo->findBySlug($slug);

        if (! $assessment) {
            return $this->errorResponse('Assessment not found', code: 404);
        }

        $result = AssessmentResult::where('id', $resultId)
            ->where('user_id', $userId)
            ->where('assessment_id', $assessment->id)
            ->first();

        if (! $result) {
            return $this->errorResponse('Result not found', code: 404);
        }

        return $this->successResponse([
            'id' => $result->id,
            'assessment' => $assessment->only(['slug', 'name']),
            'result' => json_decode($result->result, true),
            'scores' => json_decode($result->scores, true),
            'share_token' => $result->share_token,
            'completed_at' => $result->created_at->toIso8601String(),
        ]);
    }
}
