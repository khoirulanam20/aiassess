<x-guest-layout>
    <div class="flex min-h-[calc(100vh-8rem)] items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm">
            <div class="mb-8 text-center">
                <span class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-sky-500 text-lg font-bold text-white" aria-hidden="true">S</span>
                <h1 class="text-xl font-bold text-ink dark:text-white">{{ __('Verify Email') }}</h1>
                <p class="mt-1 text-sm text-ink-muted">{{ __('Please verify your email address') }}</p>
            </div>

            <div class="card">
                <div class="mb-4 rounded-lg bg-sky-50 px-4 py-3 text-sm text-sky-700 dark:bg-navy-700 dark:text-sky-300">
                    {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
                </div>

                @if (session('status') === 'verification-link-sent')
                    <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700 dark:bg-navy-700 dark:text-green-300">
                        {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                    </div>
                @endif

                <div class="flex items-center justify-between gap-2">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <x-ui.button type="submit" variant="primary">
                            {{ __('Kirim Ulang') }}
                        </x-ui.button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-ui.button type="submit" variant="ghost">
                            {{ __('Keluar') }}
                        </x-ui.button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
