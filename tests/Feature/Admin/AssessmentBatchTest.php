<?php

namespace Tests\Feature\Admin;

use App\Models\Assessment;
use App\Models\AssessmentBatch;
use App\Models\AssessmentBatchAssignment;
use App\Models\User;
use Database\Seeders\AssessmentSeeder;
use Database\Seeders\OrganizationSeeder;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentBatchTest extends TestCase
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

    public function test_admin_can_create_batch_with_assessments_and_candidates(): void
    {
        $disc = Assessment::where('slug', 'disc')->firstOrFail();
        $mbti = Assessment::where('slug', 'mbti')->firstOrFail();

        $response = $this->actingAs($this->admin)->post(route('admin.batches.store'), [
            'name' => 'Rekrutmen Q2 2026',
            'description' => 'Batch tes awal',
            'assessment_ids' => [$disc->id, $mbti->id],
            'user_ids' => [$this->candidate->id],
        ]);

        $batch = AssessmentBatch::first();
        $this->assertNotNull($batch);
        $response->assertRedirect(route('admin.batches.show', $batch));

        $this->assertDatabaseHas('assessment_batches', [
            'name' => 'Rekrutmen Q2 2026',
            'organization_id' => $this->admin->organization_id,
        ]);

        $this->assertSame(2, AssessmentBatchAssignment::where('assessment_batch_id', $batch->id)->count());
    }

    public function test_admin_can_view_batch_list_and_detail(): void
    {
        $disc = Assessment::where('slug', 'disc')->firstOrFail();

        $this->actingAs($this->admin)->post(route('admin.batches.store'), [
            'name' => 'Batch Test',
            'assessment_ids' => [$disc->id],
            'user_ids' => [$this->candidate->id],
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.batches.index'))
            ->assertOk()
            ->assertSee('Batch Test');

        $batch = AssessmentBatch::first();

        $this->actingAs($this->admin)
            ->get(route('admin.batches.show', $batch))
            ->assertOk()
            ->assertSee($this->candidate->name)
            ->assertSee('DISC');
    }

    public function test_guest_can_take_assessment_via_batch_share_without_login(): void
    {
        $disc = Assessment::where('slug', 'disc')->firstOrFail();

        $this->actingAs($this->admin)->post(route('admin.batches.store'), [
            'name' => 'Guest Take Batch',
            'assessment_ids' => [$disc->id],
            'user_ids' => [$this->candidate->id],
        ]);

        $shareResponse = $this->actingAs($this->admin)
            ->post(route('admin.batches.candidates.share', [
                AssessmentBatch::first(),
                $this->candidate,
            ]));

        $share = \App\Models\AssessmentBatchShare::first();
        $accessCode = session('access_code');

        $batch = $share->batch;
        $verify = $this->post(route('shared.batch.entry.verify'), [
            'access_code' => $accessCode,
        ]);
        $verify->assertRedirect(route('shared.batch.portal', $batch->guest_share_token));

        $cookie = collect($verify->headers->getCookies())
            ->first(fn ($c) => $c->getName() === 'guest_batch_session');

        $take = $this->call('GET', route('shared.batch.take', [$batch->guest_share_token, 'disc']), [], [
            'guest_batch_session' => $cookie->getValue(),
        ]);
        $take->assertOk();
        $take->assertSee('Kembali ke daftar assessment');
    }

    public function test_batch_show_lists_candidates_by_name_only(): void
    {
        $disc = Assessment::where('slug', 'disc')->firstOrFail();

        $this->actingAs($this->admin)->post(route('admin.batches.store'), [
            'name' => 'Batch UI',
            'assessment_ids' => [$disc->id],
            'user_ids' => [$this->candidate->id],
        ]);

        $batch = AssessmentBatch::first();

        $this->actingAs($this->admin)
            ->get(route('admin.batches.show', $batch))
            ->assertOk()
            ->assertSee($this->candidate->name)
            ->assertSee('Detail')
            ->assertDontSee($this->candidate->email);
    }

    public function test_admin_can_create_batch_share_and_guest_sees_portal(): void
    {
        $disc = Assessment::where('slug', 'disc')->firstOrFail();
        $mbti = Assessment::where('slug', 'mbti')->firstOrFail();

        $this->actingAs($this->admin)->post(route('admin.batches.store'), [
            'name' => 'Guest Portal Batch',
            'assessment_ids' => [$disc->id, $mbti->id],
            'user_ids' => [$this->candidate->id],
        ]);

        $batch = AssessmentBatch::first();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.batches.candidates.share', [$batch, $this->candidate]));

        $response->assertRedirect(route('admin.batches.show', $batch));
        $response->assertSessionHas('share_created', true);
        $response->assertSessionHas('access_code');
        $response->assertSessionHas('share_url', route('shared.batch.entry'));

        $share = \App\Models\AssessmentBatchShare::first();
        $accessCode = session('access_code');

        $this->get(route('shared.batch.entry'))->assertOk();

        $batch = $share->batch;
        $verify = $this->post(route('shared.batch.entry.verify'), [
            'access_code' => $accessCode,
        ]);

        $verify->assertRedirect(route('shared.batch.portal', $batch->guest_share_token));
        $verify->assertCookie('guest_batch_session');

        $cookie = collect($verify->headers->getCookies())
            ->first(fn ($c) => $c->getName() === 'guest_batch_session');

        $portal = $this->call('GET', route('shared.batch.portal', $batch->guest_share_token), [], [
            'guest_batch_session' => $cookie->getValue(),
        ]);

        $portal->assertOk();
        $portal->assertSee('Harus dikerjakan');
        $portal->assertSee('DISC Assessment');
        $portal->assertSee('MBTI Assessment');
    }

    public function test_admin_can_view_result_detail_from_candidate_page(): void
    {
        $mbti = Assessment::where('slug', 'mbti')->firstOrFail();

        $this->actingAs($this->admin)->post(route('admin.batches.store'), [
            'name' => 'Result Detail Batch',
            'assessment_ids' => [$mbti->id],
            'user_ids' => [$this->candidate->id],
        ]);

        $batch = AssessmentBatch::firstOrFail();
        [$share, $cookie] = $this->verifyBatchShareForBatch($batch);

        $answers = [];
        for ($i = 1; $i <= 60; $i++) {
            $code = 'Q'.str_pad((string) $i, 3, '0', STR_PAD_LEFT);
            $answers[$code] = 'A';
        }

        $this->call('POST', route('shared.batch.submit', [$batch->guest_share_token, 'mbti']), [
            'answers' => $answers,
        ], [
            \App\Http\Middleware\BatchGuestMiddleware::cookieName() => $cookie,
        ]);

        $result = \App\Models\AssessmentResult::where('user_id', $this->candidate->id)->firstOrFail();

        $this->actingAs($this->admin)
            ->get(route('admin.batches.candidates.results.show', [$batch, $this->candidate, $result]))
            ->assertOk()
            ->assertSee('Detail Hasil Assessment')
            ->assertSee('ESTJ');
    }

    public function test_candidate_detail_page_lists_assessments(): void
    {
        $disc = Assessment::where('slug', 'disc')->firstOrFail();

        $this->actingAs($this->admin)->post(route('admin.batches.store'), [
            'name' => 'Detail Batch',
            'assessment_ids' => [$disc->id],
            'user_ids' => [$this->candidate->id],
        ]);

        $batch = AssessmentBatch::first();

        $this->actingAs($this->admin)
            ->get(route('admin.batches.candidates.show', [$batch, $this->candidate]))
            ->assertOk()
            ->assertSee('DISC Assessment')
            ->assertSee('Menunggu');
    }

    /**
     * @return array{0: \App\Models\AssessmentBatchShare, 1: string}
     */
    private function verifyBatchShareForBatch(AssessmentBatch $batch): array
    {
        $this->actingAs($this->admin)
            ->post(route('admin.batches.candidates.share', [$batch, $this->candidate]))
            ->assertSessionHas('access_code');

        $share = \App\Models\AssessmentBatchShare::where('assessment_batch_id', $batch->id)
            ->where('user_id', $this->candidate->id)
            ->firstOrFail();

        $verify = $this->post(route('shared.batch.entry.verify'), [
            'access_code' => session('access_code'),
        ]);

        $verify->assertRedirect(route('shared.batch.portal', $batch->guest_share_token));

        $cookie = collect($verify->headers->getCookies())
            ->first(fn ($c) => $c->getName() === \App\Http\Middleware\BatchGuestMiddleware::cookieName())
            ->getValue();

        return [$share, $cookie];
    }
}
