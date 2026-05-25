<x-guest-minimal-layout>
    <div class="mx-auto max-w-3xl px-6 py-10">
        <div class="mb-6 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                <x-ui.icon name="check" class="h-7 w-7 text-green-600 dark:text-green-400" />
            </div>
            <h1 class="text-2xl font-bold text-ink dark:text-gray-100">{{ __('Assessment Selesai') }}</h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $assessment->name }}</p>
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
