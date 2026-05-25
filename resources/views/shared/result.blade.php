<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - {{ __('Shared Result') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .share-header { background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%); }
    </style>
</head>
<body class="bg-surface-secondary">
    <div class="share-header border-b border-gray-700/50">
        <div class="mx-auto flex h-14 max-w-4xl items-center justify-between px-6">
            <div class="flex items-center gap-3">
                <span class="text-sm font-medium text-white">{{ config('app.name') }}</span>
            </div>
            <span class="text-xs text-gray-400">{{ __('Shared Result') }}</span>
        </div>
    </div>

    <main class="mx-auto max-w-4xl px-6 py-8">
        <div class="space-y-6">
            <div class="card text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-sky-100">
                    <svg class="h-8 w-8 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-ink">{{ $result->assessment?->name ?? $result->test_name }}</h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Completed by :name on :date', [
                        'name' => $result->user?->name ?? __('Anonymous'),
                        'date' => $result->created_at->format('d F Y H:i')
                    ]) }}
                </p>
            </div>

            @php
                $decodedResult = json_decode($result->result, true) ?? [];
                $decodedScores = json_decode($result->scores, true) ?? [];
                $hasResult = !empty($decodedResult) || !empty($decodedScores);
            @endphp

            @php $assessmentSlug = $result->assessment?->slug ?? $result->test_name; @endphp

            @if ($hasResult)
                @if ($assessmentSlug === 'papikostick')
                    @include('assessments.partials.result-papikostick')
                @elseif ($assessmentSlug === 'msdt')
                    @include('assessments.partials.result-msdt')
                @elseif ($assessmentSlug === 'spm')
                    @include('assessments.partials.result-spm')
                @elseif ($assessmentSlug === 'business-insight')
                    @include('assessments.partials.result-business-insight')
                @elseif ($assessmentSlug === 'agility')
                    @include('assessments.partials.result-agility')
                @elseif (!empty($decodedScores))
                    <div class="card">
                        <h3 class="card-title">{{ __('Dimension Scores') }}</h3>
                        <div class="mt-4 space-y-4">
                            @foreach ($decodedScores as $dimension => $scores)
                                @if (is_array($scores))
                                    <div>
                                        <h4 class="mb-2 text-sm font-semibold text-ink">{{ $dimension }}</h4>
                                        <div class="space-y-2">
                                            @foreach ($scores as $pole => $percentage)
                                                <div>
                                                    <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                                        <span>{{ $pole }}</span>
                                                        <span>{{ $percentage }}%</span>
                                                    </div>
                                                    <div class="progress-bar">
                                                        <div class="progress-bar-fill" style="width: {{ $percentage }}%"></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (!empty($decodedResult))
                    @if (!empty($decodedResult['type']))
                        <div class="card text-center">
                            <h3 class="card-title mb-2">{{ __('Your Type') }}</h3>
                            <div class="inline-flex items-center justify-center rounded-2xl bg-sky-50 px-8 py-4">
                                <span class="text-3xl font-bold tracking-wider text-sky-600">{{ $decodedResult['type'] }}</span>
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
                            <ul class="mt-3 space-y-2">
                                @foreach ($decodedResult['strengths'] as $strength)
                                    <li class="flex items-center gap-2 text-sm text-gray-600">
                                        <svg class="h-4 w-4 shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        {{ $strength }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (!empty($decodedResult['weaknesses']))
                        <div class="card">
                            <h3 class="card-title">{{ __('Areas for Development') }}</h3>
                            <ul class="mt-3 space-y-2">
                                @foreach ($decodedResult['weaknesses'] as $weakness)
                                    <li class="flex items-center gap-2 text-sm text-gray-600">
                                        <svg class="h-4 w-4 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                        </svg>
                                        {{ $weakness }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (!empty($decodedResult['career_paths']))
                        <div class="card">
                            <h3 class="card-title">{{ __('Suggested Career Paths') }}</h3>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($decodedResult['career_paths'] as $career)
                                    <span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-medium text-sky-600">{{ $career }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
            @else
                <div class="card text-center py-12">
                    <div class="inline-flex items-center justify-center rounded-2xl bg-gray-50 p-4">
                        <svg class="h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-ink">{{ __('Result Not Available') }}</h3>
                    <p class="mt-2 text-sm text-gray-500">{{ __('The result data is not available for this assessment.') }}</p>
                </div>
            @endif

            <div class="text-center">
                <p class="text-xs text-gray-400">
                    {{ __('Powered by :app', ['app' => config('app.name')]) }}
                </p>
            </div>
        </div>
    </main>
</body>
</html>
