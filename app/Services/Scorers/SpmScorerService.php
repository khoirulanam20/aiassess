<?php

namespace App\Services\Scorers;

use App\Services\QuestionService;

class SpmScorerService
{
    private const SETS = ['A', 'B', 'C', 'D', 'E'];
    private const QUESTIONS_PER_SET = 12;

    public function __construct(
        private readonly QuestionService $questionService
    ) {}

    public function calculate(array $answers, string $locale = 'id'): array
    {
        $answerKey = $this->loadAnswerKey();
        $dimensions = $this->questionService->loadDimensions('spm');
        $setScores = $this->initializeSetScores();
        $setScores = $this->calculateSetScores($answers, $answerKey, $dimensions, $setScores);
        $totalCorrect = array_sum($setScores);
        $totalAnswered = count($answers);
        $percentage = $totalAnswered > 0 ? round(($totalCorrect / $totalAnswered) * 100) : 0;
        $iqData = $this->estimateIQ($totalCorrect);
        $interpretations = $this->questionService->loadInterpretations('spm');
        $levelData = $interpretations[$iqData['level']] ?? [];

        return [
            'scores' => [
                'total_correct' => $totalCorrect,
                'total_questions' => $totalAnswered,
                'percentage' => $percentage,
            ],
            'set_scores' => $setScores,
            'iq' => $iqData['iq'],
            'percentile' => $iqData['percentile'],
            'level' => $iqData['level'],
            'level_title' => $levelData['title'] ?? $iqData['level'],
            'description' => $levelData['description'] ?? '',
            'advice' => $levelData['advice'] ?? '',
        ];
    }

    private function initializeSetScores(): array
    {
        return array_fill_keys(self::SETS, 0);
    }

    private function loadAnswerKey(): array
    {
        $path = storage_path('app/assessments/spm/answer_key.json');

        if (! file_exists($path)) {
            return [];
        }

        return json_decode(file_get_contents($path), true) ?? [];
    }

    private function calculateSetScores(array $answers, array $answerKey, array $dimensions, array $scores): array
    {
        foreach ($answers as $questionCode => $answer) {
            if (! isset($answerKey[$questionCode])) {
                continue;
            }

            $answerValue = is_array($answer) ? ($answer['answer'] ?? $answer) : $answer;
            $set = $dimensions[$questionCode] ?? 'A';

            if (strtoupper($answerValue) === strtoupper($answerKey[$questionCode]) && isset($scores[$set])) {
                $scores[$set]++;
            }
        }

        return $scores;
    }

    private function estimateIQ(int $rawScore): array
    {
        $iqTablePath = storage_path('app/assessments/spm/iq_table.json');

        if (! file_exists($iqTablePath)) {
            return ['iq' => 100, 'percentile' => 50, 'level' => 'Average'];
        }

        $iqTable = json_decode(file_get_contents($iqTablePath), true) ?? [];
        $closest = null;
        $closestDiff = PHP_INT_MAX;

        foreach ($iqTable as $score => $data) {
            $diff = abs((int) $score - $rawScore);
            if ($diff < $closestDiff) {
                $closestDiff = $diff;
                $closest = $data;
            }
        }

        return $closest ?? ['iq' => 100, 'percentile' => 50, 'level' => 'Average'];
    }

    public function getSetLabel(string $set): string
    {
        $labels = ['A' => 'Set A', 'B' => 'Set B', 'C' => 'Set C', 'D' => 'Set D', 'E' => 'Set E'];

        return $labels[$set] ?? 'Unknown';
    }
}
