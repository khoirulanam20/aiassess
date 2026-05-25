<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Web\BaseController;
use App\Models\Department;
use App\Support\OrganizationContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DepartmentController extends BaseController
{
    public function index(): View
    {
        $this->authorize('viewAny', Department::class);

        $orgId = OrganizationContext::requireOrganizationId(Auth::user());

        $departments = Department::query()
            ->where('organization_id', $orgId)
            ->withCount(['positions', 'userDetails'])
            ->orderBy('name')
            ->paginate(15);

        return view('admin.master-data.departments.index', compact('departments'));
    }

    public function create(): View
    {
        $this->authorize('create', Department::class);

        return view('admin.master-data.departments.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Department::class);

        $orgId = OrganizationContext::requireOrganizationId(Auth::user());

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('departments', 'name')->where('organization_id', $orgId),
            ],
            'code' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        Department::create([
            'organization_id' => $orgId,
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.master-data.departments.index')
            ->with('success', __('Departemen berhasil ditambahkan.'));
    }

    public function edit(Department $department): View
    {
        $this->authorize('update', $department);

        return view('admin.master-data.departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        $this->authorize('update', $department);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('departments', 'name')
                    ->where('organization_id', $department->organization_id)
                    ->ignore($department->id),
            ],
            'code' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $department->update([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.master-data.departments.index')
            ->with('success', __('Departemen berhasil diperbarui.'));
    }

    public function destroy(Department $department): RedirectResponse
    {
        $this->authorize('delete', $department);

        if ($department->userDetails()->exists()) {
            return back()->with('error', __('Departemen masih digunakan oleh kandidat/karyawan.'));
        }

        if ($department->positions()->exists()) {
            return back()->with('error', __('Hapus atau pindahkan posisi di departemen ini terlebih dahulu.'));
        }

        $department->delete();

        return redirect()
            ->route('admin.master-data.departments.index')
            ->with('success', __('Departemen berhasil dihapus.'));
    }
}
