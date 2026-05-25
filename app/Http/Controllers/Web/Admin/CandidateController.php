<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Web\BaseController;
use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use App\Models\UserDetail;
use App\Services\ActivityLogService;
use App\Support\OrganizationContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CandidateController extends BaseController
{
    public function __construct(
        private readonly ActivityLogService $activityLog
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        $orgId = OrganizationContext::requireOrganizationId(Auth::user());

        $candidates = User::role('candidate')
            ->where('organization_id', $orgId)
            ->with(['userDetail.departmentEntity', 'userDetail.positionEntity'])
            ->orderBy('name')
            ->paginate(15);

        return view('admin.candidates.index', compact('candidates'));
    }

    public function create(): View
    {
        $this->authorize('createCandidate', User::class);

        return view('admin.candidates.create', $this->masterDataFormContext($orgId = OrganizationContext::requireOrganizationId(Auth::user())));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('createCandidate', User::class);

        $orgId = OrganizationContext::requireOrganizationId(Auth::user());

        $validated = $request->validate($this->candidateRules($orgId));
        $this->assertPositionBelongsToDepartment($validated, $orgId);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make(Str::random(32)),
            'organization_id' => $orgId,
            'email_verified_at' => now(),
        ]);

        $user->assignRole('candidate');

        UserDetail::create(array_merge(
            ['user_id' => $user->id, 'language' => 'id'],
            $this->detailPayload($validated, $orgId)
        ));

        $this->activityLog->log('candidate.created', "Candidate {$user->email} created");

        return redirect()->route('admin.candidates.index')
            ->with('success', __('Data kandidat/karyawan berhasil ditambahkan.'));
    }

    public function edit(User $candidate): View
    {
        $this->authorize('update', $candidate);
        abort_unless($candidate->hasRole('candidate'), 404);

        $candidate->load('userDetail');

        $orgId = OrganizationContext::requireOrganizationId(Auth::user());

        return view('admin.candidates.edit', array_merge(
            compact('candidate'),
            $this->masterDataFormContext($orgId)
        ));
    }

    public function update(Request $request, User $candidate): RedirectResponse
    {
        $this->authorize('update', $candidate);
        abort_unless($candidate->hasRole('candidate'), 404);

        $orgId = OrganizationContext::requireOrganizationId(Auth::user());

        $validated = $request->validate($this->candidateRules($orgId, $candidate->id));
        $this->assertPositionBelongsToDepartment($validated, $orgId);

        $candidate->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        $candidate->userDetail()->updateOrCreate(
            ['user_id' => $candidate->id],
            $this->detailPayload($validated, $orgId)
        );

        return redirect()->route('admin.candidates.index')
            ->with('success', __('Data kandidat/karyawan berhasil diperbarui.'));
    }

    public function destroy(User $candidate): RedirectResponse
    {
        $this->authorize('delete', $candidate);
        abort_unless($candidate->hasRole('candidate'), 404);

        $candidate->delete();

        return redirect()->route('admin.candidates.index')
            ->with('success', __('Data kandidat/karyawan berhasil dihapus.'));
    }

    /**
     * @return array<string, mixed>
     */
    private function candidateRules(int $orgId, ?int $userId = null): array
    {
        $emailRule = 'required|email|unique:users,email';
        if ($userId) {
            $emailRule .= ','.$userId;
        }

        return [
            'name' => 'required|string|max:255',
            'email' => $emailRule,
            'employee_id' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:30',
            'department_id' => [
                'nullable',
                Rule::exists('departments', 'id')->where(function ($query) use ($orgId) {
                    $query->where('organization_id', $orgId)->where('is_active', true);
                }),
            ],
            'position_id' => [
                'nullable',
                Rule::exists('positions', 'id')->where(function ($query) use ($orgId) {
                    $query->where('organization_id', $orgId)->where('is_active', true);
                }),
            ],
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
        ];
    }

    /**
     * @return array{departments: \Illuminate\Database\Eloquent\Collection, positions: \Illuminate\Database\Eloquent\Collection}
     */
    private function masterDataFormContext(int $orgId): array
    {
        return [
            'departments' => Department::query()
                ->where('organization_id', $orgId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
            'positions' => Position::query()
                ->where('organization_id', $orgId)
                ->where('is_active', true)
                ->with('department')
                ->orderBy('name')
                ->get(),
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function detailPayload(array $validated, int $orgId): array
    {
        $department = ! empty($validated['department_id'])
            ? Department::query()->where('organization_id', $orgId)->find($validated['department_id'])
            : null;

        $position = ! empty($validated['position_id'])
            ? Position::query()->where('organization_id', $orgId)->find($validated['position_id'])
            : null;

        return [
            'employee_id' => $validated['employee_id'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'department_id' => $department?->id,
            'department' => $department?->name,
            'position_id' => $position?->id,
            'position' => $position?->name,
            'birth_date' => $validated['birth_date'] ?? null,
            'gender' => $validated['gender'] ?? null,
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function assertPositionBelongsToDepartment(array $validated, int $orgId): void
    {
        if (empty($validated['position_id'])) {
            return;
        }

        if (empty($validated['department_id'])) {
            throw ValidationException::withMessages([
                'position_id' => __('Pilih departemen sebelum memilih posisi.'),
            ]);
        }

        $position = Position::query()
            ->where('organization_id', $orgId)
            ->where('is_active', true)
            ->find($validated['position_id']);

        if (! $position || (int) $position->department_id !== (int) $validated['department_id']) {
            throw ValidationException::withMessages([
                'position_id' => __('Posisi harus sesuai dengan departemen yang dipilih.'),
            ]);
        }
    }
}
