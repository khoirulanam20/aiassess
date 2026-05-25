@props(['assessment', 'backUrl' => null, 'subtitle' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - {{ $assessment->name ?? 'Assessment' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface-secondary">
    <div class="border-b border-gray-200 bg-white">
        <div class="mx-auto flex h-14 max-w-4xl items-center justify-between px-6">
            <div class="flex items-center gap-3">
                @if ($backUrl)
                    <a href="{{ $backUrl }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                @endif
                <div>
                    <span class="text-sm font-medium text-ink">{{ $assessment->name ?? '' }}</span>
                    @if ($subtitle)
                        <p class="text-xs text-gray-500">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>
            <span class="text-xs text-gray-400">{{ __('Assessment') }}</span>
        </div>
    </div>

    <main class="mx-auto max-w-4xl px-6 py-8">
        {{ $slot }}
    </main>

    @stack('scripts')
</body>
</html>
