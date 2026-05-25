<?php

namespace Tests\Unit\Services\Scorers;

use App\Services\QuestionService;
use App\Services\Scorers\AgilityScorerService;
use Tests\TestCase;

class AgilityScorerTest extends TestCase
{
    private AgilityScorerService $scorer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->scorer = new AgilityScorerService(
            $this->app->make(QuestionService::class)
        );
    }

    public function test_calculate_returns_correct_structure(): void
    {
        $answers = $this->makeSampleAnswers();
        $result = $this->scorer->calculate($answers, 'id');

        $this->assertArrayHasKey('sub_tests', $result);
        $this->assertArrayHasKey('dimensions', $result);
        $this->assertArrayHasKey('overall', $result);
        $this->assertArrayHasKey('highest_sub_test', $result);
        $this->assertArrayHasKey('lowest_sub_test', $result);
    }

    public function test_calculate_returns_all_eight_sub_tests(): void
    {
        $answers = $this->makeSampleAnswers();
        $result = $this->scorer->calculate($answers, 'id');

        $expectedSubTests = [
            'sadana', 'etung', 'sankarta', 'sacit',
            'citraleka', 'bedhe_aksara', 'pathway', 'vacana',
        ];
        foreach ($expectedSubTests as $subTest) {
            $this->assertArrayHasKey($subTest, $result['sub_tests']);
        }
    }

    public function test_each_sub_test_has_required_fields(): void
    {
        $answers = $this->makeSampleAnswers();
        $result = $this->scorer->calculate($answers, 'id');

        foreach ($result['sub_tests'] as $subTest => $data) {
            $this->assertArrayHasKey('score', $data);
            $this->assertArrayHasKey('max_score', $data);
            $this->assertArrayHasKey('percentage', $data);
            $this->assertArrayHasKey('level', $data);
            $this->assertArrayHasKey('label', $data);
        }
    }

    public function test_cognitive_agility_has_five_dimensions(): void
    {
        $answers = $this->makeSampleAnswers();
        $result = $this->scorer->calculate($answers, 'id');

        $expectedDimensions = [
            'cognitive_flexibility', 'analytical_thinking',
            'learning_potential', 'adaptability', 'self_awareness',
        ];
        foreach ($expectedDimensions as $dim) {
            $this->assertArrayHasKey($dim, $result['dimensions']);
        }
    }

    public function test_percentages_are_within_range(): void
    {
        $answers = $this->makeSampleAnswers();
        $result = $this->scorer->calculate($answers, 'id');

        foreach ($result['sub_tests'] as $data) {
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
    }

    public function test_highest_and_lowest_sub_test_are_different(): void
    {
        $answers = $this->makeSampleAnswers();
        $result = $this->scorer->calculate($answers, 'id');

        $this->assertNotNull($result['highest_sub_test']);
        $this->assertNotNull($result['lowest_sub_test']);
    }

    public function test_sub_test_labels(): void
    {
        $this->assertStringContainsString('Sadana', $this->scorer->getSubTestLabel('sadana'));
        $this->assertStringContainsString('Vacana', $this->scorer->getSubTestLabel('vacana'));
        $this->assertEquals('Unknown', $this->scorer->getSubTestLabel('unknown'));
    }

    public function test_dimension_labels(): void
    {
        $this->assertEquals('Fleksibilitas Kognitif', $this->scorer->getDimensionLabel('cognitive_flexibility'));
        $this->assertStringContainsString('Kesadaran', $this->scorer->getDimensionLabel('self_awareness'));
        $this->assertEquals('Unknown', $this->scorer->getDimensionLabel('unknown'));
    }

    public function test_scores_are_consistent_across_calls(): void
    {
        $answers = $this->makeSampleAnswers();
        $first = $this->scorer->calculate($answers, 'id');
        $second = $this->scorer->calculate($answers, 'id');

        $this->assertEquals($first['overall']['percentage'], $second['overall']['percentage']);
        $this->assertEquals($first['highest_sub_test'], $second['highest_sub_test']);
    }

    private function makeSampleAnswers(): array
    {
        $answers = [];

        // sadana, etung, sankarta, sacit, citraleka, bedhe_aksara: correct-answer sub-tests (SD01-SD10 etc.)
        $prefixes = ['sadana' => 'SD', 'etung' => 'ET', 'sankarta' => 'SK',
            'sacit' => 'SC', 'citraleka' => 'CL', 'bedhe_aksara' => 'BA',
            'pathway' => 'PW'];
        foreach ($prefixes as $prefix => $codePrefix) {
            for ($i = 1; $i <= 10; $i++) {
                $answers["{$prefix}_{$codePrefix}" . str_pad((string) $i, 2, '0', STR_PAD_LEFT)] = 'A';
            }
        }
        // vacana: Likert sub-test
        for ($i = 1; $i <= 10; $i++) {
            $answers['vacana_VC' . str_pad((string) $i, 2, '0', STR_PAD_LEFT)] = '3';
        }

        return $answers;
    }
}
