<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfileService extends BaseService
{
    public function __construct(
        private readonly ActivityLogService $activityLog
    ) {}

    public function uploadAvatar(User $user, UploadedFile $file): string
    {
        $path = $file->store("avatars/{$user->id}", 'public');

        if ($user->userDetail?->avatar_url) {
            Storage::disk('public')->delete($user->userDetail->avatar_url);
        }

        $user->userDetail()->updateOrCreate(
            ['user_id' => $user->id],
            ['avatar_url' => $path]
        );

        $this->activityLog->logAvatarUpdate();

        return $path;
    }

    public function deleteAvatar(User $user): void
    {
        if ($user->userDetail?->avatar_url) {
            Storage::disk('public')->delete($user->userDetail->avatar_url);
            $user->userDetail->update(['avatar_url' => null]);
        }
    }

    public function updateDetails(User $user, array $data): void
    {
        $user->userDetail()->updateOrCreate(
            ['user_id' => $user->id],
            $data
        );
    }
}
