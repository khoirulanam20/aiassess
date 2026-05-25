<?php

namespace Database\Seeders;

use App\Models\Assessment;
use Illuminate\Database\Seeder;

class AssessmentSeeder extends Seeder
{
    public function run(): void
    {
        $assessments = [
            [
                'slug' => 'mbti',
                'name' => 'MBTI Assessment',
                'type' => 'free',
                'description' => 'Tes kepribadian berdasarkan 16 tipe kepribadian Myers-Briggs',
                'question_count' => 60,
                'cooldown_days' => 30,
                'icon' => 'brain',
                'color' => '#1E3A5F',
                'sort_order' => 1,
            ],
            [
                'slug' => 'disc',
                'name' => 'DISC Assessment',
                'type' => 'free',
                'description' => 'Tes kepribadian berdasarkan perilaku Dominance, Influence, Steadiness, Compliance',
                'question_count' => 24,
                'cooldown_days' => 30,
                'icon' => 'bar-chart',
                'color' => '#2563EB',
                'sort_order' => 2,
            ],
            [
                'slug' => 'papikostick',
                'name' => 'PAPI Kostick',
                'type' => 'free',
                'description' => 'Tes kepribadian untuk mengukur persepsi diri di lingkungan kerja',
                'question_count' => 90,
                'cooldown_days' => 30,
                'icon' => 'users',
                'color' => '#059669',
                'sort_order' => 3,
            ],
            [
                'slug' => 'msdt',
                'name' => 'MSDT Assessment',
                'type' => 'free',
                'description' => 'Management Skill Development Test untuk mengukur keterampilan manajemen',
                'question_count' => 60,
                'cooldown_days' => 30,
                'icon' => 'briefcase',
                'color' => '#DC2626',
                'sort_order' => 4,
            ],
            [
                'slug' => 'spm',
                'name' => 'SPM Assessment',
                'type' => 'free',
                'description' => 'Standard Progressive Matrices untuk mengukur kemampuan penalaran abstrak',
                'question_count' => 60,
                'cooldown_days' => 30,
                'icon' => 'grid',
                'color' => '#7C3AED',
                'sort_order' => 5,
            ],
            [
                'slug' => 'business-insight',
                'name' => 'Business Insight',
                'type' => 'premium',
                'description' => 'Tes untuk mengukur kemampuan bisnis dan wawasan strategis',
                'question_count' => 50,
                'cooldown_days' => 30,
                'icon' => 'trending-up',
                'color' => '#F59E0B',
                'sort_order' => 6,
            ],
            [
                'slug' => 'agility',
                'name' => 'Agility Assessment',
                'type' => 'premium',
                'description' => 'Tes untuk mengukur kemampuan beradaptasi dan belajar (8 sub-test)',
                'question_count' => 8,
                'cooldown_days' => 30,
                'icon' => 'zap',
                'color' => '#EC4899',
                'sort_order' => 7,
            ],
            [
                'slug' => 'fingerprint',
                'name' => 'Fingerprint Analysis',
                'type' => 'free',
                'description' => 'Analisis sidik jari untuk mengetahui potensi bakat',
                'question_count' => 1,
                'cooldown_days' => 30,
                'icon' => 'fingerprint',
                'color' => '#14B8A6',
                'sort_order' => 8,
            ],
            [
                'slug' => 'video',
                'name' => 'Video Assessment',
                'type' => 'free',
                'description' => 'Penilaian berdasarkan video interview dengan AI analysis',
                'question_count' => 12,
                'cooldown_days' => 30,
                'icon' => 'video',
                'color' => '#F43F5E',
                'sort_order' => 9,
            ],
            [
                'slug' => 'fisiognomi',
                'name' => 'Fisiognomi (Face Reading)',
                'type' => 'free',
                'description' => 'Analisis wajah untuk mengetahui karakteristik kepribadian',
                'question_count' => 1,
                'cooldown_days' => 30,
                'icon' => 'camera',
                'color' => '#8B5CF6',
                'sort_order' => 10,
            ],
            [
                'slug' => 'palmistry',
                'name' => 'Palmistry (Palm Reading)',
                'type' => 'free',
                'description' => 'Analisis telapak tangan untuk mengetahui karakter dan potensi',
                'question_count' => 1,
                'cooldown_days' => 30,
                'icon' => 'hand',
                'color' => '#F97316',
                'sort_order' => 11,
            ],
        ];

        foreach ($assessments as $assessment) {
            Assessment::create($assessment);
        }
    }
}
