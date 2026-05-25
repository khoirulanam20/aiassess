<x-layouts.assessment :assessment="$assessment" :backUrl="route('assessments.show', $assessment->slug)" subtitle="{{ $assessment->question_count }} {{ __('questions') }}">
    <div x-data="assessmentManager()" class="space-y-6">
        <div class="card">
            <div class="mb-6">
                <div class="flex items-center justify-between text-sm">
                    <span class="font-medium text-ink dark:text-white">
                        <span x-text="currentIndex + 1"></span>/{{ count($questions) }}
                    </span>
                    <span class="text-ink-muted" x-text="`${Math.round(((currentIndex + 1) / {{ count($questions) }}) * 100)}% {{ __('complete') }}`"></span>
                </div>
                <div class="progress-bar mt-2 dark:bg-navy-700" role="progressbar"
                     x-bind:aria-valuenow="Math.round(((currentIndex + 1) / {{ count($questions) }}) * 100)"
                     aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar-fill" x-bind:style="`width: ${((currentIndex + 1) / {{ count($questions) }}) * 100}%`"></div>
                </div>
            </div>

            <form method="POST" action="{{ route('assessments.submit', $assessment->slug) }}" x-ref="assessmentForm">
                @csrf

                <template x-for="(question, idx) in questions" :key="question.code">
                    <div x-show="currentIndex === idx" x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                        <fieldset>
                            <legend class="mb-6">
                                <p class="text-xs font-medium uppercase tracking-wider text-ink-muted" x-text="'#' + question.code"></p>
                                <h3 class="mt-1 text-lg font-semibold text-ink dark:text-white" x-text="question.question"></h3>
                            </legend>

                            <div class="space-y-3">
                                <template x-for="(option, optIdx) in question.options" :key="option.id">
                                    <label class="flex cursor-pointer items-center gap-4 rounded-xl border border-gray-200 p-4 transition-all duration-150 hover:border-sky-200 hover:bg-sky-50/50 dark:border-gray-600 dark:hover:border-sky-600 dark:hover:bg-navy-700"
                                           x-bind:class="{ 'border-sky-500 bg-sky-50 ring-1 ring-sky-500 dark:border-sky-400 dark:bg-navy-700': answers[question.code] === option.id }">
                                        <input type="radio"
                                               x-bind:name="`answers[${question.code}]`"
                                               x-bind:value="option.id"
                                               x-model="answers[question.code]"
                                               class="h-4 w-4 border-gray-300 text-sky-600 focus:ring-sky-500 dark:border-gray-600 dark:bg-navy-700">
                                        <span class="text-sm text-ink-muted dark:text-gray-300" x-text="option.text"></span>
                                    </label>
                                </template>
                            </div>
                        </fieldset>
                    </div>
                </template>

                <div class="mt-8 flex items-center justify-between border-t border-gray-200 pt-6 dark:border-gray-700">
                    <button type="button"
                            class="btn-ghost inline-flex items-center gap-2"
                            x-show="currentIndex > 0"
                            @click="prevQuestion">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        {{ __('Sebelumnya') }}
                    </button>
                    <div x-show="currentIndex === 0"></div>

                    <button type="button"
                            class="btn-primary inline-flex items-center gap-2"
                            x-show="currentIndex < questions.length - 1"
                            @click="nextQuestion">
                        {{ __('Berikutnya') }}
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>

                    <button type="submit"
                            class="btn-primary inline-flex items-center gap-2"
                            x-show="currentIndex === questions.length - 1">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('Kirim Jawaban') }}
                    </button>
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
                        alert('{{ __('Please select an answer before continuing.') }}');
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
                    this.$watch('currentIndex', (val) => {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    });
                }
            }
        }
    </script>
</x-layouts.assessment>
