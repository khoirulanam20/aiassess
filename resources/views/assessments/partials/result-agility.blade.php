@php
$subTestLabels = ['sadana'=>'Sadana','etung'=>'Etung','sankarta'=>'Sankarta','sacit'=>'Sacit','citraleka'=>'Citraleka','bedhe_aksara'=>'Bedhe Aksara','pathway'=>'Pathway','vacana'=>'Vacana'];
$subTestDescriptions = ['sadana'=>'Counterfactual Thinking','etung'=>'Numeracy','sankarta'=>'Pattern Recognition','sacit'=>'Abstract Reasoning','citraleka'=>'Visual Spatial','bedhe_aksara'=>'Verbal Reasoning','pathway'=>'Learning Agility','vacana'=>'Self-Reflection'];
$dimLabels = ['cognitive_flexibility'=>'Fleksibilitas Kognitif','analytical_thinking'=>'Pemikiran Analitis','learning_potential'=>'Potensi Pembelajaran','adaptability'=>'Adaptabilitas','self_awareness'=>'Kesadaran Diri'];
$subTestColors = ['sadana'=>'#0EA5E9','etung'=>'#10B981','sankarta'=>'#F59E0B','sacit'=>'#EF4444','citraleka'=>'#8B5CF6','bedhe_aksara'=>'#EC4899','pathway'=>'#14B8A6','vacana'=>'#6366F1'];
@endphp

@if (!empty($decodedResult['sub_tests']))
    <div class="card">
        <h3 class="card-title">{{ __('Agility Profile') }}</h3>
        <p class="mt-1 text-sm text-gray-500">{{ __('8 Sub-Test Results') }}</p>
        <div class="mt-6 grid gap-4 sm:grid-cols-2">
            @foreach ($decodedResult['sub_tests'] as $key => $data)
                @php $color = $subTestColors[$key] ?? '#0EA5E9'; @endphp
                <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-card">
                    <div class="mb-3 flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg text-xs font-bold text-white" style="background: {{ $color }};">
                            {{ substr($subTestLabels[$key] ?? $key, 0, 2) }}
                        </span>
                        <div>
                            <span class="text-sm font-medium text-ink">{{ $subTestLabels[$key] ?? $key }}</span>
                            <p class="text-[10px] text-gray-400">{{ $subTestDescriptions[$key] ?? '' }}</p>
                        </div>
                    </div>
                    <div class="progress-bar mb-1">
                        <div class="progress-bar-fill transition-all duration-500" style="width: {{ $data['percentage'] }}%; background: {{ $color }};"></div>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-medium" style="color: {{ $data['percentage'] >= 70 ? '#059669' : ($data['percentage'] >= 50 ? '#D97706' : '#DC2626') }};">
                            {{ $data['level'] ?? '' }}
                        </span>
                        <span class="text-gray-400">{{ $data['percentage'] }}%</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @if (!empty($decodedResult['dimensions']))
        <div class="card">
            <h3 class="card-title">{{ __('Cognitive Dimensions') }}</h3>
            <div class="mt-4 space-y-4">
                @foreach ($decodedResult['dimensions'] as $dim => $data)
                    <div>
                        <div class="mb-1 flex items-center justify-between text-sm">
                            <span class="font-medium text-ink">{{ $dimLabels[$dim] ?? $dim }}</span>
                            <span class="text-xs text-gray-400">{{ $data['percentage'] }}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-bar-fill transition-all duration-500"
                                 style="width: {{ $data['percentage'] }}%; background: {{ $data['percentage'] >= 70 ? '#10B981' : ($data['percentage'] >= 50 ? '#F59E0B' : '#EF4444') }};"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if (!empty($decodedResult['overall']))
        <div class="card text-center">
            <h3 class="card-title">{{ __('Overall Agility') }}</h3>
            <div class="mx-auto mt-4 flex h-20 w-20 items-center justify-center rounded-full text-2xl font-bold text-white shadow-lg"
                 style="background: {{ ($decodedResult['overall']['percentage'] ?? 0) >= 70 ? '#10B981' : (($decodedResult['overall']['percentage'] ?? 0) >= 50 ? '#F59E0B' : '#EF4444') }};">
                {{ $decodedResult['overall']['percentage'] ?? 0 }}%
            </div>
            <p class="mt-2 text-lg font-semibold text-ink">{{ $decodedResult['overall']['level'] ?? '' }}</p>
        </div>
    @endif
@endif
