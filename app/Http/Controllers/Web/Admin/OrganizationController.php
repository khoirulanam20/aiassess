<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Web\BaseController;
use App\Models\Assessment;
use App\Models\Organization;
use App\Models\OrganizationAssessment;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrganizationController extends BaseController
{
    public function __construct(
        private readonly ActivityLogService $activityLog
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', Organization::class);

        $organizations = Organization::withCount('users')->orderBy('name')->paginate(15);

        return view('admin.organizations.index', compact('organizations'));
    }

    public function create(): View
    {
        $this->authorize('create', Organization::class);

        return view('admin.organizations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Organization::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'industry' => 'nullable|string|max:100',
        ]);

        $slug = Str::slug($validated['name']);
        $base = $slug;
        $i = 1;
        while (Organization::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        $organization = Organization::create([
            ...$validated,
            'slug' => $slug,
            'is_active' => true,
        ]);

        foreach (Assessment::all() as $assessment) {
            OrganizationAssessment::create([
                'organization_id' => $organization->id,
                'assessment_id' => $assessment->id,
                'is_enabled' => $assessment->is_active,
                'cooldown_days' => $assessment->cooldown_days,
            ]);
        }

        $this->activityLog->log('organization.created', "Organization {$organization->name} created");

        return redirect()->route('admin.organizations.index')
            ->with('success', __('Perusahaan berhasil dibuat.'));
    }

    public function edit(Organization $organization): View
    {
        $this->authorize('update', $organization);

        return view('admin.organizations.edit', compact('organization'));
    }

    public function update(Request $request, Organization $organization): RedirectResponse
    {
        $this->authorize('update', $organization);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'industry' => 'nullable|string|max:100',
            'is_active' => 'sometimes|boolean',
        ]);

        $organization->update($validated);

        $this->activityLog->log('organization.updated', "Organization {$organization->name} updated");

        return redirect()->route('admin.organizations.index')
            ->with('success', __('Perusahaan berhasil diperbarui.'));
    }

    public function destroy(Organization $organization): RedirectResponse
    {
        $this->authorize('delete', $organization);

        $organization->delete();

        return redirect()->route('admin.organizations.index')
            ->with('success', __('Perusahaan berhasil dihapus.'));
    }
}
