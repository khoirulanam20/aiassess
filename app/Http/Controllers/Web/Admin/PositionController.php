<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Web\BaseController;
use App\Models\Department;
use App\Models\Position;
use App\Support\OrganizationContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PositionController extends BaseController
{
    public function index(): View
    {
        $this->authorize('viewAny', Position::class);

        $orgId = OrganizationContext::requireOrganizationId(Auth::user());

        $positions = Position::query()
            ->where('organization_id', $orgId)
            ->with('department')
            ->withCount('userDetails')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.master-data.positions.index', compact('positions'));
    }

    public function create(): View
    {
        $this->authorize('create', Position::class);

        $departments = $this->activeDepartments();

        return view('admin.master-data.positions.create', compact('departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Position::class);

        $orgId = OrganizationContext::requireOrganizationId(Auth::user());

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('positions', 'name')->where('organization_id', $orgId),
            ],
            'code' => 'nullable|string|max:50',
            'department_id' => [
                'nullable',
                Rule::exists('departments', 'id')->where('organization_id', $orgId),
            ],
            'is_active' => 'boolean',
        ]);

        Position::create([
            'organization_id' => $orgId,
            'department_id' => $validated['department_id'] ?? null,
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.master-data.positions.index')
            ->with('success', __('Posisi berhasil ditambahkan.'));
    }

    public function edit(Position $position): View
    {
        $this->authorize('update', $position);

        $departments = $this->activeDepartments($position->organization_id);

        return view('admin.master-data.positions.edit', compact('position', 'departments'));
    }

    public function update(Request $request, Position $position): RedirectResponse
    {
        $this->authorize('update', $position);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('positions', 'name')
                    ->where('organization_id', $position->organization_id)
                    ->ignore($position->id),
            ],
            'code' => 'nullable|string|max:50',
            'department_id' => [
                'nullable',
                Rule::exists('departments', 'id')->where('organization_id', $position->organization_id),
            ],
            'is_active' => 'boolean',
        ]);

        $position->update([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'department_id' => $validated['department_id'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.master-data.positions.index')
            ->with('success', __('Posisi berhasil diperbarui.'));
    }

    public function destroy(Position $position): RedirectResponse
    {
        $this->authorize('delete', $position);

        if ($position->userDetails()->exists()) {
            return back()->with('error', __('Posisi masih digunakan oleh kandidat/karyawan.'));
        }

        $position->delete();

        return redirect()
            ->route('admin.master-data.positions.index')
            ->with('success', __('Posisi berhasil dihapus.'));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Department>
     */
    private function activeDepartments(?int $orgId = null)
    {
        $orgId ??= OrganizationContext::requireOrganizationId(Auth::user());

        return Department::query()
            ->where('organization_id', $orgId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}
