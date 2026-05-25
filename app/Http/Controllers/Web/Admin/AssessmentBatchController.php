<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Web\BaseController;
use App\Models\AssessmentBatch;
use App\Models\AssessmentBatchAssignment;
use App\Models\AssessmentBatchShare;
use App\Models\AssessmentResult;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\AssessmentBatchService;
use App\Support\OrganizationContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AssessmentBatchController extends BaseController
{
    public function __construct(
        private readonly ActivityLogService $activityLog,
        private readonly AssessmentBatchService $batchService,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', AssessmentBatch::class);

        $orgId = OrganizationContext::requireOrganizationId(Auth::user());

        $batches = AssessmentBatch::query()
            ->where('organization_id', $orgId)
            ->with('creator')
            ->withCount([
                'assignments',
                'assignments as completed_count' => fn ($q) => $q->where('status', 'completed'),
            ])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.batches.index', compact('batches'));
    }

    public function create(): View
    {
        $this->authorize('create', AssessmentBatch::class);

        $orgId = OrganizationContext::requireOrganizationId(Auth::user());

        $assessments = $this->batchService->enabledAssessmentsForOrganization($orgId);
        $candidates = User::role('candidate')
            ->where('organization_id', $orgId)
            ->with('userDetail')
            ->orderBy('name')
            ->get();

        return view('admin.batches.create', compact('assessments', 'candidates'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', AssessmentBatch::class);

        $orgId = OrganizationContext::requireOrganizationId(Auth::user());

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'assessment_ids' => 'required|array|min:1',
            'assessment_ids.*' => 'integer|exists:assessments,id',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        try {
            $batch = $this->batchService->createBatch(
                $orgId,
                Auth::id(),
                $validated['name'],
                $validated['description'] ?? null,
                $validated['assessment_ids'],
                $validated['user_ids'],
                $validated['starts_at'] ?? null,
                $validated['ends_at'] ?? null,
            );
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $this->activityLog->log('batch.created', "Batch #{$batch->id} {$batch->name} created");

        return redirect()
            ->route('admin.batches.show', $batch)
            ->with('success', __('Batch assessment berhasil dibuat.'));
    }

    public function show(AssessmentBatch $batch): View
    {
        $this->authorize('view', $batch);

        $batch->load([
            'creator',
            'assignments.user.userDetail',
            'assignments.assessment',
        ]);

        $assessmentSummary = $batch->assignments
            ->groupBy('assessment_id')
            ->map(fn ($items) => [
                'assessment' => $items->first()->assessment,
                'total' => $items->count(),
                'completed' => $items->where('status', 'completed')->count(),
            ])
            ->values();

        $shares = AssessmentBatchShare::query()
            ->where('assessment_batch_id', $batch->id)
            ->get()
            ->keyBy('user_id');

        $candidates = $batch->assignments
            ->groupBy('user_id')
            ->map(function ($assignments) use ($shares) {
                $user = $assignments->first()->user;

                return [
                    'user' => $user,
                    'total' => $assignments->count(),
                    'completed' => $assignments->where('status', 'completed')->count(),
                    'share' => $shares->get($user->id),
                ];
            })
            ->sortBy(fn ($row) => $row['user']->name)
            ->values();

        return view('admin.batches.show', compact('batch', 'assessmentSummary', 'candidates'));
    }

    public function showCandidate(AssessmentBatch $batch, User $candidate): View
    {
        $this->authorize('view', $batch);
        abort_unless($candidate->hasRole('candidate'), 404);
        abort_unless(
            $batch->assignments()->where('user_id', $candidate->id)->exists(),
            404
        );
        abort_unless($candidate->organization_id === $batch->organization_id, 404);

        $assignments = AssessmentBatchAssignment::query()
            ->with(['assessment', 'result'])
            ->where('assessment_batch_id', $batch->id)
            ->where('user_id', $candidate->id)
            ->orderBy('assessment_id')
            ->get();

        return view('admin.batches.candidate', compact('batch', 'candidate', 'assignments'));
    }

    public function showCandidateResult(AssessmentBatch $batch, User $candidate, AssessmentResult $result): View
    {
        $this->authorize('view', $batch);
        $this->authorize('view', $result);

        abort_unless($candidate->hasRole('candidate'), 404);
        abort_unless($candidate->organization_id === $batch->organization_id, 404);
        abort_unless(
            AssessmentBatchAssignment::query()
                ->where('assessment_batch_id', $batch->id)
                ->where('user_id', $candidate->id)
                ->where('assessment_result_id', $result->id)
                ->exists(),
            404
        );

        $result->load(['user.userDetail', 'assessment']);

        return view('admin.results.show', compact('result', 'batch', 'candidate'));
    }

    public function shareCandidate(Request $request, AssessmentBatch $batch, User $candidate): RedirectResponse
    {
        $this->authorize('view', $batch);
        abort_unless($request->user()->can('results.share_create'), 403);
        abort_unless(
            $batch->assignments()->where('user_id', $candidate->id)->exists(),
            404
        );

        if (! $batch->guest_share_token) {
            $batch->update(['guest_share_token' => Str::random(48)]);
            $batch->refresh();
        }

        $accessCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $share = AssessmentBatchShare::updateOrCreate(
            [
                'assessment_batch_id' => $batch->id,
                'user_id' => $candidate->id,
            ],
            [
                'created_by' => Auth::id(),
                'access_code_hash' => Hash::make($accessCode, ['cost' => 12]),
                'is_active' => true,
                'failed_attempts' => 0,
                'locked_until' => null,
                'revoked_at' => null,
                'revoked_by' => null,
            ]
        );

        $this->activityLog->log('batch_share.created', "Batch share for user #{$candidate->id} batch #{$batch->id}", [
            'share_id' => $share->id,
        ]);

        return redirect()
            ->route('admin.batches.show', $batch)
            ->with([
                'share_created' => true,
                'share_url' => route('shared.batch.entry'),
                'access_code' => $accessCode,
                'share_candidate_name' => $candidate->name,
            ]);
    }

    public function close(AssessmentBatch $batch): RedirectResponse
    {
        $this->authorize('update', $batch);

        $batch->update(['status' => AssessmentBatch::STATUS_CLOSED]);

        return redirect()
            ->route('admin.batches.show', $batch)
            ->with('success', __('Batch ditutup.'));
    }
}
