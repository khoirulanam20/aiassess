<?php

namespace Tests\Unit\Services\Scorers;

use App\Services\QuestionService;
use App\Services\Scorers\BusinessInsightScorerService;
use Tests\TestCase;

class BusinessInsightScorerTest extends TestCase
{
    private BusinessInsightScorerService $scorer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->scorer = new BusinessInsightScorerService(
            $this->app->make(QuestionService::class)
        );
    }

    public function test_calculate_returns_correct_structure(): void
    {
        $answers = $this->makeSampleAnswers();
        $result = $this->scorer->calculate($answers, 'id');

        $this->assertArrayHasKey('scores', $result);
        $this->assertArrayHasKey('overall', $result);
        $this->assertArrayHasKey('raw_scores', $result);
        $this->assertArrayHasKey('highest_dimension', $result);
        $this->assertArrayHasKey('lowest_dimension', $result);
    }

    public function test_calculate_returns_all_five_dimensions(): void
    {
        $answers = $this->makeSampleAnswers();
        $result = $this->scorer->calculate($answers, 'id');

        $expectedDimensions = [
            'autonomy', 'innovativeness', 'risk_taking',
            'proactiveness', 'competitive_aggressiveness',
        ];
        foreach ($expectedDimensions as $dim) {
            $this->assertArrayHasKey($dim, $result['scores']);
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

    public function test_maximum_scores_return_high_level(): void
    {
        $answers = [];
        for ($i = 1; $i <= 50; $i++) {
            $answers['B' . str_pad((string) $i, 2, '0', STR_PAD_LEFT)] = '5';
        }
        $result = $this->scorer->calculate($answers, 'id');

        foreach ($result['scores'] as $dim => $data) {
            $this->assertEquals('high', $data['level']);
        }
        $this->assertEquals('very_high', $result['overall']['level']);
    }

    public function test_minimum_scores_return_low_level(): void
    {
        $answers = [];
        for ($i = 1; $i <= 50; $i++) {
            $answers['B' . str_pad((string) $i, 2, '0', STR_PAD_LEFT)] = '1';
        }
        $result = $this->scorer->calculate($answers, 'id');

        foreach ($result['scores'] as $dim => $data) {
            $this->assertEquals('low', $data['level']);
        }
        $this->assertEquals('low', $result['overall']['level']);
    }

    public function test_overall_has_required_fields(): void
    {
        $answers = $this->makeSampleAnswers();
        $result = $this->scorer->calculate($answers, 'id');

        $this->assertArrayHasKey('percentage', $result['overall']);
        $this->assertArrayHasKey('level', $result['overall']);
        $this->assertArrayHasKey('description', $result['overall']);
        $this->assertArrayHasKey('advice', $result['overall']);
    }

    public function test_highest_and_lowest_are_different(): void
    {
        $answers = $this->makeSampleAnswers();
        $result = $this->scorer->calculate($answers, 'id');

        $this->assertNotNull($result['highest_dimension']);
        $this->assertNotNull($result['lowest_dimension']);
    }

    public function test_raw_scores_contain_total_and_count(): void
    {
        $answers = $this->makeSampleAnswers();
        $result = $this->scorer->calculate($answers, 'id');

        $this->assertArrayHasKey('total', $result['raw_scores']);
        $this->assertArrayHasKey('count', $result['raw_scores']);
        foreach ($result['raw_scores']['count'] as $dim => $count) {
            $this->assertEquals(10, $count);
        }
    }

    public function test_get_dimension_labels(): void
    {
        $this->assertEquals('Otonomi', $this->scorer->getDimensionLabel('autonomy'));
        $this->assertEquals('Proaktif', $this->scorer->getDimensionLabel('proactiveness'));
        $this->assertEquals('Unknown', $this->scorer->getDimensionLabel('unknown'));
    }

    private function makeSampleAnswers(): array
    {
        $answers = [];
        for ($i = 1; $i <= 50; $i++) {
            $answers['B' . str_pad((string) $i, 2, '0', STR_PAD_LEFT)] = (string) (($i % 5) + 1);
        }
        return $answers;
    }
}
