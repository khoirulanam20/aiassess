<x-guest-layout>
    <div class="flex min-h-[calc(100vh-8rem)] items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm">
            <div class="mb-8 text-center">
                <span class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-sky-500 text-lg font-bold text-white" aria-hidden="true">S</span>
                <h1 class="text-xl font-bold text-ink dark:text-white">{{ __('Forgot Password') }}</h1>
                <p class="mt-1 text-sm text-ink-muted">{{ __('Enter your email and we\'ll send you a reset link') }}</p>
            </div>

            <div class="card">
                <div class="mb-4 rounded-lg bg-sky-50 px-4 py-3 text-sm text-sky-700 dark:bg-navy-700 dark:text-sky-300">
                    {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.') }}
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus
                            class="mt-1 block w-full input-field @error('email') border-red-400 @enderror"
                            placeholder="you@example.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="mt-6">
                        <x-ui.button type="submit" variant="primary" class="w-full justify-center">
                            {{ __('Kirim Link Reset') }}
                        </x-ui.button>
                    </div>

                    <p class="mt-4 text-center text-sm text-ink-muted">
                        <a href="{{ route('login') }}" class="font-medium text-sky-500 hover:text-sky-600 dark:text-sky-400">{{ __('Back to Sign In') }}</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
