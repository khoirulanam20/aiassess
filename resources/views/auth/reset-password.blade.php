<x-guest-layout>
    <div class="flex min-h-[calc(100vh-8rem)] items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm">
            <div class="mb-8 text-center">
                <span class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-sky-500 text-lg font-bold text-white" aria-hidden="true">S</span>
                <h1 class="text-xl font-bold text-ink dark:text-white">{{ __('Reset Password') }}</h1>
                <p class="mt-1 text-sm text-ink-muted">{{ __('Choose a new password for your account') }}</p>
            </div>

            <div class="card">
                <form method="POST" action="{{ route('password.store') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div class="space-y-4">
                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus
                                class="mt-1 block w-full input-field @error('email') border-red-400 @enderror" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password" :value="__('New Password')" />
                            <x-text-input id="password" type="password" name="password" required
                                class="mt-1 block w-full input-field @error('password') border-red-400 @enderror"
                                placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required
                                class="mt-1 block w-full input-field"
                                placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;" />
                        </div>
                    </div>

                    <div class="mt-6">
                        <x-ui.button type="submit" variant="primary" class="w-full justify-center">
                            {{ __('Reset Password') }}
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
