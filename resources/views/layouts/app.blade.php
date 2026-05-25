<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ dark: localStorage.getItem('dark') === 'true' }" x-init="if (dark) document.documentElement.classList.add('dark')" @dark-mode-toggle.window="dark = $event.detail; document.documentElement.classList.toggle('dark', dark); localStorage.setItem('dark', dark)" :class="{ 'dark': dark }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ config('app.name', 'AI Assessment Platform') }}">
    <title>{{ config('app.name', 'Assessment') }} @isset($header) - {{ strip_tags($header) }} @endisset</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface-secondary text-ink antialiased dark:bg-navy-800 dark:text-gray-100">
    <a href="#main-content" class="sr-only focus-visible:not-sr-only focus-visible:fixed focus-visible:inset-x-0 focus-visible:top-0 focus-visible:z-50 focus-visible:flex focus-visible:h-12 focus-visible:items-center focus-visible:justify-center focus-visible:bg-sky-500 focus-visible:px-4 focus-visible:text-sm focus-visible:font-semibold focus-visible:text-white">
        {{ __('Skip to main content') }}
    </a>

    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden bg-surface-secondary dark:bg-navy-800">
        <x-shared.sidebar />

        <div class="flex flex-1 flex-col overflow-hidden lg:ml-64" role="region" aria-label="{{ __('Main content area') }}">
            <x-shared.topbar />

            <main id="main-content" class="flex-1 overflow-y-auto" role="main" tabindex="-1">
                @if (isset($header))
                    <div class="border-b border-gray-200 bg-white px-4 py-6 sm:px-8 dark:border-navy-600 dark:bg-navy-700">
                        <div class="container-wide">
                            {{ $header }}
                        </div>
                    </div>
                @endif

                <div class="container-wide py-8">
                    @if (session('success'))
                        <div class="alert-success mb-6" role="alert">
                            <svg class="alert-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div class="alert-content">{{ session('success') }}</div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert-danger mb-6" role="alert">
                            <svg class="alert-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div class="alert-content">{{ session('error') }}</div>
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="alert-warning mb-6" role="alert">
                            <svg class="alert-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                            <div class="alert-content">{{ session('warning') }}</div>
                        </div>
                    @endif

                    @if (session('info'))
                        <div class="alert-info mb-6" role="alert">
                            <svg class="alert-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div class="alert-content">{{ session('info') }}</div>
                        </div>
                    @endif

                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
