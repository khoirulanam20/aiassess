<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <x-ui.button-icon :href="route('assessments.index')" variant="ghost" icon="arrow-left">
                    {{ __('Daftar assessment') }}
                </x-ui.button-icon>
                <div>
                    <h1 class="text-2xl font-bold text-ink">{{ $assessment->name }}</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Assessment Detail') }}</p>
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
        <div class="lg:col-span-2 space-y-6">
            <div class="card">
                <h3 class="card-title">{{ __('About This Assessment') }}</h3>
                <p class="mt-3 text-sm leading-relaxed text-gray-600">{{ $assessment->description }}</p>

                <div class="mt-6 grid grid-cols-3 gap-4 rounded-lg bg-gray-50 p-4">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-ink">{{ $assessment->question_count }}</p>
                        <p class="text-xs text-gray-500">{{ __('Questions') }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-ink">{{ $assessment->cooldown_days }}</p>
                        <p class="text-xs text-gray-500">{{ __('Cooldown Days') }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-ink">{{ $assessment->type === 'free' ? __('Free') : 'Rp 30.000' }}</p>
                        <p class="text-xs text-gray-500">{{ __('Price') }}</p>
                    </div>
                </div>
            </div>

            @if ($assessment->instructions)
                <div class="card">
                    <h3 class="card-title">{{ __('Instructions') }}</h3>
                    <div class="mt-3 prose prose-sm max-w-none text-gray-600">
                        {{ $assessment->instructions }}
                    </div>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            @if ($latestResult)
                <div class="card border-l-4 border-l-amber-400">
                    <div class="flex items-start gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-semibold text-ink">{{ __('Previous Result') }}</h4>
                            <p class="mt-1 text-xs text-gray-500">{{ __('Completed :date', ['date' => $latestResult->created_at->format('d M Y')]) }}</p>
                            <div class="mt-3">
                                <x-ui.icon-action
                                    :href="route('assessments.result', [$assessment->slug, $latestResult->id])"
                                    variant="primary"
                                    :title="__('Lihat hasil sebelumnya')"
                                >
                                    <x-ui.icon name="eye" />
                                </x-ui.icon-action>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card text-center">
                <div class="flex justify-center mb-4">
                    <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-sky-50 text-sky-500">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <h3 class="text-base font-semibold text-ink">{{ __('Ready to Start?') }}</h3>
                <p class="mt-1 text-xs text-gray-500">{{ __('Estimated time: 15-30 minutes') }}</p>
                <x-ui.button-icon
                    :href="route('assessments.take', $assessment->slug)"
                    variant="solid"
                    icon="play"
                    class="mt-4 w-full justify-center py-3"
                >
                    {{ __('Mulai Assessment') }}
                </x-ui.button-icon>
            </div>
        </div>
    </div>
</x-app-layout>
