<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('assessments.index') }}" class="btn-ghost btn-sm inline-flex items-center gap-1">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    {{ __('Kembali') }}
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-ink dark:text-white">{{ $assessment->name }}</h1>
                    <p class="mt-1 text-sm text-ink-muted">{{ __('Assessment Detail') }}</p>
                </div>
            </div>
            @if ($assessment->type === 'premium')
                <span class="badge-amber">{{ __('Premium') }}</span>
            @else
                <span class="badge-green">{{ __('Free') }}</span>
            @endif
        </div>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="card">
                <h3 class="card-title dark:text-white">{{ __('About This Assessment') }}</h3>
                <p class="mt-3 text-sm leading-relaxed text-ink-muted">{{ $assessment->description }}</p>

                <div class="mt-6 grid grid-cols-3 gap-4 rounded-lg bg-surface-secondary p-4 dark:bg-navy-700">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-ink dark:text-white">{{ $assessment->question_count }}</p>
                        <p class="text-xs text-ink-muted">{{ __('Questions') }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-ink dark:text-white">{{ $assessment->cooldown_days }}</p>
                        <p class="text-xs text-ink-muted">{{ __('Cooldown Days') }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-ink dark:text-white">{{ $assessment->type === 'free' ? __('Free') : __('Premium') }}</p>
                        <p class="text-xs text-ink-muted">{{ __('Price') }}</p>
                    </div>
                </div>
            </div>

            @if ($assessment->instructions)
                <div class="card">
                    <h3 class="card-title dark:text-white">{{ __('Instructions') }}</h3>
                    <div class="mt-3 prose prose-sm max-w-none text-ink-muted dark:prose-invert">
                        {{ $assessment->instructions }}
                    </div>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            @if ($latestResult)
                <div class="card border-l-4 border-l-amber-400">
                    <div class="flex items-start gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-600 dark:bg-navy-700 dark:text-amber-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-semibold text-ink dark:text-white">{{ __('Previous Result') }}</h4>
                            <p class="mt-1 text-xs text-ink-muted">{{ __('Completed :date', ['date' => $latestResult->created_at->format('d M Y')]) }}</p>
                            <div class="mt-3">
                                <a href="{{ route('assessments.result', [$assessment->slug, $latestResult->id]) }}"
                                   class="btn-primary btn-sm inline-flex items-center gap-1" title="{{ __('Lihat hasil sebelumnya') }}">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    {{ __('Lihat') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card text-center">
                <div class="mb-4 flex justify-center">
                    <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-sky-50 text-sky-500 dark:bg-navy-700 dark:text-sky-400">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <h3 class="text-base font-semibold text-ink dark:text-white">{{ __('Ready to Start?') }}</h3>
                <p class="mt-1 text-xs text-ink-muted">{{ __('Estimated time: 15-30 minutes') }}</p>
                <a href="{{ route('assessments.take', $assessment->slug) }}" class="btn-primary mt-4 inline-flex w-full items-center justify-center gap-2 py-3">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
                    {{ __('Mulai Assessment') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
