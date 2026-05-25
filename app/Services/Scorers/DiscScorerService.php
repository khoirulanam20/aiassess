<?php

namespace App\Services\Scorers;

use App\Services\QuestionService;

class DiscScorerService
{
    private const TYPES = ['D', 'I', 'S', 'C'];

    public function __construct(
        private readonly QuestionService $questionService
    ) {}

    public function calculate(array $answers, string $locale = 'id'): array
    {
        $dimensions = $this->questionService->loadDimensions('disc');
        $rawScores = $this->initializeScores();
        $rawScores = $this->calculateRawScores($answers, $dimensions, $rawScores);
        $totalAnswered = array_sum($rawScores);
        $percentages = $this->calculatePercentages($rawScores, $totalAnswered);
        $primaryType = $this->determinePrimaryType($percentages);
        $interpretation = $this->questionService->loadInterpretations('disc', strtolower($primaryType));

        return [
            'scores' => $percentages,
            'raw_scores' => $rawScores,
            'primary_type' => $primaryType,
            'type_name' => $interpretation['title'] ?? '',
            'description' => $interpretation['description'] ?? '',
            'strengths' => $interpretation['strengths'] ?? [],
            'weaknesses' => $interpretation['weaknesses'] ?? [],
            'communication_style' => $interpretation['communication_style'] ?? '',
            'motivations' => $interpretation['motivations'] ?? [],
            'fears' => $interpretation['fears'] ?? [],
            'career_paths' => $interpretation['career_paths'] ?? [],
            'interpretation' => $interpretation,
        ];
    }

    private function initializeScores(): array
    {
        return ['D' => 0, 'I' => 0, 'S' => 0, 'C' => 0];
    }

    private function calculateRawScores(array $answers, array $dimensions, array $scores): array
    {
        foreach ($answers as $questionCode => $answer) {
            $answerValue = is_array($answer) ? ($answer['answer'] ?? $answer) : $answer;

            if (! isset($dimensions[$questionCode])) {
                continue;
            }

            $dimension = $dimensions[$questionCode];

            if (in_array($dimension, self::TYPES) && isset($scores[$dimension])) {
                $scores[$dimension]++;
            }
        }

        return $scores;
    }

    private function calculatePercentages(array $rawScores, int $total): array
    {
        $percentages = [];

        foreach (self::TYPES as $type) {
            $percentages[$type] = $total > 0
                ? round(($rawScores[$type] / $total) * 100)
                : 0;
        }

        return $percentages;
    }

    private function determinePrimaryType(array $percentages): string
    {
        $max = max($percentages);
        $types = array_keys($percentages, $max);

        return $types[0];
    }

    public function getTypeLabel(string $type): string
    {
        $labels = [
            'D' => 'Dominance',
            'I' => 'Influence',
            'S' => 'Steadiness',
            'C' => 'Conscientiousness',
        ];

        return $labels[strtoupper($type)] ?? 'Unknown';
    }
}
