<?php

namespace App\Services\Scorers;

use App\Services\QuestionService;

class MbtiScorerService
{
    private const DIMENSIONS = ['EI', 'SN', 'TF', 'JP'];

    public function __construct(
        private readonly QuestionService $questionService
    ) {}

    public function calculate(array $answers, string $locale = 'id'): array
    {
        $dimensions = $this->questionService->loadDimensions('mbti');
        $scores = $this->initializeScores();
        $dimensionScores = $this->calculateDimensionScores($answers, $dimensions, $scores);
        $type = $this->determineType($dimensionScores);
        $interpretation = $this->questionService->loadInterpretations('mbti', strtolower($type));

        return [
            'scores' => $dimensionScores,
            'type' => $type,
            'type_name' => $interpretation['name'] ?? '',
            'description' => $interpretation['description'] ?? '',
            'strengths' => $interpretation['strengths'] ?? [],
            'weaknesses' => $interpretation['weaknesses'] ?? [],
            'career_paths' => $interpretation['career_paths'] ?? [],
            'interpretation' => $interpretation,
        ];
    }

    private function initializeScores(): array
    {
        return [
            'E' => 0, 'I' => 0,
            'S' => 0, 'N' => 0,
            'T' => 0, 'F' => 0,
            'J' => 0, 'P' => 0,
        ];
    }

    private function calculateDimensionScores(array $answers, array $dimensions, array $scores): array
    {
        foreach ($answers as $questionCode => $answer) {
            if (! isset($dimensions[$questionCode])) {
                continue;
            }

            $answerLetter = is_array($answer) ? ($answer['answer'] ?? $answer) : $answer;

            foreach (self::DIMENSIONS as $dim) {
                $left = $dim[0];
                $right = $dim[1];

                if (isset($dimensions[$questionCode][$left]) && $dimensions[$questionCode][$left] == $answerLetter) {
                    $scores[$left]++;
                } elseif (isset($dimensions[$questionCode][$right]) && $dimensions[$questionCode][$right] == $answerLetter) {
                    $scores[$right]++;
                }
            }
        }

        return [
            'EI' => [
                'E' => round(($scores['E'] / max(1, ($scores['E'] + $scores['I']))) * 100),
                'I' => round(($scores['I'] / max(1, ($scores['E'] + $scores['I']))) * 100),
            ],
            'SN' => [
                'S' => round(($scores['S'] / max(1, ($scores['S'] + $scores['N']))) * 100),
                'N' => round(($scores['N'] / max(1, ($scores['S'] + $scores['N']))) * 100),
            ],
            'TF' => [
                'T' => round(($scores['T'] / max(1, ($scores['T'] + $scores['F']))) * 100),
                'F' => round(($scores['F'] / max(1, ($scores['T'] + $scores['F']))) * 100),
            ],
            'JP' => [
                'J' => round(($scores['J'] / max(1, ($scores['J'] + $scores['P']))) * 100),
                'P' => round(($scores['P'] / max(1, ($scores['J'] + $scores['P']))) * 100),
            ],
        ];
    }

    private function determineType(array $scores): string
    {
        $type = '';

        $type .= $scores['EI']['E'] >= $scores['EI']['I'] ? 'E' : 'I';
        $type .= $scores['SN']['S'] >= $scores['SN']['N'] ? 'S' : 'N';
        $type .= $scores['TF']['T'] >= $scores['TF']['F'] ? 'T' : 'F';
        $type .= $scores['JP']['J'] >= $scores['JP']['P'] ? 'J' : 'P';

        return $type;
    }

    public function getTypeLabel(string $type): string
    {
        $labels = [
            'INTJ' => 'The Architect', 'INTP' => 'The Logician',
            'ENTJ' => 'The Commander', 'ENTP' => 'The Debater',
            'INFJ' => 'The Advocate', 'INFP' => 'The Mediator',
            'ENFJ' => 'The Protagonist', 'ENFP' => 'The Campaigner',
            'ISTJ' => 'The Logistician', 'ISFJ' => 'The Defender',
            'ESTJ' => 'The Executive', 'ESFJ' => 'The Consul',
            'ISTP' => 'The Virtuoso', 'ISFP' => 'The Adventurer',
            'ESTP' => 'The Entrepreneur', 'ESFP' => 'The Entertainer',
        ];

        return $labels[strtoupper($type)] ?? 'Unknown';
    }
}
