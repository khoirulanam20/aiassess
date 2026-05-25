<x-guest-minimal-layout>
    <div class="mx-auto max-w-md px-6 py-12">
        <div class="card text-center">
            <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-sky-100 dark:bg-sky-900/30">
                <x-ui.icon name="clipboard" class="h-8 w-8 text-sky-600 dark:text-sky-400" />
            </div>

            <h1 class="text-xl font-bold text-ink dark:text-gray-100">{{ __('Kerjakan Assessment') }}</h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Masukkan kode akses 6 digit yang Anda terima dari HR.') }}
            </p>
            @if ($batch ?? null)
                <p class="mt-1 text-sm font-medium text-ink dark:text-gray-100">{{ $batch->name }}</p>
            @endif

            @if ($errors->any())
                <div class="alert-danger mt-4" role="alert">
                    <svg class="alert-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="alert-content">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ $verifyUrl }}" class="mt-6">
                @csrf
                <div>
                    <label for="access_code" class="sr-only">{{ __('Kode Akses') }}</label>
                    <input
                        id="access_code"
                        name="access_code"
                        type="text"
                        inputmode="numeric"
                        autocomplete="off"
                        maxlength="8"
                        placeholder="000000"
                        class="input-field text-center font-mono text-2xl tracking-[0.5em]"
                        required
                        autofocus
                    >
                </div>
                <x-ui.button-icon type="submit" variant="solid" icon="arrow-right" class="mt-6 w-full justify-center">
                    {{ __('Lanjut') }}
                </x-ui.button-icon>
            </form>
        </div>
    </div>
</x-guest-minimal-layout>
