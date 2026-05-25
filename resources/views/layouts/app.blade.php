<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Assessment') }} @isset($header) - {{ $header }} @endisset</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden bg-surface-secondary">
        <x-shared.sidebar />

        <div class="flex flex-1 flex-col overflow-hidden lg:ml-64">
            <x-shared.topbar />

            <main class="flex-1 overflow-y-auto">
                @if (isset($header))
                    <div class="page-header border-b border-gray-200 bg-white px-8 py-6">
                        <div class="mx-auto max-w-7xl">
                            {{ $header }}
                        </div>
                    </div>
                @endif

                <div class="mx-auto max-w-7xl px-8 py-8">
                    @if (session('success'))
                        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {{ session('error') }}
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
