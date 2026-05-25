@php
$setLabels = ['A'=>'Set A','B'=>'Set B','C'=>'Set C','D'=>'Set D','E'=>'Set E'];
$setColors = ['A'=>'#0EA5E9','B'=>'#10B981','C'=>'#F59E0B','D'=>'#EF4444','E'=>'#8B5CF6'];
@endphp

@if (!empty($decodedResult['set_scores']))
    <div class="card text-center">
        <h3 class="card-title">{{ __('SPM Assessment Result') }}</h3>
        <div class="mt-6 grid grid-cols-2 gap-6 sm:grid-cols-3">
            <div class="rounded-xl bg-gradient-to-br from-sky-50 to-blue-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Total Correct') }}</p>
                <p class="mt-1 text-3xl font-bold text-sky-600">{{ $decodedResult['total_correct'] ?? 0 }}</p>
                <p class="text-xs text-gray-400">/ {{ $decodedResult['total_questions'] ?? 60 }}</p>
            </div>
            <div class="rounded-xl bg-gradient-to-br from-green-50 to-emerald-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Percentage') }}</p>
                <p class="mt-1 text-3xl font-bold text-green-600">{{ $decodedResult['percentage'] ?? 0 }}%</p>
            </div>
            <div class="rounded-xl bg-gradient-to-br from-purple-50 to-violet-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('IQ Estimate') }}</p>
                <p class="mt-1 text-3xl font-bold text-purple-600">{{ $decodedResult['iq'] ?? 100 }}</p>
            </div>
        </div>
    </div>

    <div class="card">
        <h3 class="card-title">{{ __('Score per Set') }}</h3>
        <div class="mt-4 space-y-4">
            @foreach ($decodedResult['set_scores'] as $set => $score)
                @php $max = 12; $pct = $max > 0 ? round(($score / $max) * 100) : 0; @endphp
                <div>
                    <div class="mb-1 flex items-center justify-between text-sm">
                        <span class="font-medium text-ink">{{ $setLabels[$set] ?? $set }}</span>
                        <span class="text-gray-500">{{ $score }}/{{ $max }}</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-bar-fill transition-all duration-500"
                             style="width: {{ $pct }}%; background: {{ $setColors[$set] ?? '#0EA5E9' }};"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @if (!empty($decodedResult['percentile']))
        <div class="card">
            <h3 class="card-title">{{ __('Percentile Rank') }}</h3>
            <div class="mt-3 flex items-center gap-4">
                <div class="relative h-4 flex-1 overflow-hidden rounded-full bg-gray-100">
                    <div class="h-full rounded-full bg-gradient-to-r from-sky-400 to-purple-500 transition-all duration-700"
                         style="width: {{ $decodedResult['percentile'] }}%;"></div>
                </div>
                <span class="text-lg font-bold text-ink">{{ $decodedResult['percentile'] }}%</span>
            </div>
        </div>
    @endif

    @if (!empty($decodedResult['level']))
        <div class="card">
            <h3 class="card-title">{{ __('Cognitive Level') }}</h3>
            <div class="mt-3 flex items-center gap-4">
                <span class="flex h-14 w-14 items-center justify-center rounded-2xl text-lg font-bold text-white"
                      style="background: {{ $decodedResult['iq'] >= 120 ? '#7C3AED' : ($decodedResult['iq'] >= 100 ? '#0EA5E9' : '#F59E0B') }};">
                    {{ $decodedResult['level_title'] ?? $decodedResult['level'] }}
                </span>
                <div>
                    <p class="font-semibold text-ink">{{ $decodedResult['level_title'] ?? $decodedResult['level'] }}</p>
                    @if (!empty($decodedResult['description']))
                        <p class="mt-1 text-sm text-gray-500">{{ $decodedResult['description'] }}</p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @if (!empty($decodedResult['advice']))
        <div class="card bg-gradient-to-br from-indigo-50 to-purple-50">
            <h3 class="card-title">{{ __('Recommendation') }}</h3>
            <p class="mt-3 text-sm leading-relaxed text-gray-600">{{ $decodedResult['advice'] }}</p>
        </div>
    @endif
@endif
