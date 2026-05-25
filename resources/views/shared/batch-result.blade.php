<x-guest-minimal-layout>
    <div class="mx-auto max-w-3xl px-6 py-10">
        <div class="mb-6 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-green-100">
                <svg class="h-7 w-7 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h1 class="text-2xl font-bold text-ink">{{ __('Assessment Selesai') }}</h1>
            <p class="mt-2 text-sm text-gray-500">{{ $assessment->name }}</p>
        </div>

        <div class="space-y-6">
            <x-assessment.result-display :result="$result" />

            <div class="text-center">
                <x-ui.button-icon :href="route('shared.batch.portal', $token)" variant="solid" icon="arrow-left">
                    {{ __('Kembali ke daftar assessment') }}
                </x-ui.button-icon>
            </div>
        </div>
    </div>
</x-guest-minimal-layout>
