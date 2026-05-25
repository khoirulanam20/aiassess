<x-app-layout>
    <x-slot name="header">
        <div>
            @isset($batch, $candidate)
                <x-ui.button-icon :href="route('admin.batches.candidates.show', [$batch, $candidate])" variant="ghost" icon="arrow-left">
                    {{ __('Kembali ke daftar assessment kandidat') }}
                </x-ui.button-icon>
            @else
                <x-ui.button-icon :href="route('admin.batches.index')" variant="ghost" icon="arrow-left">
                    {{ __('Kembali') }}
                </x-ui.button-icon>
            @endisset
            <h2 class="mt-2 text-xl font-semibold text-ink">{{ __('Detail Hasil Assessment') }}</h2>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl space-y-6">
        <div class="card">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Kandidat') }}</p>
                    <p class="mt-1 text-lg font-semibold text-ink">{{ $result->user->name }}</p>
                    @if ($result->user->userDetail?->employee_id)
                        <p class="text-sm text-gray-500">ID: {{ $result->user->userDetail->employee_id }}</p>
                    @endif
                </div>
                <div class="text-right">
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Assessment') }}</p>
                    <p class="mt-1 font-semibold text-ink">{{ $result->assessment->name ?? $result->test_name }}</p>
                    @isset($batch)
                        <p class="mt-1 text-sm text-gray-500">{{ __('Batch') }}: {{ $batch->name }}</p>
                    @endisset
                    <p class="mt-2 text-sm text-gray-500">
                        {{ __('Selesai') }}: {{ $result->created_at->format('d M Y, H:i') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3">
            <svg class="h-5 w-5 shrink-0 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span class="text-sm font-medium text-green-800">{{ __('Assessment telah diselesaikan') }}</span>
        </div>

        <x-assessment.result-display :result="$result" />
    </div>
</x-app-layout>
