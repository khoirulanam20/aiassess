<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-ink">{{ __('Dashboard') }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ __('Welcome back, :name!', ['name' => Auth::user()->name]) }}</p>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="kpi-card">
            <div class="kpi-icon bg-sky-50 text-sky-500">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <p class="kpi-label">{{ __('Total Completed') }}</p>
            <p class="kpi-value">{{ $completedCount }}</p>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon bg-green-50 text-green-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <p class="kpi-label">{{ __('Available') }}</p>
            <p class="kpi-value">{{ $assessments->where('can_retake', true)->count() }}</p>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon bg-amber-50 text-amber-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="kpi-label">{{ __('In Cooldown') }}</p>
            <p class="kpi-value">{{ $assessments->where('has_completed', true)->where('can_retake', false)->count() }}</p>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon bg-purple-50 text-purple-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <p class="kpi-label">{{ __('Total Types') }}</p>
            <p class="kpi-value">{{ $assessments->count() }}</p>
        </div>
    </div>

    @if ($batchAssignments->isNotEmpty())
        <div class="mt-8">
            <div class="card-header">
                <h2 class="card-title">{{ __('Penugasan Batch') }}</h2>
            </div>
            <div class="card divide-y divide-gray-100">
                @foreach ($batchAssignments as $assignment)
                    <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4">
                        <div>
                            <p class="text-sm font-medium text-ink">{{ $assignment->assessment->name }}</p>
                            <p class="text-xs text-gray-500">{{ $assignment->batch->name }}</p>
                        </div>
                        <x-ui.button-icon
                            :href="route('assessments.show', $assignment->assessment->slug)"
                            variant="solid"
                            icon="play"
                        >
                            {{ __('Kerjakan') }}
                        </x-ui.button-icon>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="mt-8">
        <div class="card-header">
            <h2 class="card-title">{{ __('Available Assessments') }}</h2>
            <x-ui.button-icon :href="route('assessments.index')" variant="ghost" icon="squares">
                {{ __('Lihat Semua') }} &rarr;
            </x-ui.button-icon>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($assessments as $assessment)
                <x-assessment.card :assessment="$assessment" />
            @endforeach
        </div>
    </div>
</x-app-layout>
