<?php

namespace Tests\Unit\Services\Scorers;

use App\Services\QuestionService;
use App\Services\Scorers\PapiKostickScorerService;
use Tests\TestCase;

class PapiKostickScorerTest extends TestCase
{
    private PapiKostickScorerService $scorer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->scorer = new PapiKostickScorerService(
            $this->app->make(QuestionService::class)
        );
    }

    public function test_calculate_returns_correct_structure(): void
    {
        $answers = $this->makeSampleAnswers();
        $result = $this->scorer->calculate($answers, 'id');

        $this->assertArrayHasKey('scores', $result);
        $this->assertArrayHasKey('dimensions', $result);
        $this->assertArrayHasKey('profile_graph', $result);
        $this->assertArrayHasKey('highest_dimension', $result);
        $this->assertArrayHasKey('lowest_dimension', $result);
    }

    public function test_calculate_returns_all_ten_dimensions(): void
    {
        $answers = $this->makeSampleAnswers();
        $result = $this->scorer->calculate($answers, 'id');

        $expectedDimensions = ['N', 'G', 'A', 'L', 'P', 'I', 'T', 'V', 'S', 'R'];
        foreach ($expectedDimensions as $dim) {
            $this->assertArrayHasKey($dim, $result['scores']);
            $this->assertArrayHasKey($dim, $result['dimensions']);
        }
    }

    public function test_scores_are_within_valid_range(): void
    {
        $answers = $this->makeSampleAnswers();
        $result = $this->scorer->calculate($answers, 'id');

        foreach ($result['scores'] as $dim => $score) {
            $this->assertGreaterThanOrEqual(0, $score);
            $this->assertLessThanOrEqual(9, $score);
        }
    }

    public function test_highest_and_lowest_dimension_are_set(): void
    {
        $answers = $this->makeSampleAnswers();
        $result = $this->scorer->calculate($answers, 'id');

        $this->assertNotNull($result['highest_dimension']);
        $this->assertNotNull($result['lowest_dimension']);
        $this->assertNotEquals($result['highest_dimension'], $result['lowest_dimension']);
    }

    public function test_each_dimension_has_required_fields(): void
    {
        $answers = $this->makeSampleAnswers();
        $result = $this->scorer->calculate($answers, 'id');

        foreach ($result['dimensions'] as $dim => $data) {
            $this->assertArrayHasKey('score', $data);
            $this->assertArrayHasKey('percentage', $data);
            $this->assertArrayHasKey('level', $data);
            $this->assertArrayHasKey('name', $data);
            $this->assertArrayHasKey('description', $data);
        }
    }

    public function test_profile_graph_matches_scores(): void
    {
        $answers = $this->makeSampleAnswers();
        $result = $this->scorer->calculate($answers, 'id');

        foreach ($result['scores'] as $dim => $score) {
            $this->assertEquals(min($score, 9), $result['profile_graph'][$dim]);
        }
    }

    public function test_dimension_labels_return_correctly(): void
    {
        $this->assertEquals('Need to Achieve', $this->scorer->getDimensionLabel('N'));
        $this->assertEquals('Need to Lead', $this->scorer->getDimensionLabel('L'));
        $this->assertEquals('Need for Rules', $this->scorer->getDimensionLabel('R'));
        $this->assertEquals('Unknown', $this->scorer->getDimensionLabel('X'));
    }

    public function test_dimension_titles_return_correctly(): void
    {
        $this->assertEquals('Kebutuhan Berprestasi', $this->scorer->getDimensionTitle('N'));
        $this->assertEquals('Kebutuhan Otoritas', $this->scorer->getDimensionTitle('A'));
        $this->assertEquals('Unknown', $this->scorer->getDimensionTitle('Z'));
    }

    private function makeSampleAnswers(): array
    {
        $answers = [];
        for ($i = 1; $i <= 45; $i++) {
            $code = 'P' . str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            $answers[$code] = $i % 2 === 0 ? 'A' : 'B';
        }
        return $answers;
    }
}
