<x-guest-minimal-layout>
    <div class="mx-auto max-w-5xl px-6 py-10">
        <div class="mb-8 text-center">
            <h1 class="text-2xl font-bold text-ink">{{ $share->batch->name }}</h1>
            <p class="mt-2 text-sm text-gray-500">
                {{ __('Halo :name, silakan kerjakan assessment berikut.', ['name' => $share->user->name]) }}
            </p>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if ($pending->isNotEmpty())
            <div class="mb-8">
                <h2 class="mb-4 text-lg font-semibold text-ink">{{ __('Harus dikerjakan') }} ({{ $pending->count() }})</h2>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($pending as $assignment)
                        @php $assessment = $assignment->assessment; @endphp
                        <div class="assessment-card rounded-xl border border-gray-200 bg-white p-5 shadow-card">
                            <div class="mb-4 flex items-start justify-between">
                                <div class="ac-icon" style="background-color: {{ $assessment->color }}15; color: {{ $assessment->color }}">
                                    {{ strtoupper(substr($assessment->slug, 0, 2)) }}
                                </div>
                                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800">{{ __('Menunggu') }}</span>
                            </div>
                            <h4 class="mb-1.5 font-semibold text-ink">{{ $assessment->name }}</h4>
                            <p class="mb-4 line-clamp-2 text-sm text-gray-500">{{ $assessment->description }}</p>
                            <div class="border-t border-gray-100 pt-4">
                                <x-ui.button-icon
                                    :href="route('shared.batch.take', [$token, $assessment->slug])"
                                    variant="solid"
                                    icon="play"
                                    class="w-full justify-center"
                                >
                                    {{ __('Kerjakan') }}
                                </x-ui.button-icon>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($completed->isNotEmpty())
            <div>
                <h2 class="mb-4 text-lg font-semibold text-ink">{{ __('Sudah selesai') }} ({{ $completed->count() }})</h2>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($completed as $assignment)
                        @php $assessment = $assignment->assessment; @endphp
                        <div class="rounded-xl border border-green-200 bg-green-50/50 p-5">
                            <div class="mb-2 flex items-center justify-between">
                                <h4 class="font-semibold text-ink">{{ $assessment->name }}</h4>
                                <span class="text-xs font-medium text-green-700">{{ __('Selesai') }}</span>
                            </div>
                            <p class="mb-3 text-xs text-gray-500">
                                {{ $assignment->completed_at?->format('d M Y H:i') }}
                            </p>
                            @if ($assignment->assessment_result_id)
                                <x-ui.icon-action
                                    :href="route('shared.batch.result', [$token, $assessment->slug, $assignment->assessment_result_id])"
                                    variant="primary"
                                    :title="__('Lihat ringkasan hasil')"
                                >
                                    <x-ui.icon name="chart" />
                                </x-ui.icon-action>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($pending->isEmpty() && $completed->isEmpty())
            <div class="card text-center text-sm text-gray-500">
                {{ __('Tidak ada penugasan assessment.') }}
            </div>
        @endif
    </div>
</x-guest-minimal-layout>
