<x-guest-layout>
    <div class="mx-auto max-w-md px-6 py-12">
        <div class="card text-center">
            <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-sky-100">
                <svg class="h-8 w-8 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>

            <h1 class="text-xl font-bold text-ink">{{ __('Akses Hasil Assessment') }}</h1>
            <p class="mt-2 text-sm text-gray-500">
                {{ __('Masukkan kode akses yang telah diberikan untuk melihat hasil assessment.') }}
            </p>

            @if ($errors->any())
                <div class="mt-4 rounded-lg bg-red-50 p-4 text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('shared.result.verify', $token) }}" class="mt-6">
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
                        class="form-input text-center text-2xl tracking-[0.5em] font-mono"
                        required
                        autofocus
                    >
                </div>

                <x-ui.button-icon type="submit" variant="solid" icon="eye" class="mt-6 w-full justify-center">
                    {{ __('Lihat Hasil') }}
                </x-ui.button-icon>
            </form>

            @if ($share && !$share->isValid())
                <div class="mt-4 rounded-lg bg-amber-50 p-4 text-sm text-amber-600">
                    {{ __('Link ini sudah tidak aktif. Hubungi pemberi akses untuk informasi lebih lanjut.') }}
                </div>
            @endif
        </div>
    </div>
</x-guest-layout>
