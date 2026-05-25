@php
$dimLabels = ['planning'=>'Planning','organizing'=>'Organizing','leading'=>'Leading','controlling'=>'Controlling','decision_making'=>'Decision Making','communication'=>'Communication','problem_solving'=>'Problem Solving','delegation'=>'Delegation'];
$dimIcons = ['planning'=>'clipboard-list','organizing'=>'folder-tree','leading'=>'users','controlling'=>'sliders','decision_making'=>'scale','communication'=>'message-square','problem_solving'=>'lightbulb','delegation'=>'share-2'];
@endphp

@if (!empty($decodedResult['scores']))
    <div class="card">
        <h3 class="card-title">{{ __('Management Skills Profile') }}</h3>
        <p class="mt-1 text-sm text-gray-500">{{ __('8 Management Dimensions') }}</p>
        <div class="mt-6 grid gap-4 sm:grid-cols-2">
            @foreach ($decodedResult['scores'] as $dim => $data)
                <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-card">
                    <div class="mb-3 flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg text-xs font-bold"
                              style="background-color: {{ $data['percentage'] >= 70 ? '#05966920' : ($data['percentage'] >= 50 ? '#F59E0B20' : '#EF444420') }}; color: {{ $data['percentage'] >= 70 ? '#059669' : ($data['percentage'] >= 50 ? '#F59E0B' : '#EF4444') }};">
                            {{ substr($dimLabels[$dim] ?? $dim, 0, 2) }}
                        </span>
                        <span class="text-sm font-medium text-ink">{{ $dimLabels[$dim] ?? $dim }}</span>
                    </div>
                    <div class="progress-bar mb-2">
                        <div class="progress-bar-fill transition-all duration-700"
                             style="width: {{ $data['percentage'] }}%; background: {{ $data['percentage'] >= 70 ? '#10B981' : ($data['percentage'] >= 50 ? '#F59E0B' : '#EF4444') }};"></div>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-medium"
                              style="color: {{ $data['percentage'] >= 70 ? '#059669' : ($data['percentage'] >= 50 ? '#D97706' : '#DC2626') }};">
                            {{ $data['level'] ?? '' }}
                        </span>
                        <span class="text-gray-400">{{ $data['percentage'] }}%</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @if (!empty($decodedResult['overall']))
        <div class="card text-center">
            <h3 class="card-title">{{ __('Overall Management Skill Level') }}</h3>
            <div class="mt-4">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full text-2xl font-bold text-white"
                     style="background: {{ ($decodedResult['overall']['percentage'] ?? 0) >= 70 ? '#10B981' : (($decodedResult['overall']['percentage'] ?? 0) >= 50 ? '#F59E0B' : '#EF4444') }};">
                    {{ $decodedResult['overall']['percentage'] ?? 0 }}%
                </div>
                <p class="mt-2 text-lg font-semibold text-ink">{{ $decodedResult['overall']['level'] ?? '' }}</p>
            </div>
        </div>
    @endif

    @if (!empty($decodedResult['highest_dimension']) && isset($decodedResult['scores'][$decodedResult['highest_dimension']]))
        <div class="card bg-gradient-to-br from-green-50 to-emerald-50">
            <h3 class="card-title">{{ __('Strongest Skill') }}</h3>
            <div class="mt-3 text-sm text-gray-600">
                <span class="font-semibold text-ink">{{ $dimLabels[$decodedResult['highest_dimension']] ?? $decodedResult['highest_dimension'] }}</span>
                - {{ $decodedResult['scores'][$decodedResult['highest_dimension']]['percentage'] }}%
            </div>
        </div>
    @endif

    @if (!empty($decodedResult['lowest_dimension']) && isset($decodedResult['scores'][$decodedResult['lowest_dimension']]))
        <div class="card bg-gradient-to-br from-red-50 to-rose-50">
            <h3 class="card-title">{{ __('Area for Development') }}</h3>
            <div class="mt-3 text-sm text-gray-600">
                <span class="font-semibold text-ink">{{ $dimLabels[$decodedResult['lowest_dimension']] ?? $decodedResult['lowest_dimension'] }}</span>
                - {{ $decodedResult['scores'][$decodedResult['lowest_dimension']]['percentage'] }}%
            </div>
        </div>
    @endif
@endif
