<x-layouts.assessment
    :assessment="$assessment"
    :back-url="route('shared.batch.portal', $token)"
    :subtitle="$share->batch->name . ' · ' . $share->user->name"
>
    <div x-data="assessmentManager()" class="space-y-6">
        <div class="card">
            <div class="mb-6">
                <div class="flex items-center justify-between text-sm">
                    <span class="font-medium text-ink dark:text-gray-100">
                        <span x-text="currentIndex + 1"></span>/{{ count($questions) }}
                    </span>
                    <span class="text-gray-400 dark:text-gray-500" x-text="`${Math.round(((currentIndex + 1) / {{ count($questions) }}) * 100)}% complete`"></span>
                </div>
                <div class="progress-bar mt-2">
                    <div class="progress-bar-fill" x-bind:style="`width: ${((currentIndex + 1) / {{ count($questions) }}) * 100}%`"></div>
                </div>
            </div>

            <form method="POST" action="{{ route('shared.batch.submit', [$token, $assessment->slug]) }}" x-ref="assessmentForm">
                @csrf

                <template x-for="(question, idx) in questions" :key="question.code">
                    <div x-show="currentIndex === idx" x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                        <div class="mb-6">
                            <p class="text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500" x-text="question.code"></p>
                            <h3 class="mt-1 text-lg font-semibold text-ink dark:text-gray-100" x-text="question.question"></h3>
                        </div>

                        <div class="space-y-3">
                            <template x-for="(option, optIdx) in question.options" :key="option.id">
                                <label class="flex cursor-pointer items-center gap-4 rounded-xl border border-gray-200 p-4 transition-all duration-150 hover:border-sky-200 hover:bg-sky-50/50 dark:border-navy-600 dark:hover:border-sky-700 dark:hover:bg-sky-900/20"
                                       x-bind:class="{ 'border-sky-500 bg-sky-50 ring-1 ring-sky-500 dark:border-sky-400 dark:bg-sky-900/30 dark:ring-sky-400': answers[question.code] === option.id }">
                                    <input type="radio"
                                           x-bind:name="`answers[${question.code}]`"
                                           x-bind:value="option.id"
                                           x-model="answers[question.code]"
                                           class="h-4 w-4 border-gray-300 text-sky-600 focus:ring-sky-500 dark:border-navy-500 dark:bg-navy-700 dark:text-sky-400 dark:focus:ring-sky-400">
                                    <span class="text-sm text-gray-700 dark:text-gray-300" x-text="option.text"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </template>

                <div class="mt-8 flex items-center justify-between border-t border-gray-200 pt-6 dark:border-navy-600">
                    <x-ui.button-icon
                        type="button"
                        variant="ghost"
                        icon="arrow-left"
                        x-show="currentIndex > 0"
                        @click="prevQuestion"
                    >
                        {{ __('Sebelumnya') }}
                    </x-ui.button-icon>
                    <div x-show="currentIndex === 0"></div>

                    <x-ui.button-icon
                        type="button"
                        variant="solid"
                        icon="arrow-right"
                        x-show="currentIndex < questions.length - 1"
                        @click="nextQuestion"
                    >
                        {{ __('Berikutnya') }}
                    </x-ui.button-icon>

                    <x-ui.button-icon
                        type="submit"
                        variant="solid"
                        icon="check"
                        x-show="currentIndex === questions.length - 1"
                    >
                        {{ __('Kirim Jawaban') }}
                    </x-ui.button-icon>
                </div>
            </form>
        </div>
    </div>

    <script>
        function assessmentManager() {
            return {
                questions: @json($questions),
                currentIndex: 0,
                answers: {},
                nextQuestion() {
                    const currentCode = this.questions[this.currentIndex]?.code;
                    if (currentCode && !this.answers[currentCode]) {
                        alert('{{ __('Pilih jawaban terlebih dahulu.') }}');
                        return;
                    }
                    if (this.currentIndex < this.questions.length - 1) {
                        this.currentIndex++;
                    }
                },
                prevQuestion() {
                    if (this.currentIndex > 0) {
                        this.currentIndex--;
                    }
                },
                init() {
                    this.$watch('currentIndex', () => {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    });
                }
            }
        }
    </script>
</x-layouts.assessment>
