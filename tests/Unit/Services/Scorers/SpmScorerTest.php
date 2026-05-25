<?php

namespace Tests\Unit\Services\Scorers;

use App\Services\QuestionService;
use App\Services\Scorers\SpmScorerService;
use Tests\TestCase;

class SpmScorerTest extends TestCase
{
    private SpmScorerService $scorer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->scorer = new SpmScorerService(
            $this->app->make(QuestionService::class)
        );
    }

    public function test_calculate_returns_correct_structure(): void
    {
        $answers = $this->makeAllCorrectAnswers();
        $result = $this->scorer->calculate($answers, 'id');

        $this->assertArrayHasKey('scores', $result);
        $this->assertArrayHasKey('set_scores', $result);
        $this->assertArrayHasKey('iq', $result);
        $this->assertArrayHasKey('percentile', $result);
        $this->assertArrayHasKey('level', $result);
        $this->assertArrayHasKey('description', $result);
    }

    public function test_all_correct_returns_maximum_score(): void
    {
        $answers = $this->makeAllCorrectAnswers();
        $result = $this->scorer->calculate($answers, 'id');

        $this->assertEquals(60, $result['scores']['total_correct']);
        $this->assertEquals(100, $result['scores']['percentage']);
    }

    public function test_all_wrong_returns_zero_score(): void
    {
        $answers = $this->makeAllWrongAnswers();
        $result = $this->scorer->calculate($answers, 'id');

        $this->assertEquals(0, $result['scores']['total_correct']);
        $this->assertEquals(0, $result['scores']['percentage']);
    }

    public function test_set_scores_are_calculated_correctly(): void
    {
        $correctAnswers = $this->makeAllCorrectAnswers();
        $halfAnswers = [];
        foreach ($correctAnswers as $code => $correct) {
            $setNum = (int) substr($code, 1);
            $halfAnswers[$code] = $setNum % 2 === 0 ? $correct : 'wrong';
        }
        $result = $this->scorer->calculate($halfAnswers, 'id');

        $this->assertArrayHasKey('A', $result['set_scores']);
        $this->assertArrayHasKey('E', $result['set_scores']);
    }

    public function test_iq_increases_with_higher_score(): void
    {
        $allCorrect = $this->makeAllCorrectAnswers();
        $allWrong = $this->makeAllWrongAnswers();

        $highResult = $this->scorer->calculate($allCorrect, 'id');
        $lowResult = $this->scorer->calculate($allWrong, 'id');

        $this->assertGreaterThan($lowResult['iq'], $highResult['iq']);
    }

    public function test_perfect_score_returns_very_superior(): void
    {
        $answers = $this->makeAllCorrectAnswers();
        $result = $this->scorer->calculate($answers, 'id');

        $this->assertEquals('Very Superior', $result['level']);
    }

    public function test_percentile_is_within_range(): void
    {
        $answers = $this->makeAllCorrectAnswers();
        $result = $this->scorer->calculate($answers, 'id');

        $this->assertGreaterThanOrEqual(0, $result['percentile']);
        $this->assertLessThanOrEqual(100, $result['percentile']);
    }

    public function test_get_set_labels(): void
    {
        $this->assertEquals('Set A', $this->scorer->getSetLabel('A'));
        $this->assertEquals('Set E', $this->scorer->getSetLabel('E'));
        $this->assertEquals('Unknown', $this->scorer->getSetLabel('Z'));
    }

    private function makeAllCorrectAnswers(): array
    {
        $answerKeyPath = storage_path('app/assessments/spm/answer_key.json');
        return json_decode(file_get_contents($answerKeyPath), true);
    }

    private function makeAllWrongAnswers(): array
    {
        $answerKeyPath = storage_path('app/assessments/spm/answer_key.json');
        $key = json_decode(file_get_contents($answerKeyPath), true);

        $wrongMap = ['A' => 'B', 'B' => 'C', 'C' => 'D', 'D' => 'E', 'E' => 'F', 'F' => 'A'];
        $wrong = [];
        foreach ($key as $code => $correct) {
            $wrong[$code] = $wrongMap[$correct] ?? 'A';
        }
        return $wrong;
    }
}
