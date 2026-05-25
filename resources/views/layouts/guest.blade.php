<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Assessment') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-surface-secondary">
    <header class="border-b border-gray-200 bg-white">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-6">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 font-bold text-xl text-navy-500">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-sky-500 text-sm font-bold text-white">S</span>
                Skillana
            </a>
            <div class="flex items-center gap-4">
                @if (Route::has('login'))
                    @auth
                        <x-ui.button-icon :href="route('dashboard')" variant="solid" icon="squares">
                            {{ __('Dashboard') }}
                        </x-ui.button-icon>
                    @else
                        <x-ui.button-icon :href="route('login')" variant="ghost" icon="arrow-right">
                            {{ __('Masuk') }}
                        </x-ui.button-icon>
                        @if (Route::has('register'))
                            <x-ui.button-icon :href="route('register')" variant="solid" icon="plus">
                                {{ __('Daftar') }}
                            </x-ui.button-icon>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <main class="flex-1">
        {{ $slot }}
    </main>

    <footer class="border-t border-gray-200 bg-white py-6">
        <div class="mx-auto max-w-7xl px-6 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}
        </div>
    </footer>
</body>
</html>
