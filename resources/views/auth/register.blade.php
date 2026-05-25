<x-guest-layout>
    <div class="flex min-h-[calc(100vh-8rem)] items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm">
            <div class="mb-8 text-center">
                <span class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-sky-500 text-lg font-bold text-white">S</span>
                <h1 class="text-xl font-bold text-ink">{{ __('Create Account') }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ __('Start your assessment journey') }}</p>
            </div>

            <div class="card">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="space-y-4">
                        <div>
                            <label for="name" class="label-field">{{ __('Name') }}</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                                   class="input-field @error('name') border-red-400 @enderror"
                                   placeholder="John Doe">
                            @error('name')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="label-field">{{ __('Email') }}</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                                   class="input-field @error('email') border-red-400 @enderror"
                                   placeholder="you@example.com">
                            @error('email')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="label-field">{{ __('Password') }}</label>
                            <input id="password" type="password" name="password" required autocomplete="new-password"
                                   class="input-field @error('password') border-red-400 @enderror"
                                   placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;">
                            @error('password')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="label-field">{{ __('Confirm Password') }}</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                                   class="input-field"
                                   placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;">
                        </div>
                    </div>

                    <div class="mt-6">
                        <x-ui.button-icon type="submit" variant="solid" icon="plus" class="w-full justify-center">
                            {{ __('Buat Akun') }}
                        </x-ui.button-icon>
                    </div>

                    <p class="mt-4 text-center text-sm text-gray-500">
                        {{ __('Already have an account?') }}
                        <a href="{{ route('login') }}" class="font-medium text-sky-500 hover:text-sky-600">{{ __('Sign In') }}</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
