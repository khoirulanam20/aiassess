<?php

namespace Tests\Feature\Admin;

use App\Models\Assessment;
use App\Models\User;
use App\Services\QuestionService;
use Database\Seeders\AssessmentQuestionsSeeder;
use Database\Seeders\AssessmentSeeder;
use Database\Seeders\OrganizationSeeder;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GlobalAssessmentQuestionsTest extends TestCase
{
    use RefreshDatabase;

    private User $superadmin;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RbacSeeder::class);
        $this->seed(OrganizationSeeder::class);
        $this->seed(AssessmentSeeder::class);
        $this->seed(AssessmentQuestionsSeeder::class);

        $this->superadmin = User::factory()->create();
        $this->superadmin->assignRole('superadmin');

        $this->admin = User::factory()->create([
            'organization_id' => \App\Models\Organization::first()->id,
        ]);
        $this->admin->assignRole('admin');
    }

    public function test_superadmin_sees_question_list_on_global_edit(): void
    {
        $assessment = Assessment::where('slug', 'disc')->firstOrFail();

        $response = $this->actingAs($this->superadmin)
            ->get(route('admin.system.assessments.edit', $assessment));

        $response->assertOk();
        $response->assertSee('Daftar pertanyaan');
        $response->assertSee('Q01');
        $response->assertSee('Saya cenderung');
    }

    public function test_admin_cannot_access_global_assessment_edit(): void
    {
        $assessment = Assessment::where('slug', 'disc')->firstOrFail();

        $this->actingAs($this->admin)
            ->get(route('admin.system.assessments.edit', $assessment))
            ->assertForbidden();
    }

    public function test_superadmin_can_update_questions_json(): void
    {
        $assessment = Assessment::where('slug', 'disc')->firstOrFail();
        $service = app(QuestionService::class);
        $bundle = $service->loadQuestionsForAdmin('disc');
        $questions = $bundle['questions'];
        $questions[0]['question'] = 'Pertanyaan uji diubah superadmin';

        $payload = [
            'name' => $assessment->name,
            'type' => $assessment->type,
            'description' => $assessment->description,
            'instructions' => $assessment->instructions,
            'cooldown_days' => $assessment->cooldown_days,
            'sort_order' => $assessment->sort_order,
            'is_active' => '1',
            'questions' => $questions,
        ];

        $this->actingAs($this->superadmin)
            ->patch(route('admin.system.assessments.update', $assessment), $payload)
            ->assertRedirect(route('admin.system.assessments.index'));

        $saved = $service->loadQuestionsForAdmin('disc');
        $this->assertSame('Pertanyaan uji diubah superadmin', $saved['questions'][0]['question']);
        $this->assertSame(count($questions), $assessment->fresh()->question_count);
    }
}
