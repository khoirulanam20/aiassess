<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogService extends BaseService
{
    public function log(string $action, ?string $description = null, ?array $metadata = null, ?int $userId = null): ActivityLog
    {
        $request = app(Request::class);

        return ActivityLog::create([
            'user_id' => $userId ?? Auth::id(),
            'action' => $action,
            'description' => $description,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'metadata' => $metadata,
        ]);
    }

    public function logLogin(): void
    {
        $this->log('login', 'User logged in');
    }

    public function logLogout(): void
    {
        $this->log('logout', 'User logged out');
    }

    public function logProfileUpdate(array $changes): void
    {
        $this->log('profile_update', 'Profile information updated', [
            'changes' => $changes,
        ]);
    }

    public function logAvatarUpdate(): void
    {
        $this->log('avatar_update', 'Profile avatar updated');
    }

    public function logPasswordChange(): void
    {
        $this->log('password_change', 'Password changed');
    }

    public function logAssessmentStart(string $assessmentName): void
    {
        $this->log('assessment_start', "Started assessment: {$assessmentName}", [
            'assessment' => $assessmentName,
        ]);
    }

    public function logAssessmentComplete(string $assessmentName): void
    {
        $this->log('assessment_complete', "Completed assessment: {$assessmentName}", [
            'assessment' => $assessmentName,
        ]);
    }

    public function logShareCreated(int $resultId, int $shareId): void
    {
        $this->log('share.created', "Share created for result #{$resultId}", [
            'result_id' => $resultId,
            'share_id' => $shareId,
        ]);
    }

    public function logShareVerified(string $shareToken, int $shareId, string $ip, string $userAgent): void
    {
        $this->log('share.verified', "Guest verified share: {$shareToken}", [
            'share_id' => $shareId,
            'ip' => $ip,
            'user_agent' => $userAgent,
        ]);
    }

    public function logShareRevoked(int $shareId): void
    {
        $this->log('share.revoked', "Share #{$shareId} revoked", [
            'share_id' => $shareId,
        ]);
    }

    public function logShareCodeRegenerated(int $shareId): void
    {
        $this->log('share.code_regenerated', "Access code regenerated for share #{$shareId}", [
            'share_id' => $shareId,
        ]);
    }

    public function logRoleAssigned(int $targetUserId, string $role): void
    {
        $this->log('role.assigned', "Role {$role} assigned to user #{$targetUserId}", [
            'target_user_id' => $targetUserId,
            'role' => $role,
        ]);
    }

    public function getUserLogs(int $userId, int $limit = 20): array
    {
        return ActivityLog::where('user_id', $userId)
            ->latest()
            ->take($limit)
            ->get()
            ->toArray();
    }

    public function getRecentActivity(int $limit = 10): array
    {
        return ActivityLog::where('user_id', Auth::id())
            ->latest()
            ->take($limit)
            ->get()
            ->toArray();
    }
}
