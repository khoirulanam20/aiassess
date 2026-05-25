<x-guest-layout>
    <div class="flex min-h-screen items-center justify-center">
        <div class="card mx-auto max-w-md text-center">
            <h1 class="text-6xl font-bold text-primary-500">404</h1>
            <h2 class="mt-4 text-xl font-semibold text-foreground">{{ __('Page Not Found') }}</h2>
            <p class="mt-2 text-gray-500">{{ __('The page you are looking for does not exist or has been moved.') }}</p>
            <div class="mt-6 flex justify-center">
                <x-ui.button-icon :href="route('dashboard')" variant="solid" icon="squares">
                    {{ __('Ke Dashboard') }}
                </x-ui.button-icon>
            </div>
        </div>
    </div>
</x-guest-layout>
