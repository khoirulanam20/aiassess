<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ dark: localStorage.getItem('dark') === 'true' }" x-init="if (dark) document.documentElement.classList.add('dark')" @dark-mode-toggle.window="dark = $event.detail; document.documentElement.classList.toggle('dark', dark); localStorage.setItem('dark', dark)" :class="{ 'dark': dark }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ config('app.name', 'Assessment') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-surface-secondary text-ink antialiased dark:bg-navy-800 dark:text-gray-100">
    <a href="#main-content" class="sr-only focus-visible:not-sr-only focus-visible:fixed focus-visible:inset-x-0 focus-visible:top-0 focus-visible:z-50 focus-visible:flex focus-visible:h-12 focus-visible:items-center focus-visible:justify-center focus-visible:bg-sky-500 focus-visible:px-4 focus-visible:text-sm focus-visible:font-semibold focus-visible:text-white">
        {{ __('Skip to main content') }}
    </a>

    <main id="main-content" class="mx-auto min-h-screen max-w-4xl px-4 py-6 sm:px-6 sm:py-10" role="main" tabindex="-1">
        {{ $slot }}
    </main>

    @stack('scripts')
</body>
</html>
