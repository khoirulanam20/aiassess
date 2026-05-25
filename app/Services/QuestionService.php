<?php

namespace App\Services;

class QuestionService extends BaseService
{
    private function basePath(): string
    {
        return storage_path('app/assessments');
    }

    public function loadQuestions(string $assessmentSlug, string $locale = 'id'): array
    {
        $paths = [
            "{$this->basePath()}/{$assessmentSlug}/questions_{$locale}.json",
            "{$this->basePath()}/{$assessmentSlug}/questions_en.json",
            "{$this->basePath()}/{$assessmentSlug}/questions.json",
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                return json_decode(file_get_contents($path), true) ?? [];
            }
        }

        return [];
    }

    public function loadDimensions(string $assessmentSlug): array
    {
        $path = "{$this->basePath()}/{$assessmentSlug}/dimensions.json";

        if (! file_exists($path)) {
            return [];
        }

        return json_decode(file_get_contents($path), true) ?? [];
    }

    public function loadInterpretations(string $assessmentSlug, ?string $type = null): array
    {
        $singlePath = "{$this->basePath()}/{$assessmentSlug}/interpretations/{$type}.json";
        $multiPath = "{$this->basePath()}/{$assessmentSlug}/interpretations.json";

        if ($type && file_exists($singlePath)) {
            return json_decode(file_get_contents($singlePath), true) ?? [];
        }

        if (file_exists($multiPath)) {
            $data = json_decode(file_get_contents($multiPath), true) ?? [];

            return $type && isset($data[$type]) ? $data[$type] : $data;
        }

        return [];
    }

    public function loadAgilityQuestions(string $locale = 'id'): array
    {
        $subTests = ['sadana', 'etung', 'sankarta', 'sacit', 'citraleka', 'bedhe_aksara', 'pathway', 'vacana'];
        $allQuestions = [];

        foreach ($subTests as $subTest) {
            $slug = str_replace('_', '-', $subTest);
            $questions = $this->loadQuestions("agility/{$slug}", $locale);

            foreach ($questions as $q) {
                $q['code'] = "{$subTest}_{$q['code']}";
                $q['sub_test'] = $subTest;
                $allQuestions[] = $q;
            }
        }

        return $allQuestions;
    }

    public function shuffleQuestions(array $questions, bool $shuffle = true): array
    {
        if ($shuffle) {
            shuffle($questions);
        }

        return $questions;
    }

    public function formatForFrontend(array $questions, array $options = []): array
    {
        return array_map(function ($q) {
            return [
                'code' => $q['code'] ?? '',
                'question' => $q['question'] ?? '',
                'options' => $q['options'] ?? [],
            ];
        }, $questions);
    }

    /** @return list<string> */
    public function agilitySubTests(): array
    {
        return ['sadana', 'etung', 'sankarta', 'sacit', 'citraleka', 'bedhe_aksara', 'pathway', 'vacana'];
    }

    public function resolveQuestionsFilePath(string $assessmentSlug, string $locale = 'id'): ?string
    {
        $candidates = [
            "{$this->basePath()}/{$assessmentSlug}/questions_{$locale}.json",
            "{$this->basePath()}/{$assessmentSlug}/questions.json",
        ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return "{$this->basePath()}/{$assessmentSlug}/questions_{$locale}.json";
    }

    public function resolveAgilitySubTestFilePath(string $subTest, string $locale = 'id'): string
    {
        $folder = str_replace('_', '-', $subTest);

        return "{$this->basePath()}/agility/{$folder}/questions_{$locale}.json";
    }

    /**
     * @return array{mode: string, locale: string, questions: array, groups: array<int, array{key: string, label: string, file: string, questions: array}>}
     */
    public function loadQuestionsForAdmin(string $assessmentSlug, string $locale = 'id'): array
    {
        if ($assessmentSlug === 'agility') {
            $groups = [];
            foreach ($this->agilitySubTests() as $subTest) {
                $path = $this->resolveAgilitySubTestFilePath($subTest, $locale);
                $questions = file_exists($path)
                    ? (json_decode(file_get_contents($path), true) ?? [])
                    : [];

                $groups[] = [
                    'key' => $subTest,
                    'label' => ucwords(str_replace('_', ' ', $subTest)),
                    'file' => str_replace(storage_path('app/'), 'storage/app/', $path),
                    'questions' => $this->normalizeQuestions($questions),
                ];
            }

            return [
                'mode' => 'agility',
                'locale' => $locale,
                'questions' => [],
                'groups' => $groups,
            ];
        }

        $path = $this->resolveQuestionsFilePath($assessmentSlug, $locale);
        $questions = file_exists($path)
            ? (json_decode(file_get_contents($path), true) ?? [])
            : [];

        return [
            'mode' => empty($questions) ? 'empty' : 'single',
            'locale' => $locale,
            'file' => str_replace(storage_path('app/'), 'storage/app/', $path),
            'questions' => $this->normalizeQuestions($questions),
            'groups' => [],
        ];
    }

    public function countQuestionsForAdmin(string $assessmentSlug, string $locale = 'id'): int
    {
        $data = $this->loadQuestionsForAdmin($assessmentSlug, $locale);

        if ($data['mode'] === 'agility') {
            return collect($data['groups'])->sum(fn ($g) => count($g['questions']));
        }

        return count($data['questions']);
    }

    /**
     * @param  array<int, array<string, mixed>>  $questions
     */
    public function saveQuestionsFile(string $assessmentSlug, string $locale, array $questions): void
    {
        $path = $this->resolveQuestionsFilePath($assessmentSlug, $locale);
        $dir = dirname($path);

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $normalized = $this->normalizeQuestions($questions);

        file_put_contents(
            $path,
            json_encode($normalized, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n"
        );
    }

    /**
     * @param  array<string, array{questions?: array}>  $groups
     */
    public function saveAgilityQuestions(string $locale, array $groups): void
    {
        foreach ($this->agilitySubTests() as $subTest) {
            $path = $this->resolveAgilitySubTestFilePath($subTest, $locale);
            $dir = dirname($path);

            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $questions = $this->normalizeQuestions($groups[$subTest]['questions'] ?? []);

            file_put_contents(
                $path,
                json_encode($questions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n"
            );
        }
    }

    /**
     * @param  array<int, mixed>  $questions
     * @return array<int, array{code: string, question: string, options: array}>
     */
    public function normalizeQuestions(array $questions): array
    {
        $normalized = [];

        foreach ($questions as $question) {
            if (! is_array($question)) {
                continue;
            }

            $code = trim((string) ($question['code'] ?? ''));
            $text = trim((string) ($question['question'] ?? ''));

            if ($code === '' && $text === '') {
                continue;
            }

            $options = [];
            foreach ($question['options'] ?? [] as $option) {
                if (! is_array($option)) {
                    continue;
                }

                $id = trim((string) ($option['id'] ?? ''));
                $optionText = trim((string) ($option['text'] ?? ''));

                if ($id === '' && $optionText === '') {
                    continue;
                }

                $entry = [
                    'id' => $id !== '' ? $id : chr(65 + count($options)),
                    'text' => $optionText,
                ];

                if (isset($option['dimension']) && trim((string) $option['dimension']) !== '') {
                    $entry['dimension'] = trim((string) $option['dimension']);
                }

                $options[] = $entry;
            }

            if ($code === '' || $text === '' || count($options) < 2) {
                continue;
            }

            $normalized[] = [
                'code' => $code,
                'question' => $text,
                'options' => $options,
            ];
        }

        return $normalized;
    }
}
