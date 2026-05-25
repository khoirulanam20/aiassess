<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Web\BaseController;
use App\Models\Organization;
use App\Models\User;
use App\Models\UserDetail;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class HrUserController extends BaseController
{
    public function __construct(
        private readonly ActivityLogService $activityLog
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        $hrUsers = User::role('admin')
            ->with('organization')
            ->orderBy('name')
            ->paginate(15);

        $organizations = Organization::orderBy('name')->get();

        return view('admin.hr-users.index', compact('hrUsers', 'organizations'));
    }

    public function create(): View
    {
        $this->authorize('createHr', User::class);

        $organizations = Organization::where('is_active', true)->orderBy('name')->get();

        return view('admin.hr-users.create', compact('organizations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('createHr', User::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'organization_id' => 'required|exists:organizations,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'organization_id' => $validated['organization_id'],
            'email_verified_at' => now(),
        ]);

        $user->assignRole('admin');

        UserDetail::create([
            'user_id' => $user->id,
            'language' => 'id',
        ]);

        $this->activityLog->log('user.created', "HR user {$user->email} created for org #{$validated['organization_id']}");

        return redirect()->route('admin.hr-users.index')
            ->with('success', __('Akun HR/Admin berhasil dibuat.'));
    }

    public function edit(User $hrUser): View
    {
        $this->authorize('update', $hrUser);
        abort_unless($hrUser->hasRole('admin'), 404);

        $organizations = Organization::where('is_active', true)->orderBy('name')->get();

        return view('admin.hr-users.edit', compact('hrUser', 'organizations'));
    }

    public function update(Request $request, User $hrUser): RedirectResponse
    {
        $this->authorize('update', $hrUser);
        abort_unless($hrUser->hasRole('admin'), 404);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$hrUser->id,
            'organization_id' => 'required|exists:organizations,id',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $hrUser->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'organization_id' => $validated['organization_id'],
        ]);

        if (! empty($validated['password'])) {
            $hrUser->update(['password' => Hash::make($validated['password'])]);
        }

        return redirect()->route('admin.hr-users.index')
            ->with('success', __('Akun HR/Admin berhasil diperbarui.'));
    }
}
