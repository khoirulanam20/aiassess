<x-guest-layout>
    <div class="flex min-h-[calc(100vh-8rem)] items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm">
            <div class="mb-8 text-center">
                <span class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-sky-500 text-lg font-bold text-white">S</span>
                <h1 class="text-xl font-bold text-ink">{{ __('Reset Password') }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ __('Choose a new password for your account') }}</p>
            </div>

            <div class="card">
                <form method="POST" action="{{ route('password.store') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div class="space-y-4">
                        <div>
                            <label for="email" class="label-field">{{ __('Email') }}</label>
                            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus
                                   class="input-field @error('email') border-red-400 @enderror">
                            @error('email')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="label-field">{{ __('New Password') }}</label>
                            <input id="password" type="password" name="password" required
                                   class="input-field @error('password') border-red-400 @enderror"
                                   placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;">
                            @error('password')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="label-field">{{ __('Confirm Password') }}</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required
                                   class="input-field"
                                   placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;">
                        </div>
                    </div>

                    <div class="mt-6">
                        <x-ui.button-icon type="submit" variant="solid" icon="check" class="w-full justify-center">
                            {{ __('Reset Password') }}
                        </x-ui.button-icon>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
