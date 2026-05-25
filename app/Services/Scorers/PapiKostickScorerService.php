<?php

namespace App\Services\Scorers;

use App\Services\QuestionService;

class PapiKostickScorerService
{
    private const DIMENSIONS = ['N', 'G', 'A', 'L', 'P', 'I', 'T', 'V', 'S', 'R'];

    private const MAX_SCORE = 9;

    public function __construct(
        private readonly QuestionService $questionService
    ) {}

    public function calculate(array $answers, string $locale = 'id'): array
    {
        $dimensions = $this->questionService->loadDimensions('papikostick');
        $rawScores = $this->initializeScores();
        $rawScores = $this->calculateRawScores($answers, $dimensions, $rawScores);
        $interpretations = $this->questionService->loadInterpretations('papikostick');
        $dimensionResults = $this->buildDimensionResults($rawScores, $interpretations);
        $profileGraph = $this->buildProfileGraph($rawScores);

        return [
            'scores' => $rawScores,
            'dimensions' => $dimensionResults,
            'profile_graph' => $profileGraph,
            'highest_dimension' => $this->findExtreme($rawScores, 'highest'),
            'lowest_dimension' => $this->findExtreme($rawScores, 'lowest'),
        ];
    }

    private function initializeScores(): array
    {
        return array_fill_keys(self::DIMENSIONS, 0);
    }

    private function calculateRawScores(array $answers, array $dimensions, array $scores): array
    {
        foreach ($answers as $questionCode => $answer) {
            if (! isset($dimensions[$questionCode])) {
                continue;
            }

            $answerValue = is_array($answer) ? ($answer['answer'] ?? $answer) : $answer;

            if (isset($dimensions[$questionCode][$answerValue])) {
                $dim = $dimensions[$questionCode][$answerValue];
                if (in_array($dim, self::DIMENSIONS)) {
                    $scores[$dim]++;
                }
            }
        }

        return $scores;
    }

    private function buildDimensionResults(array $rawScores, array $interpretations): array
    {
        $results = [];

        foreach (self::DIMENSIONS as $dim) {
            $score = min($rawScores[$dim], self::MAX_SCORE);
            $level = $this->determineLevel($score);
            $interp = $interpretations[$dim] ?? [];
            $levelData = $this->getLevelData($interp, $score);

            $results[$dim] = [
                'score' => $score,
                'max_score' => self::MAX_SCORE,
                'percentage' => round(($score / self::MAX_SCORE) * 100),
                'level' => $level,
                'name' => $interp['name'] ?? $dim,
                'title' => $interp['title'] ?? '',
                'description' => $interp['description'] ?? '',
                'level_description' => $levelData['description'] ?? '',
                'advice' => $levelData['advice'] ?? '',
            ];
        }

        return $results;
    }

    private function determineLevel(int $score): string
    {
        if ($score <= 2) {
            return 'Rendah';
        }
        if ($score <= 4) {
            return 'Cukup';
        }
        if ($score <= 6) {
            return 'Sedang';
        }
        if ($score <= 8) {
            return 'Tinggi';
        }

        return 'Sangat Tinggi';
    }

    private function getLevelData(array $interp, int $score): array
    {
        if (empty($interp)) {
            return [];
        }

        $levels = $interp['levels'] ?? [];

        foreach ($levels as $range => $data) {
            [$min, $max] = explode('-', $range);
            if ($score >= (int) $min && $score <= (int) $max) {
                return $data;
            }
        }

        return [];
    }

    private function buildProfileGraph(array $rawScores): array
    {
        $graph = [];

        foreach (self::DIMENSIONS as $dim) {
            $graph[$dim] = min($rawScores[$dim], self::MAX_SCORE);
        }

        return $graph;
    }

    private function findExtreme(array $scores, string $type): ?string
    {
        if (empty($scores)) {
            return null;
        }

        if ($type === 'highest') {
            $max = max($scores);
            $keys = array_keys($scores, $max);

            return $keys[0] ?? null;
        }

        $min = min($scores);
        $keys = array_keys($scores, $min);

        return $keys[0] ?? null;
    }

    public function getDimensionLabel(string $code): string
    {
        $labels = [
            'N' => 'Need to Achieve', 'G' => 'Need to be Noticed',
            'A' => 'Need for Authority', 'L' => 'Need to Lead',
            'P' => 'Need for Affiliation', 'I' => 'Need to be Independent',
            'T' => 'Need for Change', 'V' => 'Need to be Vigorous',
            'S' => 'Need to be Sociable', 'R' => 'Need for Rules',
        ];

        return $labels[strtoupper($code)] ?? 'Unknown';
    }

    public function getDimensionTitle(string $code): string
    {
        $titles = [
            'N' => 'Kebutuhan Berprestasi', 'G' => 'Kebutuhan Diperhatikan',
            'A' => 'Kebutuhan Otoritas', 'L' => 'Kebutuhan Memimpin',
            'P' => 'Kebutuhan Afiliasi', 'I' => 'Kebutuhan Mandiri',
            'T' => 'Kebutuhan Perubahan', 'V' => 'Kebutuhan Vigor',
            'S' => 'Kebutuhan Bersosialisasi', 'R' => 'Kebutuhan Aturan',
        ];

        return $titles[strtoupper($code)] ?? 'Unknown';
    }
}
