@php
$dimLabels = ['N'=>'Need to Achieve','G'=>'Need to be Noticed','A'=>'Need for Authority','L'=>'Need to Lead','P'=>'Need for Affiliation','I'=>'Need to be Independent','T'=>'Need for Change','V'=>'Need to be Vigorous','S'=>'Need to be Sociable','R'=>'Need for Rules'];
$dimColors = ['N'=>'#0EA5E9','G'=>'#F59E0B','A'=>'#EF4444','L'=>'#8B5CF6','P'=>'#10B981','I'=>'#EC4899','T'=>'#F97316','V'=>'#14B8A6','S'=>'#3B82F6','R'=>'#6366F1'];
@endphp

@if (!empty($decodedResult['dimensions']))
    <div class="card">
        <h3 class="card-title">{{ __('PAPI Kostick Profile') }}</h3>
        <p class="mt-1 text-sm text-gray-500">{{ __('10 Work Needs Dimensions') }}</p>
        <div class="mt-6 space-y-4">
            @foreach ($decodedResult['dimensions'] as $dim => $data)
                <div>
                    <div class="mb-1 flex items-center justify-between text-sm">
                        <span class="font-medium text-ink">{{ $dim }}</span>
                        <span class="font-semibold" style="color: {{ $dimColors[$dim] ?? '#666' }}">{{ $dimLabels[$dim] ?? $dim }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="progress-bar flex-1">
                            <div class="progress-bar-fill transition-all duration-700"
                                 style="width: {{ $data['percentage'] }}%; background: {{ $dimColors[$dim] ?? '#0EA5E9' }};"></div>
                        </div>
                        <span class="w-16 text-right text-xs font-medium text-gray-500">{{ $data['score'] }}/9</span>
                    </div>
                    @if (!empty($data['level_description']))
                        <p class="mt-0.5 text-xs text-gray-400">{{ $data['level'] }} - {{ $data['level_description'] }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    @if (!empty($decodedResult['highest_dimension']))
        <div class="card bg-gradient-to-br from-sky-50 to-blue-50">
            <h3 class="card-title">{{ __('Highest Dimension') }}</h3>
            <div class="mt-3 flex items-center gap-4">
                <span class="flex h-14 w-14 items-center justify-center rounded-2xl text-xl font-bold text-white"
                      style="background: {{ $dimColors[$decodedResult['highest_dimension']] ?? '#0EA5E9' }};">
                    {{ $decodedResult['highest_dimension'] }}
                </span>
                <div>
                    <p class="font-semibold text-ink">{{ $dimLabels[$decodedResult['highest_dimension']] ?? $decodedResult['highest_dimension'] }}</p>
                    <p class="text-sm text-gray-500">{{ $decodedResult['dimensions'][$decodedResult['highest_dimension']]['level'] ?? '' }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (!empty($decodedResult['lowest_dimension']) && isset($dimLabels[$decodedResult['lowest_dimension']]))
        <div class="card bg-gradient-to-br from-amber-50 to-orange-50">
            <h3 class="card-title">{{ __('Lowest Dimension') }}</h3>
            <div class="mt-3 flex items-center gap-4">
                <span class="flex h-14 w-14 items-center justify-center rounded-2xl text-xl font-bold text-white"
                      style="background: {{ $dimColors[$decodedResult['lowest_dimension']] ?? '#F59E0B' }};">
                    {{ $decodedResult['lowest_dimension'] }}
                </span>
                <div>
                    <p class="font-semibold text-ink">{{ $dimLabels[$decodedResult['lowest_dimension']] ?? $decodedResult['lowest_dimension'] }}</p>
                    <p class="text-sm text-gray-500">{{ $decodedResult['dimensions'][$decodedResult['lowest_dimension']]['level'] ?? '' }}</p>
                </div>
            </div>
        </div>
    @endif
@endif
