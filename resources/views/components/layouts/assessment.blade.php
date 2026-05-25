@props(['assessment', 'backUrl' => null, 'subtitle' => null, 'progress' => null, 'progressLabel' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ dark: localStorage.getItem('dark') === 'true' }" x-init="if (dark) document.documentElement.classList.add('dark')" @dark-mode-toggle.window="dark = $event.detail; document.documentElement.classList.toggle('dark', dark); localStorage.setItem('dark', dark)" :class="{ 'dark': dark }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ config('app.name') }} - {{ $assessment->name ?? 'Assessment' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface-secondary text-ink antialiased dark:bg-navy-800 dark:text-gray-100">
    <a href="#main-content" class="sr-only focus-visible:not-sr-only focus-visible:fixed focus-visible:inset-x-0 focus-visible:top-0 focus-visible:z-50 focus-visible:flex focus-visible:h-12 focus-visible:items-center focus-visible:justify-center focus-visible:bg-sky-500 focus-visible:px-4 focus-visible:text-sm focus-visible:font-semibold focus-visible:text-white">
        {{ __('Skip to main content') }}
    </a>

    <div class="border-b border-gray-200 bg-white dark:border-navy-600 dark:bg-navy-700" role="banner">
        <div class="container-narrow flex h-14 items-center justify-between">
            <div class="flex items-center gap-3 min-w-0">
                @if ($backUrl)
                    <a href="{{ $backUrl }}"
                       class="flex min-h-11 min-w-11 items-center justify-center rounded-lg text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-500 focus-visible:ring-offset-2 dark:hover:bg-navy-600 dark:hover:text-gray-300 dark:focus-visible:ring-offset-navy-800"
                       aria-label="{{ __('Back') }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                @endif
                <div class="min-w-0">
                    <span class="truncate text-sm font-medium text-ink dark:text-gray-100">{{ $assessment->name ?? '' }}</span>
                    @if ($subtitle)
                        <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>
            <span class="shrink-0 text-xs text-gray-400 dark:text-gray-500">{{ __('Assessment') }}</span>
        </div>

        @if ($progress !== null)
            <div class="container-narrow px-4 sm:px-6" role="progressbar" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100" aria-label="{{ $progressLabel ?? __('Assessment progress') }}">
                <div class="progress-bar mb-3">
                    <div class="progress-bar-fill" style="width: {{ $progress }}%"></div>
                </div>
            </div>
        @endif
    </div>

    <main id="main-content" class="container-narrow py-8" role="main" tabindex="-1">
        {{ $slot }}
    </main>

    @stack('scripts')
</body>
</html>
