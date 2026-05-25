<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Web\BaseController;
use App\Models\Organization;
use App\Services\ActivityLogService;
use App\Support\OrganizationContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CompanyController extends BaseController
{
    public function __construct(
        private readonly ActivityLogService $activityLog
    ) {}

    public function edit(): View
    {
        $organization = $this->resolveCompany();

        $this->authorize('update', $organization);

        return view('admin.company.edit', compact('organization'));
    }

    public function update(Request $request): RedirectResponse
    {
        $organization = $this->resolveCompany();

        $this->authorize('update', $organization);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'industry' => 'nullable|string|max:100',
        ]);

        $organization->update($validated);

        $this->activityLog->log('organization.updated', "Company profile {$organization->name} updated");

        return redirect()->route('admin.company.edit')
            ->with('success', __('Profil perusahaan berhasil diperbarui.'));
    }

    private function resolveCompany(): Organization
    {
        $orgId = OrganizationContext::requireOrganizationId(Auth::user());

        return Organization::findOrFail($orgId);
    }
}
