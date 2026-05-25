<x-guest-minimal-layout>
    <div class="mx-auto max-w-md px-6 py-12">
        <div class="card text-center">
            <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-sky-100">
                <svg class="h-8 w-8 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>

            <h1 class="text-xl font-bold text-ink">{{ __('Kerjakan Assessment') }}</h1>
            <p class="mt-2 text-sm text-gray-500">
                {{ __('Masukkan kode akses 6 digit yang Anda terima dari HR.') }}
            </p>
            @if ($batch ?? null)
                <p class="mt-1 text-sm font-medium text-ink">{{ $batch->name }}</p>
            @endif

            @if ($errors->any())
                <div class="mt-4 rounded-lg bg-red-50 p-4 text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
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
                        class="form-input text-center font-mono text-2xl tracking-[0.5em]"
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
