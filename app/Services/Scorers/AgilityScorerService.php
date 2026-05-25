<?php

namespace App\Services\Scorers;

use App\Services\QuestionService;

class AgilityScorerService
{
    private const SUB_TESTS = ['sadana', 'etung', 'sankarta', 'sacit', 'citraleka', 'bedhe_aksara', 'pathway', 'vacana'];
    private const MAX_SCORES = [
        'sadana' => 10, 'etung' => 10, 'sankarta' => 10, 'sacit' => 10,
        'citraleka' => 10, 'bedhe_aksara' => 10, 'pathway' => 10, 'vacana' => 50,
    ];
    private const DIMENSION_MAP = [
        'sadana' => 'cognitive_flexibility', 'etung' => 'numerical_reasoning',
        'sankarta' => 'pattern_recognition', 'sacit' => 'abstract_reasoning',
        'citraleka' => 'visual_spatial', 'bedhe_aksara' => 'verbal_reasoning',
        'pathway' => 'learning_agility', 'vacana' => 'self_reflection',
    ];
    private const AGGREGATE_DIMENSIONS = ['cognitive_flexibility', 'analytical_thinking', 'learning_potential', 'adaptability', 'self_awareness'];

    public function __construct(
        private readonly QuestionService $questionService
    ) {}

    public function calculate(array $answers, string $locale = 'id'): array
    {
        $subTestResults = $this->calculateSubTests($answers, $locale);
        $dimensionScores = $this->aggregateToDimensions($subTestResults);
        $overall = $this->calculateOverall($dimensionScores);

        return [
            'sub_tests' => $subTestResults,
            'dimensions' => $dimensionScores,
            'overall' => $overall,
            'highest_sub_test' => $this->findExtremeSubTest($subTestResults, 'highest'),
            'lowest_sub_test' => $this->findExtremeSubTest($subTestResults, 'lowest'),
        ];
    }

    private function calculateSubTests(array $allAnswers, string $locale): array
    {
        $results = [];

        foreach (self::SUB_TESTS as $subTest) {
            $slug = str_replace('_', '-', $subTest);
            $questions = $this->questionService->loadQuestions("agility/{$slug}", $locale);
            $maxScore = self::MAX_SCORES[$subTest] ?? 10;

            if ($subTest === 'vacana') {
                $score = $this->calculateLikertSubTest($allAnswers, $questions, $slug);
            } else {
                $score = $this->calculateCorrectSubTest($allAnswers, $questions, $slug);
            }

            $percentage = $maxScore > 0 ? round(($score / $maxScore) * 100) : 0;

            $results[$subTest] = [
                'score' => $score,
                'max_score' => $maxScore,
                'percentage' => $percentage,
                'level' => $this->determineLevel($percentage),
                'label' => $this->getSubTestLabel($subTest),
            ];
        }

        return $results;
    }

    private function calculateCorrectSubTest(array $allAnswers, array $questions, string $prefix): int
    {
        $score = 0;

        foreach ($questions as $q) {
            $code = $q['code'] ?? '';
            $fullCode = "{$prefix}_{$code}";

            if (! isset($allAnswers[$fullCode]) && ! isset($allAnswers[$code])) {
                continue;
            }

            $answer = $allAnswers[$fullCode] ?? $allAnswers[$code] ?? '';
            $answerValue = is_array($answer) ? ($answer['answer'] ?? $answer) : $answer;
            $correct = $q['correct'] ?? '';

            if (! empty($correct) && strtoupper((string) $answerValue) === strtoupper($correct)) {
                $score++;
            }
        }

        return $score;
    }

    private function calculateLikertSubTest(array $allAnswers, array $questions, string $prefix): int
    {
        $score = 0;

        foreach ($questions as $q) {
            $code = $q['code'] ?? '';
            $fullCode = "{$prefix}_{$code}";

            if (! isset($allAnswers[$fullCode]) && ! isset($allAnswers[$code])) {
                continue;
            }

            $answer = $allAnswers[$fullCode] ?? $allAnswers[$code] ?? '';
            $answerValue = is_array($answer) ? ($answer['answer'] ?? $answer) : $answer;

            $score += min(max((int) $answerValue, 1), 5);
        }

        return $score;
    }

    private function aggregateToDimensions(array $subTestResults): array
    {
        return [
            'cognitive_flexibility' => [
                'percentage' => $subTestResults['sadana']['percentage'] ?? 0,
                'level' => $subTestResults['sadana']['level'] ?? 'Rendah',
            ],
            'analytical_thinking' => [
                'percentage' => round((
                    ($subTestResults['etung']['percentage'] ?? 0) +
                    ($subTestResults['sankarta']['percentage'] ?? 0)
                ) / 2),
                'level' => '',
            ],
            'learning_potential' => [
                'percentage' => round((
                    ($subTestResults['sacit']['percentage'] ?? 0) +
                    ($subTestResults['citraleka']['percentage'] ?? 0) +
                    ($subTestResults['bedhe_aksara']['percentage'] ?? 0)
                ) / 3),
                'level' => '',
            ],
            'adaptability' => [
                'percentage' => $subTestResults['pathway']['percentage'] ?? 0,
                'level' => $subTestResults['pathway']['level'] ?? 'Rendah',
            ],
            'self_awareness' => [
                'percentage' => $subTestResults['vacana']['percentage'] ?? 0,
                'level' => $subTestResults['vacana']['level'] ?? 'Rendah',
            ],
        ];
    }

    private function calculateOverall(array $dimensionScores): array
    {
        $total = 0;
        $count = count($dimensionScores);

        foreach ($dimensionScores as $data) {
            $total += $data['percentage'];
        }

        $avg = $count > 0 ? round($total / $count) : 0;

        return [
            'percentage' => $avg,
            'level' => $this->determineOverallLevel($avg),
        ];
    }

    private function determineLevel(float $percentage): string
    {
        if ($percentage < 40) return 'Perlu Pengembangan';
        if ($percentage < 60) return 'Cukup';
        if ($percentage < 80) return 'Baik';
        return 'Sangat Baik';
    }

    private function determineOverallLevel(float $percentage): string
    {
        if ($percentage < 40) return 'Perlu Pengembangan';
        if ($percentage < 55) return 'Kurang';
        if ($percentage < 70) return 'Cukup';
        if ($percentage < 85) return 'Baik';
        return 'Sangat Baik';
    }

    private function findExtremeSubTest(array $results, string $type): ?string
    {
        if (empty($results)) return null;

        if ($type === 'highest') {
            $max = -1;
            $maxKey = null;
            foreach ($results as $key => $data) {
                if ($data['percentage'] > $max) {
                    $max = $data['percentage'];
                    $maxKey = $key;
                }
            }
            return $maxKey;
        }

        $min = 101;
        $minKey = null;
        foreach ($results as $key => $data) {
            if ($data['percentage'] < $min) {
                $min = $data['percentage'];
                $minKey = $key;
            }
        }
        return $minKey;
    }

    public function getSubTestLabel(string $code): string
    {
        $labels = [
            'sadana' => 'Sadana (Counterfactual Thinking)',
            'etung' => 'Etung (Numeracy)',
            'sankarta' => 'Sankarta (Pattern Recognition)',
            'sacit' => 'Sacit (Abstract Reasoning)',
            'citraleka' => 'Citraleka (Visual Spatial)',
            'bedhe_aksara' => 'Bedhe Aksara (Verbal Reasoning)',
            'pathway' => 'Pathway (Learning Agility)',
            'vacana' => 'Vacana (Self-Reflection)',
        ];

        return $labels[$code] ?? 'Unknown';
    }

    public function getDimensionLabel(string $code): string
    {
        $labels = [
            'cognitive_flexibility' => 'Fleksibilitas Kognitif',
            'analytical_thinking' => 'Pemikiran Analitis',
            'learning_potential' => 'Potensi Pembelajaran',
            'adaptability' => 'Adaptabilitas',
            'self_awareness' => 'Kesadaran Diri',
        ];

        return $labels[$code] ?? 'Unknown';
    }
}
