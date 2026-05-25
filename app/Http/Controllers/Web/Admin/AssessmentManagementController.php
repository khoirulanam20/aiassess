<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Web\BaseController;
use App\Models\Assessment;
use App\Models\Organization;
use App\Models\OrganizationAssessment;
use App\Services\ActivityLogService;
use App\Services\QuestionService;
use App\Support\OrganizationContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AssessmentManagementController extends BaseController
{
    public function __construct(
        private readonly ActivityLogService $activityLog,
        private readonly QuestionService $questionService,
    ) {}

    public function indexGlobal(): View
    {
        abort_unless(Auth::user()->can('assessments.manage_global'), 403);

        $assessments = Assessment::orderBy('sort_order')->get();

        return view('admin.assessments.global-index', compact('assessments'));
    }

    public function editGlobal(Assessment $assessment): View
    {
        abort_unless(Auth::user()->can('assessments.manage_global'), 403);

        $questionBundle = $this->questionService->loadQuestionsForAdmin($assessment->slug);
        $showDimension = $assessment->slug === 'papikostick';

        return view('admin.assessments.global-edit', compact('assessment', 'questionBundle', 'showDimension'));
    }

    public function updateGlobal(Request $request, Assessment $assessment): RedirectResponse
    {
        abort_unless(Auth::user()->can('assessments.manage_global'), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:free,premium',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'cooldown_days' => 'required|integer|min:0',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'required|integer|min:0',
            'questions' => 'nullable|array',
            'questions.*.code' => 'nullable|string|max:50',
            'questions.*.question' => 'nullable|string|max:5000',
            'questions.*.options' => 'nullable|array',
            'questions.*.options.*.id' => 'nullable|string|max:10',
            'questions.*.options.*.text' => 'nullable|string|max:2000',
            'questions.*.options.*.dimension' => 'nullable|string|max:20',
            'groups' => 'nullable|array',
            'groups.*.questions' => 'nullable|array',
            'groups.*.questions.*.code' => 'nullable|string|max:50',
            'groups.*.questions.*.question' => 'nullable|string|max:5000',
            'groups.*.questions.*.options' => 'nullable|array',
            'groups.*.questions.*.options.*.id' => 'nullable|string|max:10',
            'groups.*.questions.*.options.*.text' => 'nullable|string|max:2000',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $questionCount = $assessment->question_count;

        if ($assessment->slug === 'agility' && $request->has('groups')) {
            $this->questionService->saveAgilityQuestions('id', $request->input('groups', []));
            $questionCount = $this->questionService->countQuestionsForAdmin($assessment->slug);
        } elseif ($assessment->slug !== 'agility' && $request->has('questions')) {
            $this->questionService->saveQuestionsFile($assessment->slug, 'id', $request->input('questions', []));
            $questionCount = $this->questionService->countQuestionsForAdmin($assessment->slug);
        }

        $assessment->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'instructions' => $validated['instructions'] ?? null,
            'cooldown_days' => $validated['cooldown_days'],
            'is_active' => $validated['is_active'],
            'sort_order' => $validated['sort_order'],
            'question_count' => $questionCount,
        ]);

        $this->activityLog->log('assessment.updated', "Global assessment {$assessment->slug} updated");

        return redirect()->route('admin.system.assessments.index')
            ->with('success', __('Assessment berhasil diperbarui.'));
    }

    public function indexOrg(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->hasRole('superadmin')) {
            return redirect()->route('admin.system.assessments.index');
        }

        $orgId = OrganizationContext::requireOrganizationId($user);
        $organization = Organization::findOrFail($orgId);

        $this->authorize('manageOrg', $organization);

        $orgAssessments = OrganizationAssessment::with('assessment')
            ->where('organization_id', $orgId)
            ->orderBy('assessment_id')
            ->get();

        return view('admin.assessments.org-index', compact('organization', 'orgAssessments'));
    }

    public function editOrg(OrganizationAssessment $orgAssessment): View
    {
        $this->authorize('update', $orgAssessment);

        $orgAssessment->load('assessment', 'organization');

        return view('admin.assessments.org-edit', compact('orgAssessment'));
    }

    public function updateOrg(Request $request, OrganizationAssessment $orgAssessment): RedirectResponse
    {
        $this->authorize('update', $orgAssessment);

        $validated = $request->validate([
            'is_enabled' => 'sometimes|boolean',
            'cooldown_days' => 'nullable|integer|min:0',
            'custom_name' => 'nullable|string|max:255',
            'custom_description' => 'nullable|string',
            'custom_instructions' => 'nullable|string',
        ]);

        $validated['is_enabled'] = $request->boolean('is_enabled');

        $orgAssessment->update($validated);

        $this->activityLog->log('assessment.org_updated', "Org assessment #{$orgAssessment->id} updated");

        return redirect()->route('admin.assessments.org.index')
            ->with('success', __('Konfigurasi assessment perusahaan berhasil diperbarui.'));
    }
}
