<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\ActivityLogService;
use App\Services\ProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private readonly ProfileService $profileService,
        private readonly ActivityLogService $activityLog
    ) {}

    public function edit(Request $request): View
    {
        $user = $request->user()->load('userDetail');
        $activities = $this->activityLog->getRecentActivity(15);

        return view('profile.edit', compact('user', 'activities'));
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $oldData = [
            'name' => $request->user()->name,
            'email' => $request->user()->email,
        ];

        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        $this->profileService->updateDetails($request->user(), $request->only([
            'phone', 'bio', 'language', 'company', 'position',
        ]));

        $changes = [];
        foreach ($request->validated() as $key => $value) {
            if (isset($oldData[$key]) && $oldData[$key] !== $value) {
                $changes[$key] = $oldData[$key].' -> '.$value;
            }
        }

        if (! empty($changes)) {
            $this->activityLog->logProfileUpdate($changes);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function avatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $this->profileService->uploadAvatar($request->user(), $request->file('avatar'));

        return Redirect::route('profile.edit')->with('status', 'avatar-updated');
    }

    public function deleteAvatar(Request $request): RedirectResponse
    {
        $this->profileService->deleteAvatar($request->user());

        return Redirect::route('profile.edit')->with('status', 'avatar-deleted');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
