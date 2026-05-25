<x-guest-layout>
    <div class="flex min-h-[calc(100vh-8rem)] items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm">
            <div class="mb-8 text-center">
                <span class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-sky-500 text-lg font-bold text-white" aria-hidden="true">S</span>
                <h1 class="text-xl font-bold text-ink dark:text-white">{{ __('Welcome Back') }}</h1>
                <p class="mt-1 text-sm text-ink-muted">{{ __('Sign in to continue your assessments') }}</p>
            </div>

            <div class="card">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="space-y-4">
                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                                class="mt-1 block w-full input-field @error('email') border-red-400 @enderror"
                                placeholder="you@example.com" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password" type="password" name="password" required autocomplete="current-password"
                                class="mt-1 block w-full input-field @error('password') border-red-400 @enderror"
                                placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-gray-300 text-sky-500 focus:ring-sky-500 dark:border-gray-600 dark:bg-navy-700">
                                <span class="text-sm text-ink-muted">{{ __('Remember me') }}</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm font-medium text-sky-500 hover:text-sky-600 dark:text-sky-400">
                                    {{ __('Forgot?') }}
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6">
                        <x-ui.button type="submit" variant="primary" class="w-full justify-center">
                            {{ __('Masuk') }}
                        </x-ui.button>
                    </div>

                    @if (Route::has('register'))
                        <p class="mt-4 text-center text-sm text-ink-muted">
                            {{ __("Don't have an account?") }}
                            <a href="{{ route('register') }}" class="font-medium text-sky-500 hover:text-sky-600 dark:text-sky-400">{{ __('Register') }}</a>
                        </p>
                    @endif
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
