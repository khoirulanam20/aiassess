<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Web\BaseController;
use App\Models\User;
use App\Models\UserDetail;
use App\Services\ActivityLogService;
use App\Support\OrganizationContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
            ->with('userDetail')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.candidates.index', compact('candidates'));
    }

    public function create(): View
    {
        $this->authorize('createCandidate', User::class);

        return view('admin.candidates.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('createCandidate', User::class);

        $orgId = OrganizationContext::requireOrganizationId(Auth::user());

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'employee_id' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:30',
            'position' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make(Str::random(32)),
            'organization_id' => $orgId,
            'email_verified_at' => now(),
        ]);

        $user->assignRole('candidate');

        UserDetail::create([
            'user_id' => $user->id,
            'employee_id' => $validated['employee_id'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'position' => $validated['position'] ?? null,
            'department' => $validated['department'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'language' => 'id',
        ]);

        $this->activityLog->log('candidate.created', "Candidate {$user->email} created");

        return redirect()->route('admin.candidates.index')
            ->with('success', __('Data kandidat/karyawan berhasil ditambahkan.'));
    }

    public function edit(User $candidate): View
    {
        $this->authorize('update', $candidate);
        abort_unless($candidate->hasRole('candidate'), 404);

        $candidate->load('userDetail');

        return view('admin.candidates.edit', compact('candidate'));
    }

    public function update(Request $request, User $candidate): RedirectResponse
    {
        $this->authorize('update', $candidate);
        abort_unless($candidate->hasRole('candidate'), 404);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$candidate->id,
            'employee_id' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:30',
            'position' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
        ]);

        $candidate->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        $candidate->userDetail()->updateOrCreate(
            ['user_id' => $candidate->id],
            [
                'employee_id' => $validated['employee_id'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'position' => $validated['position'] ?? null,
                'department' => $validated['department'] ?? null,
                'birth_date' => $validated['birth_date'] ?? null,
                'gender' => $validated['gender'] ?? null,
            ]
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
}
