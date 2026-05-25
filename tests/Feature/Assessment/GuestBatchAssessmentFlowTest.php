<?php

namespace Tests\Feature\Assessment;

use App\Http\Middleware\BatchGuestMiddleware;
use App\Models\Assessment;
use App\Models\AssessmentBatch;
use App\Models\AssessmentBatchShare;
use App\Models\User;
use Database\Seeders\AssessmentSeeder;
use Database\Seeders\OrganizationSeeder;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestBatchAssessmentFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $candidate;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RbacSeeder::class);
        $this->seed(AssessmentSeeder::class);
        $this->seed(OrganizationSeeder::class);

        $orgId = \App\Models\Organization::first()->id;
        $this->admin = User::factory()->create(['organization_id' => $orgId]);
        $this->admin->assignRole('admin');
        $this->candidate = User::factory()->create(['organization_id' => $orgId]);
        $this->candidate->assignRole('candidate');
    }

    public function test_guest_entry_page_is_accessible(): void
    {
        $this->get(route('shared.batch.entry'))
            ->assertOk()
            ->assertSee('Kerjakan Assessment');
    }

    public function test_legacy_user_assessment_routes_are_removed(): void
    {
        $this->get('/assessments')->assertNotFound();
        $this->get('/assessments/mbti/take')->assertNotFound();
        $this->get('/results/history')->assertNotFound();
    }

    public function test_guest_submits_mbti_via_batch_share(): void
    {
        $this->createBatchWithMbti();
        [$share, $cookie] = $this->verifyBatchShare();

        $answers = [];
        for ($i = 1; $i <= 60; $i++) {
            $code = 'Q'.str_pad((string) $i, 3, '0', STR_PAD_LEFT);
            $answers[$code] = $i % 2 === 0 ? 'A' : 'B';
        }

        $response = $this->call('POST', route('shared.batch.submit', [$share->batch->guest_share_token, 'mbti']), [
            'answers' => $answers,
        ], [
            BatchGuestMiddleware::cookieName() => $cookie,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('assessment_results', [
            'user_id' => $this->candidate->id,
        ]);

        $this->assertDatabaseHas('assessment_batch_assignments', [
            'user_id' => $this->candidate->id,
            'status' => 'completed',
        ]);

        $portal = $this->call('GET', route('shared.batch.portal', $share->batch->guest_share_token), [], [
            BatchGuestMiddleware::cookieName() => $cookie,
        ]);
        $portal->assertOk();
        $portal->assertSee('Sudah selesai');
        $portal->assertDontSee('Harus dikerjakan');
    }

    public function test_guest_cannot_retake_completed_batch_assignment(): void
    {
        $this->createBatchWithMbti();
        [$share, $cookie] = $this->verifyBatchShare();

        $answers = [];
        for ($i = 1; $i <= 60; $i++) {
            $code = 'Q'.str_pad((string) $i, 3, '0', STR_PAD_LEFT);
            $answers[$code] = 'A';
        }

        $this->call('POST', route('shared.batch.submit', [$share->batch->guest_share_token, 'mbti']), [
            'answers' => $answers,
        ], [
            BatchGuestMiddleware::cookieName() => $cookie,
        ]);

        $take = $this->call('GET', route('shared.batch.take', [$share->batch->guest_share_token, 'mbti']), [], [
            BatchGuestMiddleware::cookieName() => $cookie,
        ]);

        $take->assertRedirect(route('shared.batch.portal', $share->batch->guest_share_token));
        $take->assertSessionHas('error');
    }

    public function test_guest_submits_disc_via_batch_share(): void
    {
        $disc = Assessment::where('slug', 'disc')->firstOrFail();

        $this->actingAs($this->admin)->post(route('admin.batches.store'), [
            'name' => 'DISC Batch',
            'assessment_ids' => [$disc->id],
            'user_ids' => [$this->candidate->id],
        ]);

        $batch = AssessmentBatch::firstOrFail();
        $this->actingAs($this->admin)
            ->post(route('admin.batches.candidates.share', [$batch, $this->candidate]))
            ->assertSessionHas('access_code');

        [$share, $cookie] = $this->verifyBatchShare();

        $answers = [];
        for ($i = 1; $i <= 24; $i++) {
            $code = 'D'.str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            $answers[$code] = ['most' => 'A', 'least' => 'B'];
        }

        $response = $this->call('POST', route('shared.batch.submit', [$share->batch->guest_share_token, 'disc']), [
            'answers' => $answers,
        ], [
            BatchGuestMiddleware::cookieName() => $cookie,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    private function createBatchWithMbti(): AssessmentBatch
    {
        $mbti = Assessment::where('slug', 'mbti')->firstOrFail();

        $this->actingAs($this->admin)
            ->post(route('admin.batches.store'), [
                'name' => 'MBTI Guest Batch',
                'assessment_ids' => [$mbti->id],
                'user_ids' => [$this->candidate->id],
            ])
            ->assertRedirect();

        $batch = AssessmentBatch::firstOrFail();

        $this->actingAs($this->admin)
            ->post(route('admin.batches.candidates.share', [$batch, $this->candidate]))
            ->assertSessionHas('access_code');

        return $batch;
    }

    /**
     * @return array{0: AssessmentBatchShare, 1: string}
     */
    private function verifyBatchShare(): array
    {
        $share = AssessmentBatchShare::firstOrFail();
        $accessCode = session('access_code');

        $batch = $share->batch;
        $verify = $this->post(route('shared.batch.entry.verify'), [
            'access_code' => $accessCode,
        ]);

        $verify->assertRedirect(route('shared.batch.portal', $batch->guest_share_token));

        $cookie = collect($verify->headers->getCookies())
            ->first(fn ($c) => $c->getName() === BatchGuestMiddleware::cookieName())
            ->getValue();

        return [$share, $cookie];
    }
}
