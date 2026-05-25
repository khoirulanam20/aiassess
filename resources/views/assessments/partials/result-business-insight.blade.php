@php
$dimLabels = ['autonomy'=>'Otonomi','innovativeness'=>'Inovasi','risk_taking'=>'Pengambilan Risiko','proactiveness'=>'Proaktif','competitive_aggressiveness'=>'Agresivitas Kompetitif'];
$dimIcons = ['autonomy'=>'compass','innovativeness'=>'lightbulb','risk_taking'=>'activity','proactiveness'=>'zap','competitive_aggressiveness'=>'target'];
$levelColors = ['high'=>'#10B981','medium'=>'#F59E0B','low'=>'#EF4444','very_high'=>'#8B5CF6'];
$levelLabels = ['high'=>'Tinggi','medium'=>'Sedang','low'=>'Rendah','very_high'=>'Sangat Tinggi'];
@endphp

@if (!empty($decodedResult['scores']))
    <div class="card">
        <h3 class="card-title">{{ __('Entrepreneurial Profile') }}</h3>
        <p class="mt-1 text-sm text-gray-500">{{ __('5 Entrepreneurial Dimensions') }}</p>
        <div class="mt-6 space-y-5">
            @foreach ($decodedResult['scores'] as $dim => $data)
                @php $color = $levelColors[$data['level']] ?? '#666'; @endphp
                <div>
                    <div class="mb-1 flex items-center justify-between">
                        <span class="text-sm font-medium text-ink">{{ $dimLabels[$dim] ?? $dim }}</span>
                        <span class="text-xs font-medium" style="color: {{ $color }};">
                            {{ $levelLabels[$data['level']] ?? $data['level'] }} - {{ $data['percentage'] }}%
                        </span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-bar-fill transition-all duration-500" style="width: {{ $data['percentage'] }}%; background: {{ $color }};"></div>
                    </div>
                    @if (!empty($data['description']))
                        <p class="mt-0.5 text-xs text-gray-400">{{ $data['description'] }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    @if (!empty($decodedResult['overall']))
        @php $overallColor = $levelColors[$decodedResult['overall']['level']] ?? '#666'; @endphp
        <div class="card text-center" style="background: linear-gradient(135deg, {{ $overallColor }}10, transparent);">
            <h3 class="card-title">{{ __('Entrepreneurial Potential') }}</h3>
            <div class="mx-auto mt-4 flex h-24 w-24 items-center justify-center rounded-full text-3xl font-bold text-white shadow-lg"
                 style="background: {{ $overallColor }};">
                {{ $decodedResult['overall']['percentage'] }}%
            </div>
            <p class="mt-3 text-lg font-semibold text-ink">{{ $decodedResult['overall']['label'] ?? $decodedResult['overall']['level'] }}</p>
            @if (!empty($decodedResult['overall']['description']))
                <p class="mt-2 text-sm leading-relaxed text-gray-500 max-w-md mx-auto">{{ $decodedResult['overall']['description'] }}</p>
            @endif
        </div>

        @if (!empty($decodedResult['overall']['advice']))
            <div class="card bg-gradient-to-br from-amber-50 to-yellow-50">
                <h3 class="card-title">{{ __('Development Advice') }}</h3>
                <p class="mt-3 text-sm leading-relaxed text-gray-600">{{ $decodedResult['overall']['advice'] }}</p>
            </div>
        @endif
    @endif
@endif
