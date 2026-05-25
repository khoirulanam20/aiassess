<x-guest-layout>
    <div class="flex min-h-[calc(100vh-8rem)] items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm">
            <div class="mb-8 text-center">
                <span class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-sky-500 text-lg font-bold text-white">S</span>
                <h1 class="text-xl font-bold text-ink">{{ __('Verify Email') }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ __('Please verify your email address') }}</p>
            </div>

            <div class="card">
                <div class="mb-4 rounded-lg bg-sky-50 px-4 py-3 text-sm text-sky-700">
                    {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
                </div>

                @if (session('status') === 'verification-link-sent')
                    <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">
                        {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                    </div>
                @endif

                <div class="flex items-center justify-between gap-2">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <x-ui.button-icon type="submit" variant="solid" icon="refresh">
                            {{ __('Kirim Ulang') }}
                        </x-ui.button-icon>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-ui.button-icon type="submit" variant="ghost" icon="arrow-left">
                            {{ __('Keluar') }}
                        </x-ui.button-icon>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
