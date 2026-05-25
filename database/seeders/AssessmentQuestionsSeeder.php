<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Services\QuestionService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class AssessmentQuestionsSeeder extends Seeder
{
    public function run(): void
    {
        $source = database_path('seeders/data/assessments');
        $destination = storage_path('app/assessments');

        if (! File::isDirectory($source)) {
            $this->command?->warn("Direktori sumber tidak ditemukan: {$source}");

            return;
        }

        File::ensureDirectoryExists($destination);

        $copied = $this->copyAssessmentFiles($source, $destination);

        $this->command?->info("Disalin {$copied} file bank soal ke storage/app/assessments/");

        $this->syncQuestionCounts();
    }

    private function copyAssessmentFiles(string $source, string $destination): int
    {
        $count = 0;

        foreach (File::allFiles($source) as $file) {
            $relative = $file->getRelativePathname();
            $target = $destination.DIRECTORY_SEPARATOR.$relative;

            File::ensureDirectoryExists(dirname($target));
            File::copy($file->getPathname(), $target);
            $count++;
        }

        return $count;
    }

    private function syncQuestionCounts(): void
    {
        $questionService = app(QuestionService::class);

        foreach (Assessment::all() as $assessment) {
            $count = $questionService->countQuestionsForAdmin($assessment->slug);

            if ($count > 0) {
                $assessment->update(['question_count' => $count]);
            }
        }
    }
}
