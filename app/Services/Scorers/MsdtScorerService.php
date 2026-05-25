<?php

namespace App\Services\Scorers;

use App\Services\QuestionService;

class MsdtScorerService
{
    private const DIMENSIONS = ['planning', 'organizing', 'leading', 'controlling', 'decision_making', 'communication', 'problem_solving', 'delegation'];

    private const LIKERT_MAX = 5;

    public function __construct(
        private readonly QuestionService $questionService
    ) {}

    public function calculate(array $answers, string $locale = 'id'): array
    {
        $dimensions = $this->questionService->loadDimensions('msdt');
        $interpretations = $this->questionService->loadInterpretations('msdt');
        $rawScores = $this->initializeScores();
        $counts = $this->initializeScores();
        $scores = $this->calculateDimensionScores($answers, $dimensions, $rawScores, $counts);
        $averages = $this->calculateAverages($scores['total'], $scores['count']);
        $dimensionResults = $this->buildDimensionResults($averages, $interpretations);
        $overallScore = $this->calculateOverall($dimensionResults);

        return [
            'scores' => $dimensionResults,
            'averages' => $averages,
            'overall' => $overallScore,
            'highest_dimension' => $this->findExtreme($dimensionResults, 'highest'),
            'lowest_dimension' => $this->findExtreme($dimensionResults, 'lowest'),
        ];
    }

    private function initializeScores(): array
    {
        return [
            'total' => array_fill_keys(self::DIMENSIONS, 0),
            'count' => array_fill_keys(self::DIMENSIONS, 0),
        ];
    }

    private function calculateDimensionScores(array $answers, array $dimensions, array $scores, array $counts): array
    {
        foreach ($answers as $questionCode => $answer) {
            if (! isset($dimensions[$questionCode])) {
                continue;
            }

            $dim = $dimensions[$questionCode];
            if (! in_array($dim, self::DIMENSIONS)) {
                continue;
            }

            $answerValue = is_array($answer) ? ($answer['answer'] ?? $answer) : $answer;
            $score = min(max((int) $answerValue, 1), self::LIKERT_MAX);

            $scores['total'][$dim] += $score;
            $scores['count'][$dim]++;
        }

        return ['total' => $scores['total'], 'count' => $scores['count']];
    }

    private function calculateAverages(array $totals, array $counts): array
    {
        $averages = [];

        foreach (self::DIMENSIONS as $dim) {
            $averages[$dim] = $counts[$dim] > 0
                ? round($totals[$dim] / $counts[$dim], 2)
                : 0;
        }

        return $averages;
    }

    private function buildDimensionResults(array $averages, array $interpretations): array
    {
        $results = [];

        foreach (self::DIMENSIONS as $dim) {
            $avg = $averages[$dim];
            $percentage = round(($avg / self::LIKERT_MAX) * 100);
            $level = $this->determineLevel($percentage);
            $interp = $interpretations[$dim] ?? [];

            $results[$dim] = [
                'average' => $avg,
                'percentage' => $percentage,
                'level' => $level,
                'name' => $this->getDimensionLabel($dim),
                'title' => $interp['title'] ?? $this->getDimensionLabel($dim),
                'description' => $interp['description'] ?? '',
                'strengths' => $interp['strengths'] ?? [],
                'weaknesses' => $interp['weaknesses'] ?? [],
                'advice' => $interp['advice'] ?? '',
            ];
        }

        return $results;
    }

    private function determineLevel(float $percentage): string
    {
        if ($percentage < 40) {
            return 'Perlu Pengembangan';
        }
        if ($percentage < 55) {
            return 'Kurang';
        }
        if ($percentage < 70) {
            return 'Cukup';
        }
        if ($percentage < 85) {
            return 'Baik';
        }

        return 'Sangat Baik';
    }

    private function calculateOverall(array $dimensionResults): array
    {
        $totalPercentage = 0;
        $count = count(self::DIMENSIONS);

        foreach (self::DIMENSIONS as $dim) {
            $totalPercentage += $dimensionResults[$dim]['percentage'] ?? 0;
        }

        $averagePercentage = $count > 0 ? round($totalPercentage / $count) : 0;

        return [
            'percentage' => $averagePercentage,
            'level' => $this->determineLevel($averagePercentage),
        ];
    }

    private function findExtreme(array $results, string $type): ?string
    {
        $dimensions = array_keys($results);
        if (empty($dimensions)) {
            return null;
        }

        if ($type === 'highest') {
            $max = -1;
            $maxDim = null;
            foreach ($results as $dim => $data) {
                if ($data['percentage'] > $max) {
                    $max = $data['percentage'];
                    $maxDim = $dim;
                }
            }

            return $maxDim;
        }

        $min = 101;
        $minDim = null;
        foreach ($results as $dim => $data) {
            if ($data['percentage'] < $min) {
                $min = $data['percentage'];
                $minDim = $dim;
            }
        }

        return $minDim;
    }

    public function getDimensionLabel(string $code): string
    {
        $labels = [
            'planning' => 'Planning',
            'organizing' => 'Organizing',
            'leading' => 'Leading',
            'controlling' => 'Controlling',
            'decision_making' => 'Decision Making',
            'communication' => 'Communication',
            'problem_solving' => 'Problem Solving',
            'delegation' => 'Delegation',
        ];

        return $labels[$code] ?? 'Unknown';
    }

    public function getDimensionTitle(string $code): string
    {
        $titles = [
            'planning' => 'Perencanaan',
            'organizing' => 'Pengorganisasian',
            'leading' => 'Kepemimpinan',
            'controlling' => 'Pengendalian',
            'decision_making' => 'Pengambilan Keputusan',
            'communication' => 'Komunikasi',
            'problem_solving' => 'Pemecahan Masalah',
            'delegation' => 'Delegasi',
        ];

        return $titles[$code] ?? 'Unknown';
    }
}
