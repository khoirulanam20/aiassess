<?php

namespace App\Services\Scorers;

use App\Services\QuestionService;

class BusinessInsightScorerService
{
    private const DIMENSIONS = ['autonomy', 'innovativeness', 'risk_taking', 'proactiveness', 'competitive_aggressiveness'];
    private const LIKERT_MAX = 5;
    private const QUESTIONS_PER_DIM = 10;

    public function __construct(
        private readonly QuestionService $questionService
    ) {}

    public function calculate(array $answers, string $locale = 'id'): array
    {
        $dimensions = $this->questionService->loadDimensions('business-insight');
        $interpretations = $this->questionService->loadInterpretations('business-insight');
        $rawScores = $this->initializeScores();
        $rawScores = $this->calculateDimensionScores($answers, $dimensions, $rawScores);
        $dimensionResults = $this->buildDimensionResults($rawScores, $interpretations);
        $overall = $this->calculateOverall($dimensionResults, $interpretations);

        return [
            'scores' => $dimensionResults,
            'overall' => $overall,
            'raw_scores' => $rawScores,
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

    private function calculateDimensionScores(array $answers, array $dimensions, array $scores): array
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

        return $scores;
    }

    private function buildDimensionResults(array $rawScores, array $interpretations): array
    {
        $results = [];

        foreach (self::DIMENSIONS as $dim) {
            $avg = $rawScores['count'][$dim] > 0
                ? round($rawScores['total'][$dim] / $rawScores['count'][$dim], 2)
                : 0;

            $percentage = round(($avg / self::LIKERT_MAX) * 100);
            $level = $this->determineLevel($percentage);
            $interp = $interpretations[$dim] ?? [];
            $levelDesc = $interp['levels'] ?? [];

            $results[$dim] = [
                'average' => $avg,
                'percentage' => $percentage,
                'level' => $level,
                'description' => $interp['description'] ?? '',
                'level_description' => $this->getLevelDescription($levelDesc, $level),
            ];
        }

        return $results;
    }

    private function determineLevel(float $percentage): string
    {
        if ($percentage < 45) return 'low';
        if ($percentage < 65) return 'medium';
        return 'high';
    }

    private function getLevelDescription(array $levelDesc, string $level): string
    {
        return $levelDesc[$level] ?? '';
    }

    private function calculateOverall(array $dimensionResults, array $interpretations): array
    {
        $totalPercentage = 0;

        foreach (self::DIMENSIONS as $dim) {
            $totalPercentage += $dimensionResults[$dim]['percentage'] ?? 0;
        }

        $avgPercentage = round($totalPercentage / count(self::DIMENSIONS));
        $overallInterp = $interpretations['overall'] ?? [];
        $levels = $overallInterp['levels'] ?? [];

        if ($avgPercentage < 40) {
            $level = 'low';
        } elseif ($avgPercentage < 60) {
            $level = 'medium';
        } elseif ($avgPercentage < 80) {
            $level = 'high';
        } else {
            $level = 'very_high';
        }

        $levelData = $levels[$level] ?? [];

        return [
            'percentage' => $avgPercentage,
            'level' => $level,
            'label' => $levelData['label'] ?? '',
            'description' => $levelData['description'] ?? '',
            'advice' => $levelData['advice'] ?? '',
        ];
    }

    private function findExtreme(array $results, string $type): ?string
    {
        if (empty($results)) return null;

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
            'autonomy' => 'Otonomi',
            'innovativeness' => 'Inovasi',
            'risk_taking' => 'Pengambilan Risiko',
            'proactiveness' => 'Proaktif',
            'competitive_aggressiveness' => 'Agresivitas Kompetitif',
        ];

        return $labels[$code] ?? 'Unknown';
    }
}
