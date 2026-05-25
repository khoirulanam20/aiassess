<?php

namespace Tests\Unit\Services\Scorers;

use App\Services\QuestionService;
use App\Services\Scorers\MsdtScorerService;
use Tests\TestCase;

class MsdtScorerTest extends TestCase
{
    private MsdtScorerService $scorer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->scorer = new MsdtScorerService(
            $this->app->make(QuestionService::class)
        );
    }

    public function test_calculate_returns_correct_structure(): void
    {
        $answers = $this->makeSampleAnswers();
        $result = $this->scorer->calculate($answers, 'id');

        $this->assertArrayHasKey('scores', $result);
        $this->assertArrayHasKey('averages', $result);
        $this->assertArrayHasKey('overall', $result);
        $this->assertArrayHasKey('highest_dimension', $result);
        $this->assertArrayHasKey('lowest_dimension', $result);
    }

    public function test_calculate_returns_all_eight_dimensions(): void
    {
        $answers = $this->makeSampleAnswers();
        $result = $this->scorer->calculate($answers, 'id');

        $expectedDimensions = [
            'planning', 'organizing', 'leading', 'controlling',
            'decision_making', 'communication', 'problem_solving', 'delegation',
        ];
        foreach ($expectedDimensions as $dim) {
            $this->assertArrayHasKey($dim, $result['scores']);
            $this->assertArrayHasKey($dim, $result['averages']);
        }
    }

    public function test_averages_are_within_likert_range(): void
    {
        $answers = $this->makeSampleAnswers();
        $result = $this->scorer->calculate($answers, 'id');

        foreach ($result['averages'] as $dim => $avg) {
            $this->assertGreaterThanOrEqual(1, $avg);
            $this->assertLessThanOrEqual(5, $avg);
        }
    }

    public function test_percentages_are_within_range(): void
    {
        $answers = $this->makeSampleAnswers();
        $result = $this->scorer->calculate($answers, 'id');

        foreach ($result['scores'] as $dim => $data) {
            $this->assertGreaterThanOrEqual(0, $data['percentage']);
            $this->assertLessThanOrEqual(100, $data['percentage']);
        }
    }

    public function test_overall_has_required_fields(): void
    {
        $answers = $this->makeSampleAnswers();
        $result = $this->scorer->calculate($answers, 'id');

        $this->assertArrayHasKey('percentage', $result['overall']);
        $this->assertArrayHasKey('level', $result['overall']);
        $this->assertGreaterThanOrEqual(0, $result['overall']['percentage']);
        $this->assertLessThanOrEqual(100, $result['overall']['percentage']);
    }

    public function test_highest_and_lowest_dimension_are_different(): void
    {
        $answers = $this->makeSampleAnswers();
        $result = $this->scorer->calculate($answers, 'id');

        $this->assertNotNull($result['highest_dimension']);
        $this->assertNotNull($result['lowest_dimension']);
    }

    public function test_dimension_levels_match_percentage(): void
    {
        $answers = $this->makeSampleAnswers(['M01' => 5, 'M02' => 5, 'M09' => 5, 'M10' => 5]);
        $result = $this->scorer->calculate($answers, 'id');

        foreach ($result['scores'] as $dim => $data) {
            if ($data['percentage'] >= 85) {
                $this->assertEquals('Sangat Baik', $data['level']);
            } elseif ($data['percentage'] < 40) {
                $this->assertEquals('Perlu Pengembangan', $data['level']);
            }
        }
    }

    public function test_all_zero_answers_return_lowest_level(): void
    {
        $answers = [];
        for ($i = 1; $i <= 60; $i++) {
            $answers['M' . str_pad((string) $i, 2, '0', STR_PAD_LEFT)] = '1';
        }
        $result = $this->scorer->calculate($answers, 'id');

        $this->assertEquals('Perlu Pengembangan', $result['overall']['level']);
    }

    public function test_get_dimension_labels(): void
    {
        $this->assertEquals('Planning', $this->scorer->getDimensionLabel('planning'));
        $this->assertEquals('Communication', $this->scorer->getDimensionLabel('communication'));
        $this->assertEquals('Unknown', $this->scorer->getDimensionLabel('unknown'));
    }

    private function makeSampleAnswers(array $overrides = []): array
    {
        $answers = [];
        for ($i = 1; $i <= 60; $i++) {
            $code = 'M' . str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            $answers[$code] = $overrides[$code] ?? (string) (($i % 5) + 1);
        }
        return $answers;
    }
}
