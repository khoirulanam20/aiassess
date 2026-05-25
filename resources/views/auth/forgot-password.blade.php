<x-guest-layout>
    <div class="flex min-h-[calc(100vh-8rem)] items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm">
            <div class="mb-8 text-center">
                <span class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-sky-500 text-lg font-bold text-white">S</span>
                <h1 class="text-xl font-bold text-ink">{{ __('Forgot Password') }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ __('Enter your email and we\'ll send you a reset link') }}</p>
            </div>

            <div class="card">
                <div class="mb-4 rounded-lg bg-sky-50 px-4 py-3 text-sm text-sky-700">
                    {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.') }}
                </div>

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div>
                        <label for="email" class="label-field">{{ __('Email') }}</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="input-field @error('email') border-red-400 @enderror"
                               placeholder="you@example.com">
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-6">
                        <x-ui.button-icon type="submit" variant="solid" icon="arrow-right" class="w-full justify-center">
                            {{ __('Kirim Link Reset') }}
                        </x-ui.button-icon>
                    </div>

                    <p class="mt-4 text-center text-sm text-gray-500">
                        <a href="{{ route('login') }}" class="font-medium text-sky-500 hover:text-sky-600">{{ __('Back to Sign In') }}</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
