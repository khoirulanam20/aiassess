<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Services\QuestionService;
use Database\Seeders\AssessmentQuestionsSeeder;
use Database\Seeders\AssessmentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AssessmentQuestionsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_copies_disc_questions_to_storage(): void
    {
        $this->seed(AssessmentSeeder::class);

        $target = storage_path('app/assessments/disc/questions_id.json');
        if (file_exists($target)) {
            unlink($target);
        }

        $this->seed(AssessmentQuestionsSeeder::class);

        $this->assertFileExists($target);

        $questions = app(QuestionService::class)->loadQuestions('disc');
        $this->assertNotEmpty($questions);
        $this->assertSame(count($questions), Assessment::where('slug', 'disc')->first()->question_count);
    }

    public function test_artisan_command_seeds_questions(): void
    {
        $this->seed(AssessmentSeeder::class);

        File::deleteDirectory(storage_path('app/assessments'));

        $this->artisan('assessments:seed-questions')
            ->assertSuccessful();

        $this->assertFileExists(storage_path('app/assessments/disc/questions_id.json'));
        $this->assertGreaterThan(0, Assessment::where('slug', 'disc')->first()->question_count);
    }
}
