<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ dark: localStorage.getItem('dark') === 'true' }" x-init="if (dark) document.documentElement.classList.add('dark')" @dark-mode-toggle.window="dark = $event.detail; document.documentElement.classList.toggle('dark', dark); localStorage.setItem('dark', dark)" :class="{ 'dark': dark }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ config('app.name', 'AI Assessment Platform') }}">
    <title>{{ config('app.name', 'Assessment') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-gradient-to-br from-surface-secondary via-white to-sky-50 text-ink antialiased dark:from-navy-800 dark:via-navy-700 dark:to-navy-800 dark:text-gray-100">
    <a href="#main-content" class="sr-only focus-visible:not-sr-only focus-visible:fixed focus-visible:inset-x-0 focus-visible:top-0 focus-visible:z-50 focus-visible:flex focus-visible:h-12 focus-visible:items-center focus-visible:justify-center focus-visible:bg-sky-500 focus-visible:px-4 focus-visible:text-sm focus-visible:font-semibold focus-visible:text-white">
        {{ __('Skip to main content') }}
    </a>

    <header class="border-b border-gray-200 bg-white/80 backdrop-blur-sm dark:border-navy-600 dark:bg-navy-700/80" role="banner">
        <div class="container-wide flex h-16 items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 text-xl font-bold text-navy-500 dark:text-white" aria-label="{{ config('app.name') }}">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-sky-500 text-sm font-bold text-white" aria-hidden="true">S</span>
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
                <button x-data="{ dark: localStorage.getItem('dark') === 'true' }"
                        x-init="if (dark) document.documentElement.classList.add('dark')"
                        @click="dark = !dark; document.documentElement.classList.toggle('dark', dark); localStorage.setItem('dark', dark)"
                        class="flex min-h-11 min-w-11 items-center justify-center rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-500 focus-visible:ring-offset-2 dark:text-gray-400 dark:hover:bg-navy-600 dark:focus-visible:ring-offset-navy-800"
                        aria-label="{{ __('Toggle dark mode') }}">
                    <svg x-show="!dark" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg x-show="dark" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>
            </div>
        </div>
    </header>

    <main id="main-content" class="flex-1" role="main" tabindex="-1">
        {{ $slot }}
    </main>

    <footer class="border-t border-gray-200 bg-white/80 backdrop-blur-sm py-6 dark:border-navy-600 dark:bg-navy-700/80" role="contentinfo">
        <div class="container-wide text-center text-sm text-gray-500 dark:text-gray-400">
            &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}
        </div>
    </footer>
</body>
</html>
