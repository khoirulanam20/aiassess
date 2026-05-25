@props(['result'])

@php
    $assessment = $result->assessment;
    $assessmentSlug = $assessment?->slug ?? $result->test_name;
    $decodedResult = json_decode($result->result, true) ?? [];
    $decodedScores = json_decode($result->scores, true) ?? [];
    $hasResult = ! empty($decodedResult) || ! empty($decodedScores);
@endphp

@if ($hasResult)
    <div class="space-y-6">
        {{-- Skor MBTI --}}
        @if (! empty($decodedScores) && isset($decodedScores['EI']))
            <div class="card dark:border-gray-700 dark:bg-navy-800">
                <h3 class="card-title dark:text-white">Dimensi Kepribadian</h3>
                <div class="mt-4 space-y-5">
                    @foreach (['EI' => ['E', 'I'], 'SN' => ['S', 'N'], 'TF' => ['T', 'F'], 'JP' => ['J', 'P']] as $dim => $poles)
                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $dim }}</span>
                                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $decodedScores[$dim][$poles[0]] }}% / {{ $decodedScores[$dim][$poles[1]] }}%</span>
                            </div>
                            <div class="relative h-3 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700" role="progressbar" aria-valuenow="{{ $decodedScores[$dim][$poles[0]] }}" aria-valuemin="0" aria-valuemax="100" aria-label="{{ $poles[0] }}: {{ $decodedScores[$dim][$poles[0]] }}%, {{ $poles[1] }}: {{ $decodedScores[$dim][$poles[1]] }}%">
                                <div class="absolute inset-y-0 left-0 rounded-full transition-all duration-500"
                                     style="width: {{ $decodedScores[$dim][$poles[0]] }}%; background: linear-gradient(90deg, #0369A1, #0EA5E9);"></div>
                            </div>
                            <div class="mt-1 flex justify-between text-xs">
                                <span class="font-medium text-sky-600 dark:text-sky-400">{{ $poles[0] }}</span>
                                <span class="font-medium text-gray-400 dark:text-gray-500">{{ $poles[1] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        {{-- Skor DISC --}}
        @elseif (! empty($decodedScores) && isset($decodedScores['D']))
            <div class="card dark:border-gray-700 dark:bg-navy-800">
                <h3 class="card-title dark:text-white">Skor Dimensi</h3>
                <div class="mt-4 space-y-4">
                    @foreach (['D' => 'Dominance', 'I' => 'Influence', 'S' => 'Steadiness', 'C' => 'Conscientiousness'] as $type => $label)
                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span class="font-medium text-ink dark:text-white">{{ $label }} ({{ $type }})</span>
                                <span class="text-gray-500 dark:text-gray-400">{{ $decodedScores[$type] ?? 0 }}%</span>
                            </div>
                            <div class="progress-bar dark:bg-gray-700" role="progressbar" aria-valuenow="{{ $decodedScores[$type] ?? 0 }}" aria-valuemin="0" aria-valuemax="100" aria-label="{{ $label }}: {{ $decodedScores[$type] ?? 0 }}%">
                                <div class="progress-bar-fill transition-all duration-500"
                                     style="width: {{ $decodedScores[$type] ?? 0 }}%; background: {{ $type === 'D' ? '#EF4444' : ($type === 'I' ? '#F59E0B' : ($type === 'S' ? '#10B981' : '#3B82F6')) }};">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        @elseif ($assessmentSlug === 'papikostick')
            @include('assessments.partials.result-papikostick')
        @elseif ($assessmentSlug === 'msdt')
            @include('assessments.partials.result-msdt')
        @elseif ($assessmentSlug === 'spm')
            @include('assessments.partials.result-spm')
        @elseif ($assessmentSlug === 'business-insight')
            @include('assessments.partials.result-business-insight')
        @elseif ($assessmentSlug === 'agility')
            @include('assessments.partials.result-agility')
        @elseif (! empty($decodedScores))
            <div class="card dark:border-gray-700 dark:bg-navy-800">
                <h3 class="card-title dark:text-white">Skor</h3>
                <div class="mt-4 space-y-4">
                    @foreach ($decodedScores as $key => $value)
                        @if (is_array($value))
                            <div>
                                <h4 class="mb-2 text-sm font-semibold text-ink dark:text-white">{{ $key }}</h4>
                                <div class="space-y-2">
                                    @foreach ($value as $pole => $percentage)
                                        <div>
                                            <div class="mb-1 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                                <span>{{ $pole }}</span>
                                                <span>{{ is_numeric($percentage) ? $percentage.'%' : $percentage }}</span>
                                            </div>
                                            @if (is_numeric($percentage))
                                                <div class="progress-bar dark:bg-gray-700" role="progressbar" aria-valuenow="{{ min(100, (float) $percentage) }}" aria-valuemin="0" aria-valuemax="100" aria-label="{{ $pole }}: {{ $percentage }}%">
                                                    <div class="progress-bar-fill" style="width: {{ min(100, (float) $percentage) }}%"></div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-medium text-ink dark:text-white">{{ $key }}</span>
                                <span class="text-gray-500 dark:text-gray-400">{{ is_numeric($value) ? $value.'%' : $value }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        @if (! empty($decodedResult['type']))
            <div class="card py-8 text-center dark:border-gray-700 dark:bg-navy-800">
                <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Tipe Hasil</p>
                <div class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-br from-sky-50 to-blue-50 px-10 py-5 shadow-inner dark:from-sky-900/30 dark:to-blue-900/30">
                    <span class="text-4xl font-bold tracking-widest text-sky-600 dark:text-sky-400">{{ $decodedResult['type'] }}</span>
                </div>
                @if (! empty($decodedResult['type_name']))
                    <p class="mt-3 text-lg font-semibold text-ink dark:text-white">{{ $decodedResult['type_name'] }}</p>
                @endif
            </div>
        @endif

        @if (! empty($decodedResult['iq']) || ! empty($decodedResult['level_title']))
            <div class="card dark:border-gray-700 dark:bg-navy-800">
                <h3 class="card-title dark:text-white">Ringkasan</h3>
                <dl class="mt-4 grid gap-3 sm:grid-cols-2">
                    @if (! empty($decodedResult['iq']))
                        <div class="rounded-lg bg-sky-50 px-4 py-3 dark:bg-sky-900/20">
                            <dt class="text-xs font-medium uppercase text-sky-600 dark:text-sky-400">IQ</dt>
                            <dd class="mt-1 text-2xl font-bold text-ink dark:text-white">{{ $decodedResult['iq'] }}</dd>
                        </div>
                    @endif
                    @if (! empty($decodedResult['percentage']))
                        <div class="rounded-lg bg-gray-50 px-4 py-3 dark:bg-gray-700/50">
                            <dt class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Persentase</dt>
                            <dd class="mt-1 text-2xl font-bold text-ink dark:text-white">{{ $decodedResult['percentage'] }}%</dd>
                        </div>
                    @endif
                    @if (! empty($decodedResult['level_title']))
                        <div class="rounded-lg bg-indigo-50 px-4 py-3 dark:bg-indigo-900/20 sm:col-span-2">
                            <dt class="text-xs font-medium uppercase text-indigo-600 dark:text-indigo-400">Level</dt>
                            <dd class="mt-1 text-lg font-semibold text-ink dark:text-white">{{ $decodedResult['level_title'] }}</dd>
                        </div>
                    @endif
                </dl>
                @if (! empty($decodedResult['advice']))
                    <p class="mt-4 text-sm leading-relaxed text-gray-600 dark:text-gray-400">{{ $decodedResult['advice'] }}</p>
                @endif
            </div>
        @endif

        @if (! empty($decodedResult['description']))
            <div class="card dark:border-gray-700 dark:bg-navy-800">
                <h3 class="card-title dark:text-white">Deskripsi</h3>
                <p class="mt-3 text-sm leading-relaxed text-gray-600 dark:text-gray-400">{{ $decodedResult['description'] }}</p>
            </div>
        @endif

        @if (! empty($decodedResult['strengths']))
            <div class="card dark:border-gray-700 dark:bg-navy-800">
                <h3 class="card-title dark:text-white">Kekuatan</h3>
                <ul class="mt-3 grid gap-2 sm:grid-cols-2">
                    @foreach ($decodedResult['strengths'] as $strength)
                        <li class="flex items-center gap-2 rounded-lg bg-green-50 px-3 py-2 text-sm text-gray-700 dark:bg-green-900/20 dark:text-gray-300">
                            <svg class="h-4 w-4 shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $strength }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (! empty($decodedResult['weaknesses']))
            <div class="card dark:border-gray-700 dark:bg-navy-800">
                <h3 class="card-title dark:text-white">Area Pengembangan</h3>
                <ul class="mt-3 grid gap-2 sm:grid-cols-2">
                    @foreach ($decodedResult['weaknesses'] as $weakness)
                        <li class="flex items-center gap-2 rounded-lg bg-amber-50 px-3 py-2 text-sm text-gray-700 dark:bg-amber-900/20 dark:text-gray-300">
                            <svg class="h-4 w-4 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            {{ $weakness }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (! empty($decodedResult['communication_style']))
            <div class="card dark:border-gray-700 dark:bg-navy-800">
                <h3 class="card-title dark:text-white">Gaya Komunikasi</h3>
                <p class="mt-3 text-sm leading-relaxed text-gray-600 dark:text-gray-400">{{ $decodedResult['communication_style'] }}</p>
            </div>
        @endif

        @if (! empty($decodedResult['motivations']))
            <div class="card dark:border-gray-700 dark:bg-navy-800">
                <h3 class="card-title dark:text-white">Motivasi</h3>
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach ($decodedResult['motivations'] as $motivation)
                        <span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-medium text-sky-600 dark:bg-sky-900/30 dark:text-sky-400">{{ $motivation }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        @if (! empty($decodedResult['fears']))
            <div class="card dark:border-gray-700 dark:bg-navy-800">
                <h3 class="card-title dark:text-white">Ketakutan / Hambatan</h3>
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach ($decodedResult['fears'] as $fear)
                        <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-600 dark:bg-red-900/30 dark:text-red-400">{{ $fear }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        @if (! empty($decodedResult['career_paths']))
            <div class="card dark:border-gray-700 dark:bg-navy-800">
                <h3 class="card-title dark:text-white">Rekomendasi Karier</h3>
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach ($decodedResult['career_paths'] as $career)
                        <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">{{ $career }}</span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@else
    <div class="card py-12 text-center dark:border-gray-700 dark:bg-navy-800">
        <div class="inline-flex items-center justify-center rounded-2xl bg-gray-50 p-4 dark:bg-gray-700">
            <svg class="h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>
        <h3 class="mt-4 text-lg font-semibold text-ink dark:text-white">Hasil belum tersedia</h3>
        <p class="mx-auto mt-2 max-w-sm text-sm text-gray-500 dark:text-gray-400">Data hasil assessment belum dapat ditampilkan.</p>
    </div>
@endif
