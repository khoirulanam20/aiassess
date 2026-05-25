<?php

namespace Tests\Feature\Rbac;

use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Models\Organization;
use App\Models\ResultShare;
use App\Models\User;
use Database\Seeders\AssessmentSeeder;
use Database\Seeders\OrganizationSeeder;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class RbacFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $superadmin;
    private User $admin;
    private User $adminOrgB;
    private User $candidate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RbacSeeder::class);
        $this->seed(AssessmentSeeder::class);
        $this->seed(OrganizationSeeder::class);

        $orgA = Organization::where('slug', 'demo-perusahaan')->first();
        $orgB = Organization::where('slug', 'acme-corp')->first();

        $this->superadmin = User::factory()->create(['organization_id' => null]);
        $this->superadmin->assignRole('superadmin');

        $this->admin = User::factory()->create(['organization_id' => $orgA->id]);
        $this->admin->assignRole('admin');

        $this->adminOrgB = User::factory()->create(['organization_id' => $orgB->id]);
        $this->adminOrgB->assignRole('admin');

        $this->candidate = User::factory()->create(['organization_id' => $orgA->id]);
        $this->candidate->assignRole('candidate');
    }

    // ─── Role & Permission Checks ───

    public function test_superadmin_has_all_permissions(): void
    {
        $this->assertTrue($this->superadmin->hasRole('superadmin'));
        $this->assertTrue($this->superadmin->can('users.view_any'));
        $this->assertTrue($this->superadmin->can('system.config'));
        $this->assertTrue($this->superadmin->can('results.view_any'));
        $this->assertTrue($this->superadmin->can('admin.dashboard'));
    }

    public function test_admin_has_limited_permissions(): void
    {
        $this->assertTrue($this->admin->hasRole('admin'));
        $this->assertTrue($this->admin->can('results.view_org'));
        $this->assertTrue($this->admin->can('results.share_create'));
        $this->assertTrue($this->admin->can('admin.dashboard'));
        $this->assertFalse($this->admin->can('users.view_any'));
        $this->assertFalse($this->admin->can('system.config'));
        $this->assertFalse($this->admin->can('users.assign_role'));
        $this->assertFalse($this->admin->can('results.view_any'));
    }

    public function test_candidate_has_no_staff_permissions(): void
    {
        $this->assertTrue($this->candidate->hasRole('candidate'));
        $this->assertFalse($this->candidate->can('assessments.take'));
        $this->assertFalse($this->candidate->can('admin.dashboard'));
        $this->assertFalse($this->candidate->can('results.share_create'));
        $this->assertFalse($this->candidate->canLogin());
    }

    // ─── Guest Share Flow ───

    public function test_guest_cannot_access_admin_routes(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_share_form_loads(): void
    {
        $share = $this->createShare();

        $response = $this->get(route('shared.result.form', $share->share_token));

        $response->assertOk();
        $response->assertSee('Kode Akses');
    }

    public function test_share_form_with_invalid_token_returns_404(): void
    {
        $this->get(route('shared.result.form', 'invalid-token'))->assertNotFound();
    }

    public function test_verify_with_correct_code_redirects_to_result(): void
    {
        $share = $this->createShare();

        $response = $this->post(route('shared.result.verify', $share->share_token), [
            'access_code' => '123456',
        ]);

        $response->assertRedirect(route('shared.result.view', $share->share_token));
        $response->assertCookie('guest_share_session');
    }

    public function test_verify_with_wrong_code_returns_error(): void
    {
        $share = $this->createShare();

        $response = $this->post(route('shared.result.verify', $share->share_token), [
            'access_code' => '000000',
        ]);

        $response->assertSessionHasErrors('access_code');
    }

    public function test_locked_share_returns_error_message(): void
    {
        $share = $this->createShare();
        $share->update([
            'locked_until' => now()->addMinutes(30),
            'failed_attempts' => 5,
        ]);

        $response = $this->post(route('shared.result.verify', $share->share_token), [
            'access_code' => '123456',
        ]);

        $response->assertSessionHasErrors('access_code');
    }

    public function test_brute_force_locks_out_after_5_attempts(): void
    {
        $share = $this->createShare();

        for ($i = 0; $i < 5; $i++) {
            $response = $this->post(route('shared.result.verify', $share->share_token), [
                'access_code' => '000000',
            ]);
        }

        $share->refresh();
        $this->assertNotNull($share->locked_until);
        $this->assertTrue($share->isLocked());
    }

    public function test_verify_with_revoked_share_returns_error(): void
    {
        $share = $this->createShare();
        $share->update(['is_active' => false, 'revoked_at' => now()]);

        $response = $this->post(route('shared.result.verify', $share->share_token), [
            'access_code' => '123456',
        ]);

        $response->assertSessionHasErrors('access_code');
    }

    public function test_guest_session_required_for_result_page(): void
    {
        $share = $this->createShare();

        $response = $this->get(route('shared.result.view', $share->share_token));

        $response->assertRedirect(route('shared.result.form', $share->share_token));
    }

    public function test_guest_can_view_result_with_valid_session(): void
    {
        $share = $this->createShare();

        $response = $this->post(route('shared.result.verify', $share->share_token), [
            'access_code' => '123456',
        ]);

        $cookies = $response->headers->getCookies();
        $cookieValue = null;
        foreach ($cookies as $cookie) {
            if ($cookie->getName() === 'guest_share_session') {
                $cookieValue = $cookie->getValue();
                break;
            }
        }

        $this->assertNotNull($cookieValue);

        $resultResponse = $this->call('GET', route('shared.result.view', $share->share_token), [], [
            'guest_share_session' => $cookieValue,
        ]);

        $resultResponse->assertOk();
    }

    public function test_verify_increments_view_count(): void
    {
        $share = $this->createShare();
        $this->assertSame(0, $share->view_count);

        $this->post(route('shared.result.verify', $share->share_token), [
            'access_code' => '123456',
        ]);

        $this->assertSame(1, $share->fresh()->view_count);
    }

    // ─── Share Admin Flow ───

    public function test_candidate_cannot_create_share(): void
    {
        $result = $this->createResultForUser($this->candidate);

        $response = $this->actingAs($this->candidate)
            ->post(route('admin.results.share', $result));

        $response->assertForbidden();
    }

    public function test_admin_can_create_share_for_same_org(): void
    {
        $result = $this->createResultForUser($this->candidate);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.results.share', $result));

        $response->assertSessionHas('share_created');
    }

    public function test_admin_can_revoke_share(): void
    {
        $share = $this->createShare();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.shares.revoke', $share));

        $response->assertSessionHas('success');
        $this->assertFalse($share->fresh()->is_active);
        $this->assertNotNull($share->fresh()->revoked_at);
    }

    public function test_admin_can_regenerate_code(): void
    {
        $share = $this->createShare();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.shares.regenerate', $share));

        $response->assertSessionHas('share_created');
        $response->assertSessionHas('access_code');
    }

    public function test_superadmin_can_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('admin.dashboard'));

        $response->assertOk();
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertOk();
    }

    public function test_admin_can_access_org_assessment_config(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.assessments.org.index'));

        $response->assertOk();
    }

    public function test_candidate_cannot_login(): void
    {
        $this->candidate->update(['password' => Hash::make('password')]);

        $response = $this->post('/login', [
            'email' => $this->candidate->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_superadmin_can_access_system_config(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('admin.system.config'));

        $response->assertOk();
    }

    public function test_admin_cannot_access_system_config(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.system.config'));

        $response->assertForbidden();
    }

    public function test_guest_verify_has_rate_limit(): void
    {
        $share = $this->createShare();

        for ($i = 0; $i < 5; $i++) {
            $response = $this->post(route('shared.result.verify', $share->share_token), [
                'access_code' => '000000',
            ]);
        }

        $response = $this->post(route('shared.result.verify', $share->share_token), [
            'access_code' => '000000',
        ]);

        $response->assertStatus(429);
    }

    // ─── Route Permission Enforcement ───

    public function test_legacy_assessment_take_route_removed(): void
    {
        $this->get('/assessments/mbti/take')->assertNotFound();
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $this->admin->update(['password' => Hash::make('password')]);

        $response = $this->post('/login', [
            'email' => $this->admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_sidebar_admin_sees_hr_menus_not_assessment_catalog(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Admin / HR');
        $response->assertSee('Batch Assessment');
        $response->assertSee('Kandidat / Karyawan');
        $response->assertSee('Konfig Assessment');
        $response->assertDontSee('Superadmin');
        $response->assertDontSee('Assessment Global');
        $response->assertDontSee('Perusahaan (Tenant)');
        $response->assertDontSee('Daftar Assessment');
        $response->assertDontSee('Hasil & Share');
        $response->assertDontSee('/assessments/mbti/take');
        $response->assertDontSee('/results/history');
    }

    public function test_sidebar_superadmin_sees_system_menus_not_hr_company(): void
    {
        $response = $this->actingAs($this->superadmin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Superadmin');
        $response->assertSee('Assessment Global');
        $response->assertSee('Perusahaan (Tenant)');
        $response->assertSee('Dashboard Sistem');
        $response->assertDontSee('Admin / HR');
        $response->assertDontSee('Batch Assessment');
        $response->assertDontSee('Kandidat / Karyawan');
        $response->assertDontSee('Daftar Assessment');
    }

    // ─── Helpers ───

    private function createShare(): ResultShare
    {
        $result = $this->createResultForUser($this->candidate);

        return ResultShare::create([
            'assessment_result_id' => $result->id,
            'created_by' => $this->admin->id,
            'share_token' => Str::random(48),
            'access_code_hash' => Hash::make('123456', ['cost' => 4]),
            'is_active' => true,
            'view_count' => 0,
        ]);
    }

    private function createResultForUser(User $user): AssessmentResult
    {
        $assessment = Assessment::where('slug', 'mbti')->first();

        return AssessmentResult::create([
            'user_id' => $user->id,
            'assessment_id' => $assessment->id,
            'test_name' => 'MBTI Assessment',
            'result' => json_encode(['type' => 'INTJ']),
            'scores' => json_encode(['EI' => ['E' => 40, 'I' => 60]]),
            'status' => 'completed',
        ]);
    }
}
