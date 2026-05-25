<x-guest-layout>
    <div class="flex min-h-[calc(100vh-8rem)] items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm">
            <div class="mb-8 text-center">
                <span class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-sky-500 text-lg font-bold text-white" aria-hidden="true">S</span>
                <h1 class="text-xl font-bold text-ink dark:text-white">{{ __('Confirm Password') }}</h1>
                <p class="mt-1 text-sm text-ink-muted">{{ __('Please confirm your password before continuing') }}</p>
            </div>

            <div class="card">
                <form method="POST" action="{{ route('password.confirm') }}">
                    @csrf

                    <div>
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password" type="password" name="password" required autocomplete="current-password"
                            class="mt-1 block w-full input-field @error('password') border-red-400 @enderror" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="mt-6">
                        <x-ui.button type="submit" variant="primary" class="w-full justify-center">
                            {{ __('Konfirmasi') }}
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
