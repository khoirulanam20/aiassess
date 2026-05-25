<x-guest-layout>
    <div class="flex min-h-[calc(100vh-8rem)] items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm">
            <div class="mb-8 text-center">
                <span class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-sky-500 text-lg font-bold text-white">S</span>
                <h1 class="text-xl font-bold text-ink">{{ __('Confirm Password') }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ __('Please confirm your password before continuing') }}</p>
            </div>

            <div class="card">
                <form method="POST" action="{{ route('password.confirm') }}">
                    @csrf

                    <div>
                        <label for="password" class="label-field">{{ __('Password') }}</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                               class="input-field @error('password') border-red-400 @enderror">
                        @error('password')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-6">
                        <x-ui.button-icon type="submit" variant="solid" icon="check" class="w-full justify-center">
                            {{ __('Konfirmasi') }}
                        </x-ui.button-icon>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
