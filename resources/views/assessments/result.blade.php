<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('results.history') }}" class="btn-ghost btn-sm inline-flex items-center gap-1">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    {{ __('Riwayat') }}
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-ink dark:text-white">{{ __('Assessment Result') }}</h1>
                    <p class="mt-1 text-sm text-ink-muted">{{ $assessment->name }}</p>
                </div>
            </div>
            @can('results.share_create')
            @if ($result->share_token)
                <div x-data="{
                    shareResult() {
                        const url = '{{ route('shared.result.form', $result->share_token) }}';
                        if (navigator.share) {
                            navigator.share({ title: '{{ __('Assessment Result') }}', url: url });
                        } else {
                            navigator.clipboard.writeText(url);
                            alert('{{ __('Link copied to clipboard!') }}');
                        }
                    }
                }">
                    <button type="button" @click="shareResult()" class="btn-primary btn-sm inline-flex items-center gap-1" title="{{ __('Share hasil') }}">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                        {{ __('Share') }}
                    </button>
                </div>
            @endif
            @endcan
        </div>
    </x-slot>

    @php
        $decodedResult = json_decode($result->result, true) ?? [];
        $decodedScores = json_decode($result->scores, true) ?? [];
        $hasResult = !empty($decodedResult) || !empty($decodedScores);
    @endphp

    <div class="mx-auto max-w-3xl space-y-6">
        <div class="card text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-100 dark:bg-navy-700">
                <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h2 class="text-xl font-bold text-ink dark:text-white">{{ __('Assessment Completed') }}</h2>
            <p class="mt-1 text-sm text-ink-muted">{{ __('Completed on :date', ['date' => $result->created_at->format('d F Y H:i')]) }}</p>
        </div>

        @if ($hasResult)
            @php $assessmentSlug = $result->assessment?->slug ?? $result->test_name; @endphp

            @if (in_array($assessmentSlug, ['papikostick', 'msdt', 'spm', 'business-insight', 'agility']))
                @include("assessments.partials.result-{$assessmentSlug}")
            @elseif (!empty($decodedScores) && isset($decodedScores['EI']))
                <div class="card">
                    <h3 class="card-title dark:text-white">{{ __('Personality Dimensions') }}</h3>
                    <div class="mt-4 space-y-5">
                        @foreach (['EI' => ['E', 'I'], 'SN' => ['S', 'N'], 'TF' => ['T', 'F'], 'JP' => ['J', 'P']] as $dim => $poles)
                            <div>
                                <div class="mb-2 flex items-center justify-between">
                                    <span class="text-xs font-semibold uppercase tracking-wider text-ink-muted">{{ $dim }}</span>
                                    <span class="text-xs text-ink-light">{{ $decodedScores[$dim][$poles[0]] }}% / {{ $decodedScores[$dim][$poles[1]] }}%</span>
                                </div>
                                <div class="relative h-3 rounded-full bg-surface-muted overflow-hidden dark:bg-navy-700">
                                    <div class="absolute inset-y-0 left-0 rounded-full transition-all duration-500"
                                         style="width: {{ $decodedScores[$dim][$poles[0]] }}%; background: linear-gradient(90deg, #0369A1, #0EA5E9);"></div>
                                </div>
                                <div class="mt-1 flex justify-between text-xs">
                                    <span class="font-medium text-sky-600 dark:text-sky-400">{{ $poles[0] }}</span>
                                    <span class="font-medium text-ink-light">{{ $poles[1] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @elseif (!empty($decodedScores) && isset($decodedScores['D']))
                <div class="card">
                    <h3 class="card-title dark:text-white">{{ __('Dimension Scores') }}</h3>
                    <div class="mt-4 space-y-4">
                        @foreach (['D' => 'Dominance', 'I' => 'Influence', 'S' => 'Steadiness', 'C' => 'Conscientiousness'] as $type => $label)
                            <div>
                                <div class="flex items-center justify-between text-sm mb-1">
                                    <span class="font-medium text-ink dark:text-white">{{ $label }} ({{ $type }})</span>
                                    <span class="text-ink-muted">{{ $decodedScores[$type] ?? 0 }}%</span>
                                </div>
                                <div class="progress-bar dark:bg-navy-700">
                                    <div class="progress-bar-fill transition-all duration-500"
                                         style="width: {{ $decodedScores[$type] ?? 0 }}%;
                                         background: {{ $type === 'D' ? '#EF4444' : ($type === 'I' ? '#F59E0B' : ($type === 'S' ? '#10B981' : '#3B82F6')) }};">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @elseif (!empty($decodedScores))
                <div class="card">
                    <h3 class="card-title dark:text-white">{{ __('Scores') }}</h3>
                    <div class="mt-4 space-y-4">
                        @foreach ($decodedScores as $key => $value)
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-ink dark:text-white">{{ $key }}</span>
                                <span class="text-sm text-ink-muted">{{ is_numeric($value) ? $value . '%' : (is_array($value) ? json_encode($value) : $value) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (!empty($decodedResult['type']))
                <div class="card py-8 text-center">
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-ink-muted">{{ __('Your Personality Type') }}</p>
                    <div class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-br from-sky-50 to-blue-50 px-10 py-5 shadow-inner dark:from-navy-700 dark:to-navy-600">
                        <span class="text-4xl font-bold tracking-widest text-sky-600 dark:text-sky-400">{{ $decodedResult['type'] }}</span>
                    </div>
                    @if (!empty($decodedResult['type_name']))
                        <p class="mt-3 text-lg font-semibold text-ink dark:text-white">{{ $decodedResult['type_name'] }}</p>
                    @endif
                </div>
            @endif

            @if (!empty($decodedResult['description']))
                <div class="card">
                    <h3 class="card-title dark:text-white">{{ __('Description') }}</h3>
                    <p class="mt-3 text-sm leading-relaxed text-ink-muted">{{ $decodedResult['description'] }}</p>
                </div>
            @endif

            @if (!empty($decodedResult['strengths']))
                <div class="card">
                    <h3 class="card-title dark:text-white">{{ __('Strengths') }}</h3>
                    <ul class="mt-3 grid gap-2 sm:grid-cols-2">
                        @foreach ($decodedResult['strengths'] as $strength)
                            <li class="flex items-center gap-2 rounded-lg bg-green-50 px-3 py-2 text-sm text-gray-700 dark:bg-navy-700 dark:text-gray-300">
                                <svg class="h-4 w-4 shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                {{ $strength }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (!empty($decodedResult['weaknesses']))
                <div class="card">
                    <h3 class="card-title dark:text-white">{{ __('Areas for Development') }}</h3>
                    <ul class="mt-3 grid gap-2 sm:grid-cols-2">
                        @foreach ($decodedResult['weaknesses'] as $weakness)
                            <li class="flex items-center gap-2 rounded-lg bg-amber-50 px-3 py-2 text-sm text-gray-700 dark:bg-navy-700 dark:text-gray-300">
                                <svg class="h-4 w-4 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                {{ $weakness }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (!empty($decodedResult['communication_style']))
                <div class="card">
                    <h3 class="card-title dark:text-white">{{ __('Communication Style') }}</h3>
                    <p class="mt-3 text-sm text-ink-muted">{{ $decodedResult['communication_style'] }}</p>
                </div>
            @endif

            @if (!empty($decodedResult['motivations']))
                <div class="card">
                    <h3 class="card-title dark:text-white">{{ __('Motivations') }}</h3>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ($decodedResult['motivations'] as $motivation)
                            <span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-medium text-sky-600 dark:bg-navy-700 dark:text-sky-400">{{ $motivation }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (!empty($decodedResult['career_paths']))
                <div class="card">
                    <h3 class="card-title dark:text-white">{{ __('Suggested Career Paths') }}</h3>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ($decodedResult['career_paths'] as $career)
                            <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-600 dark:bg-navy-700 dark:text-indigo-400">{{ $career }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            <div class="card py-12 text-center">
                <div class="inline-flex items-center justify-center rounded-2xl bg-surface-muted p-4 dark:bg-navy-700">
                    <svg class="h-12 w-12 text-ink-light" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-ink dark:text-white">{{ __('Results Processing') }}</h3>
                <p class="mx-auto mt-2 max-w-sm text-sm text-ink-muted">{{ __('Your result is being processed. Please check back shortly.') }}</p>
            </div>
        @endif

        <div class="flex justify-center gap-3">
            <a href="{{ route('assessments.index') }}" class="btn-secondary inline-flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                {{ __('Assessments') }}
            </a>
            <a href="{{ route('results.history') }}" class="btn-primary inline-flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                {{ __('Riwayat') }}
            </a>
        </div>
    </div>
</x-app-layout>
