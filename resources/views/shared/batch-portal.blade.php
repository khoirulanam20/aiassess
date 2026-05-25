<x-guest-minimal-layout>
    <div class="mx-auto max-w-5xl px-6 py-10">
        <div class="mb-8 text-center">
            <h1 class="text-2xl font-bold text-ink dark:text-gray-100">{{ $share->batch->name }}</h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Halo :name, silakan kerjakan assessment berikut.', ['name' => $share->user->name]) }}
            </p>
        </div>

        @if (session('success'))
            <div class="alert-success mb-6" role="alert">
                <svg class="alert-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="alert-content">{{ session('success') }}</div>
            </div>
        @endif
        @if (session('error'))
            <div class="alert-danger mb-6" role="alert">
                <svg class="alert-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="alert-content">{{ session('error') }}</div>
            </div>
        @endif

        @if ($pending->isNotEmpty())
            <div class="mb-8">
                <h2 class="mb-4 text-lg font-semibold text-ink dark:text-gray-100">{{ __('Harus dikerjakan') }} ({{ $pending->count() }})</h2>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($pending as $assignment)
                        @php $assessment = $assignment->assessment; @endphp
                        <div class="assessment-card">
                            <div class="mb-4 flex items-start justify-between">
                                <div class="ac-icon" style="background-color: {{ $assessment->color }}15; color: {{ $assessment->color }}">
                                    {{ strtoupper(substr($assessment->slug, 0, 2)) }}
                                </div>
                                <span class="badge-amber">{{ __('Menunggu') }}</span>
                            </div>
                            <h4 class="mb-1.5 font-semibold text-ink dark:text-gray-100">{{ $assessment->name }}</h4>
                            <p class="mb-4 line-clamp-2 text-sm text-gray-500 dark:text-gray-400">{{ $assessment->description }}</p>
                            <div class="divider pt-4">
                                <x-ui.button-icon
                                    :href="route('shared.batch.take', [$token, $assessment->slug])"
                                    variant="solid"
                                    icon="play"
                                    class="w-full justify-center"
                                >
                                    {{ __('Kerjakan') }}
                                </x-ui.button-icon>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($completed->isNotEmpty())
            <div>
                <h2 class="mb-4 text-lg font-semibold text-ink dark:text-gray-100">{{ __('Sudah selesai') }} ({{ $completed->count() }})</h2>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($completed as $assignment)
                        @php $assessment = $assignment->assessment; @endphp
                        <div class="card border-green-200 bg-green-50/50 dark:border-green-800 dark:bg-green-900/20">
                            <div class="mb-2 flex items-center justify-between">
                                <h4 class="font-semibold text-ink dark:text-gray-100">{{ $assessment->name }}</h4>
                                <span class="badge-green">{{ __('Selesai') }}</span>
                            </div>
                            <p class="mb-3 text-xs text-gray-500 dark:text-gray-400">
                                {{ $assignment->completed_at?->format('d M Y H:i') }}
                            </p>
                            @if ($assignment->assessment_result_id)
                                <x-ui.icon-action
                                    :href="route('shared.batch.result', [$token, $assessment->slug, $assignment->assessment_result_id])"
                                    variant="primary"
                                    :title="__('Lihat ringkasan hasil')"
                                >
                                    <x-ui.icon name="chart" />
                                </x-ui.icon-action>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($pending->isEmpty() && $completed->isEmpty())
            <div class="card text-center text-sm text-gray-500 dark:text-gray-400">
                {{ __('Tidak ada penugasan assessment.') }}
            </div>
        @endif
    </div>
</x-guest-minimal-layout>
