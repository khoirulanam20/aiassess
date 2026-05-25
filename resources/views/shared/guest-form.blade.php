<x-guest-layout>
    <div class="mx-auto max-w-md px-6 py-12">
        <div class="card text-center">
            <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-sky-100 dark:bg-navy-700">
                <svg class="h-8 w-8 text-sky-600 dark:text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>

            <h1 class="text-xl font-bold text-ink dark:text-white">{{ __('Akses Hasil Assessment') }}</h1>
            <p class="mt-2 text-sm text-ink-muted">{{ __('Masukkan kode akses yang telah diberikan untuk melihat hasil assessment.') }}</p>

            @if ($errors->any())
                <div class="mt-4 rounded-lg bg-red-50 p-4 text-sm text-red-600 dark:bg-navy-700 dark:text-red-400" role="alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('shared.result.verify', $token) }}" class="mt-6">
                @csrf

                <div>
                    <x-input-label for="access_code" :value="__('Kode Akses')" class="sr-only" />
                    <x-text-input
                        id="access_code"
                        name="access_code"
                        type="text"
                        inputmode="numeric"
                        autocomplete="off"
                        maxlength="8"
                        placeholder="000000"
                        class="mt-1 block w-full text-center text-2xl tracking-[0.5em] font-mono input-field"
                        required
                        autofocus
                    />
                </div>

                <x-ui.button type="submit" variant="primary" class="mt-6 w-full justify-center">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    {{ __('Lihat Hasil') }}
                </x-ui.button>
            </form>

            @if ($share && !$share->isValid())
                <div class="mt-4 rounded-lg bg-amber-50 p-4 text-sm text-amber-600 dark:bg-navy-700 dark:text-amber-400">
                    {{ __('Link ini sudah tidak aktif. Hubungi pemberi akses untuk informasi lebih lanjut.') }}
                </div>
            @endif
        </div>
    </div>
</x-guest-layout>
