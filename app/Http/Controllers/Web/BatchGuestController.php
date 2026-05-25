<?php

namespace App\Http\Controllers\Web;

use App\Http\Middleware\BatchGuestMiddleware;
use App\Models\Assessment;
use App\Models\AssessmentBatch;
use App\Models\AssessmentBatchAssignment;
use App\Models\AssessmentBatchShare;
use App\Models\AssessmentResult;
use App\Repositories\Contracts\AssessmentRepositoryInterface;
use App\Services\ActivityLogService;
use App\Services\AssessmentBatchService;
use App\Services\AssessmentService;
use App\Services\BatchGuestShareService;
use App\Services\QuestionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\View\View;

class BatchGuestController extends BaseController
{
    public function __construct(
        private readonly ActivityLogService $activityLog,
        private readonly AssessmentRepositoryInterface $assessmentRepo,
        private readonly AssessmentService $assessmentService,
        private readonly AssessmentBatchService $batchService,
        private readonly BatchGuestShareService $batchGuestShare,
        private readonly QuestionService $questionService,
    ) {}

    public function showEntry(): View
    {
        return view('shared.batch-guest-form', [
            'batch' => null,
            'verifyUrl' => route('shared.batch.entry.verify'),
        ]);
    }

    public function showForm(string $token): View
    {
        $batch = $this->batchGuestShare->resolveBatchByGuestToken($token);

        if (! $batch) {
            abort(404);
        }

        return view('shared.batch-guest-form', [
            'batch' => $batch,
            'verifyUrl' => route('shared.batch.verify', $token),
        ]);
    }

    public function verifyEntry(Request $request): RedirectResponse
    {
        return $this->processVerify($request, null);
    }

    public function verify(Request $request, string $token): RedirectResponse
    {
        $batch = $this->batchGuestShare->resolveBatchByGuestToken($token);

        if (! $batch) {
            return back()->withErrors(['access_code' => __('Link tidak valid.')]);
        }

        return $this->processVerify($request, $batch);
    }

    public function portal(Request $request, string $token): View
    {
        $share = $this->shareFromRequest($request);

        $this->batchService->reconcilePendingAssignments(
            $share->assessment_batch_id,
            $share->user_id
        );

        $assignments = AssessmentBatchAssignment::query()
            ->with('assessment')
            ->where('assessment_batch_id', $share->assessment_batch_id)
            ->where('user_id', $share->user_id)
            ->orderBy('assessment_id')
            ->get();

        $pending = $assignments->where('status', AssessmentBatchAssignment::STATUS_PENDING);
        $completed = $assignments->where('status', AssessmentBatchAssignment::STATUS_COMPLETED);

        return view('shared.batch-portal', compact('share', 'pending', 'completed', 'token'));
    }

    public function take(Request $request, string $token, string $slug): View|RedirectResponse
    {
        $share = $this->shareFromRequest($request);
        $assessment = $this->resolveAssessment($slug);

        $check = $this->assessmentService->canTakeAssessment(
            $share->user_id,
            $assessment->id,
            $share->assessment_batch_id
        );

        if (! ($check['success'] ?? false)) {
            return redirect()
                ->route('shared.batch.portal', $token)
                ->with('error', $check['message'] ?? __('Anda tidak dapat mengerjakan assessment ini.'));
        }

        $questions = $slug === 'agility'
            ? $this->questionService->loadAgilityQuestions(app()->getLocale())
            : $this->questionService->loadQuestions($slug, app()->getLocale());
        $questions = $this->questionService->shuffleQuestions($questions);
        $questions = $this->questionService->formatForFrontend($questions);

        return view('shared.batch-take', [
            'share' => $share,
            'assessment' => $assessment,
            'questions' => $questions,
            'token' => $token,
        ]);
    }

    public function submit(Request $request, string $token, string $slug): RedirectResponse
    {
        $share = $this->shareFromRequest($request);

        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required',
        ]);

        $result = $this->assessmentService->submitAssessment(
            $slug,
            $validated['answers'],
            app()->getLocale(),
            $share->user_id,
            $share->assessment_batch_id
        );

        if (! ($result['success'] ?? false)) {
            return redirect()
                ->route('shared.batch.portal', $token)
                ->with('error', $result['message'] ?? __('Gagal mengirim jawaban.'));
        }

        return redirect()
            ->route('shared.batch.result', [$token, $slug, $result['data']['result_id']])
            ->with('success', __('Assessment berhasil diselesaikan!'));
    }

    public function result(Request $request, string $token, string $slug, int $id): View|RedirectResponse
    {
        $share = $this->shareFromRequest($request);
        $assessment = $this->resolveAssessment($slug);

        $result = AssessmentResult::where('id', $id)
            ->where('user_id', $share->user_id)
            ->where('assessment_id', $assessment->id)
            ->firstOrFail();

        $resultData = $this->assessmentService->getResult($share->user_id, $slug, $id);

        if (! ($resultData['success'] ?? false)) {
            abort(404);
        }

        return view('shared.batch-result', [
            'share' => $share,
            'assessment' => $assessment,
            'result' => $result,
            'token' => $token,
        ]);
    }

    private function processVerify(Request $request, ?AssessmentBatch $batch): RedirectResponse
    {
        $request->validate([
            'access_code' => ['required', 'string', 'min:4', 'max:16'],
        ]);

        $share = $this->batchGuestShare->findActiveShareByAccessCode($request->access_code, $batch);

        if (! $share) {
            return back()->withErrors([
                'access_code' => __('Kode akses tidak valid.'),
            ]);
        }

        if ($share->isLocked()) {
            $minutes = now()->diffInMinutes($share->locked_until);

            return back()->withErrors([
                'access_code' => __('Terlalu banyak percobaan. Coba lagi dalam :minutes menit.', ['minutes' => $minutes]),
            ]);
        }

        $share->update([
            'failed_attempts' => 0,
            'locked_until' => null,
        ]);

        $share->increment('view_count');

        $token = $share->batch->guest_share_token;
        $encrypted = Crypt::encrypt(BatchGuestMiddleware::buildCookiePayload($share));

        $this->activityLog->log('batch_share.verified', "Guest verified batch share for user #{$share->user_id}", [
            'share_id' => $share->id,
            'ip' => $request->ip(),
        ]);

        return redirect()->route('shared.batch.portal', $token)
            ->cookie(
                BatchGuestMiddleware::cookieName(),
                $encrypted,
                BatchGuestMiddleware::sessionTtl(),
                '/',
                null,
                true,
                true,
                false,
                'lax'
            );
    }

    private function shareFromRequest(Request $request): AssessmentBatchShare
    {
        /** @var AssessmentBatchShare $share */
        $share = $request->attributes->get('guest_batch_share');
        $share->load('batch', 'user');

        return $share;
    }

    private function resolveAssessment(string $slug): Assessment
    {
        $assessment = $this->assessmentRepo->findBySlug($slug);

        if (! $assessment || ! $assessment->is_active) {
            abort(404);
        }

        return $assessment;
    }
}
