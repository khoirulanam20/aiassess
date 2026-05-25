<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <x-ui.button-icon :href="route('results.history')" variant="ghost" icon="arrow-left">
                    {{ __('Riwayat') }}
                </x-ui.button-icon>
                <div>
                    <h1 class="text-2xl font-bold text-ink">{{ __('Assessment Result') }}</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ $assessment->name }}</p>
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
                    <x-ui.icon-action type="button" @click="shareResult()" variant="primary" :title="__('Share hasil')">
                        <x-ui.icon name="share" />
                    </x-ui.icon-action>
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
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-100">
                <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h2 class="text-xl font-bold text-ink">{{ __('Assessment Completed') }}</h2>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('Completed on :date', ['date' => $result->created_at->format('d F Y H:i')]) }}
            </p>
        </div>

        @if ($hasResult)
            @if (!empty($decodedScores) && isset($decodedScores['EI']))
                <div class="card">
                    <h3 class="card-title">{{ __('Personality Dimensions') }}</h3>
                    <div class="mt-4 space-y-5">
                        @foreach (['EI' => ['E', 'I'], 'SN' => ['S', 'N'], 'TF' => ['T', 'F'], 'JP' => ['J', 'P']] as $dim => $poles)
                            <div>
                                <div class="mb-2 flex items-center justify-between">
                                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ $dim }}</span>
                                    <span class="text-xs text-gray-400">{{ $decodedScores[$dim][$poles[0]] }}% / {{ $decodedScores[$dim][$poles[1]] }}%</span>
                                </div>
                                <div class="relative h-3 rounded-full bg-gray-100 overflow-hidden">
                                    <div class="absolute inset-y-0 left-0 rounded-full transition-all duration-500"
                                         style="width: {{ $decodedScores[$dim][$poles[0]] }}%; background: linear-gradient(90deg, #0369A1, #0EA5E9);"></div>
                                </div>
                                <div class="mt-1 flex justify-between text-xs">
                                    <span class="font-medium text-sky-600">{{ $poles[0] }}</span>
                                    <span class="font-medium text-gray-400">{{ $poles[1] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @elseif (!empty($decodedScores) && isset($decodedScores['D']))
                <div class="card">
                    <h3 class="card-title">{{ __('Dimension Scores') }}</h3>
                    <div class="mt-4 space-y-4">
                        @foreach (['D' => 'Dominance', 'I' => 'Influence', 'S' => 'Steadiness', 'C' => 'Conscientiousness'] as $type => $label)
                            <div>
                                <div class="flex items-center justify-between text-sm mb-1">
                                    <span class="font-medium text-ink">{{ $label }} ({{ $type }})</span>
                                    <span class="text-gray-500">{{ $decodedScores[$type] ?? 0 }}%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-bar-fill transition-all duration-500"
                                         style="width: {{ $decodedScores[$type] ?? 0 }}%;
                                         background: {{ $type === 'D' ? '#EF4444' : ($type === 'I' ? '#F59E0B' : ($type === 'S' ? '#10B981' : '#3B82F6')) }};">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @elseif ($assessment->slug === 'papikostick')
                @include('assessments.partials.result-papikostick')
            @elseif ($assessment->slug === 'msdt')
                @include('assessments.partials.result-msdt')
            @elseif ($assessment->slug === 'spm')
                @include('assessments.partials.result-spm')
            @elseif ($assessment->slug === 'business-insight')
                @include('assessments.partials.result-business-insight')
            @elseif ($assessment->slug === 'agility')
                @include('assessments.partials.result-agility')
            @elseif (!empty($decodedScores))
                <div class="card">
                    <h3 class="card-title">{{ __('Scores') }}</h3>
                    <div class="mt-4 space-y-4">
                        @foreach ($decodedScores as $key => $value)
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-ink">{{ $key }}</span>
                                <span class="text-sm text-gray-500">{{ is_numeric($value) ? $value . '%' : (is_array($value) ? json_encode($value) : $value) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (!empty($decodedResult['type']))
                <div class="card text-center py-8">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">{{ __('Your Personality Type') }}</p>
                    <div class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-br from-sky-50 to-blue-50 px-10 py-5 shadow-inner">
                        <span class="text-4xl font-bold tracking-widest text-sky-600">{{ $decodedResult['type'] }}</span>
                    </div>
                    @if (!empty($decodedResult['type_name']))
                        <p class="mt-3 text-lg font-semibold text-ink">{{ $decodedResult['type_name'] }}</p>
                    @endif
                </div>
            @endif

            @if (!empty($decodedResult['description']))
                <div class="card">
                    <h3 class="card-title">{{ __('Description') }}</h3>
                    <p class="mt-3 text-sm leading-relaxed text-gray-600">{{ $decodedResult['description'] }}</p>
                </div>
            @endif

            @if (!empty($decodedResult['strengths']))
                <div class="card">
                    <h3 class="card-title">{{ __('Strengths') }}</h3>
                    <ul class="mt-3 grid gap-2 sm:grid-cols-2">
                        @foreach ($decodedResult['strengths'] as $strength)
                            <li class="flex items-center gap-2 rounded-lg bg-green-50 px-3 py-2 text-sm text-gray-700">
                                <svg class="h-4 w-4 shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                {{ $strength }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (!empty($decodedResult['weaknesses']))
                <div class="card">
                    <h3 class="card-title">{{ __('Areas for Development') }}</h3>
                    <ul class="mt-3 grid gap-2 sm:grid-cols-2">
                        @foreach ($decodedResult['weaknesses'] as $weakness)
                            <li class="flex items-center gap-2 rounded-lg bg-amber-50 px-3 py-2 text-sm text-gray-700">
                                <svg class="h-4 w-4 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                {{ $weakness }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (!empty($decodedResult['communication_style']))
                <div class="card">
                    <h3 class="card-title">{{ __('Communication Style') }}</h3>
                    <p class="mt-3 text-sm text-gray-600">{{ $decodedResult['communication_style'] }}</p>
                </div>
            @endif

            @if (!empty($decodedResult['motivations']))
                <div class="card">
                    <h3 class="card-title">{{ __('Motivations') }}</h3>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ($decodedResult['motivations'] as $motivation)
                            <span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-medium text-sky-600">{{ $motivation }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (!empty($decodedResult['career_paths']))
                <div class="card">
                    <h3 class="card-title">{{ __('Suggested Career Paths') }}</h3>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ($decodedResult['career_paths'] as $career)
                            <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-600">{{ $career }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            <div class="card py-12 text-center">
                <div class="inline-flex items-center justify-center rounded-2xl bg-gray-50 p-4">
                    <svg class="h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-ink">{{ __('Results Processing') }}</h3>
                <p class="mt-2 text-sm text-gray-500 max-w-sm mx-auto">
                    {{ __('Your result is being processed. Please check back shortly.') }}
                </p>
            </div>
        @endif

        <div class="flex justify-center gap-3">
            <x-ui.button-icon :href="route('assessments.index')" variant="secondary" icon="squares">
                {{ __('Assessment') }}
            </x-ui.button-icon>
            <x-ui.button-icon :href="route('results.history')" variant="primary" icon="chart">
                {{ __('Riwayat') }}
            </x-ui.button-icon>
        </div>
    </div>
</x-app-layout>
