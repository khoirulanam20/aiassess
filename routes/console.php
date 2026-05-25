<?php

use Database\Seeders\AssessmentQuestionsSeeder;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('assessments:seed-questions', function () {
    $this->call('db:seed', ['--class' => AssessmentQuestionsSeeder::class, '--force' => true]);
})->purpose('Salin bank soal JSON dari database/seeders/data ke storage/app/assessments');
