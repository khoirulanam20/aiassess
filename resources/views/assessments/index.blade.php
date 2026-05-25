<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-ink dark:text-white">{{ __('Assessments') }}</h1>
                <p class="mt-1 text-sm text-ink-muted">{{ __('Choose an assessment to discover more about yourself') }}</p>
            </div>
            <x-ui.button type="button" variant="primary" @click="$dispatch('open-assessment-modal')">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ __('Assessment Baru') }}
            </x-ui.button>
        </div>
    </x-slot>

    <div x-data="assessmentModal()" x-init="initModal()">
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($assessments as $assessment)
                <x-assessment.card :assessment="$assessment" />
            @endforeach
        </div>

        @if ($assessments->hasPages())
            <div class="mt-8">
                {{ $assessments->links() }}
            </div>
        @endif

        <div x-show="open" x-cloak role="dialog" aria-modal="true" aria-labelledby="modal-title"
             class="fixed inset-0 z-50 flex items-center justify-center px-4"
             x-transition:enter="transition duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="fixed inset-0 bg-navy-500/40 backdrop-blur-sm dark:bg-navy-900/60" @click="open = false"></div>
            <div class="relative w-full max-w-3xl rounded-2xl bg-white p-8 shadow-dropdown dark:bg-navy-800"
                 x-transition:enter="transition duration-200"
                 x-transition:enter-start="scale-95 opacity-0"
                 x-transition:enter-end="scale-100 opacity-100"
                 x-transition:leave="transition duration-150"
                 x-transition:leave-start="scale-100 opacity-100"
                 x-transition:leave-end="scale-95 opacity-0">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 id="modal-title" class="text-xl font-bold text-ink dark:text-white">{{ __('New Assessment') }}</h2>
                        <p class="mt-1 text-sm text-ink-muted">{{ __('Select an assessment type to begin') }}</p>
                    </div>
                    <button type="button" @click="open = false" class="rounded-lg p-2 text-ink-light hover:bg-gray-100 hover:text-ink dark:hover:bg-navy-700" aria-label="{{ __('Close') }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($allAssessments as $item)
                        <a href="{{ route('assessments.show', $item->slug) }}"
                           class="group card card-hover"
                           @click="open = false">
                            <div class="mb-3 flex items-center gap-3">
                                <span class="flex h-10 w-10 items-center justify-center rounded-lg text-sm font-bold transition-transform duration-200 group-hover:scale-110"
                                      style="background-color: {{ $item->color }}15; color: {{ $item->color }}">
                                    {{ substr($item->name, 0, 2) }}
                                </span>
                                @if ($item->type === 'premium')
                                    <span class="badge-amber text-[10px]">{{ __('Premium') }}</span>
                                @else
                                    <span class="badge-green text-[10px]">{{ __('Free') }}</span>
                                @endif
                            </div>
                            <h4 class="mb-1 font-semibold text-ink dark:text-white">{{ $item->name }}</h4>
                            <p class="mb-3 text-xs leading-relaxed text-ink-muted line-clamp-2">{{ $item->description }}</p>
                            <div class="flex items-center gap-3 text-[11px] text-ink-light">
                                <span class="flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    {{ $item->question_count }} {{ __('questions') }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $item->cooldown_days }}d
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function assessmentModal() {
            return {
                open: false,
                initModal() {
                    window.addEventListener('open-assessment-modal', () => {
                        this.open = true;
                    });
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape') this.open = false;
                    });
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
