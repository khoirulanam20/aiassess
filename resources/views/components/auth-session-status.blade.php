@props(['status'])

@if ($status)
    <div
        {{ $attributes->merge(['class' => 'flex items-center gap-2 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-4 py-3 text-sm font-medium text-green-700 dark:text-green-300']) }}
        role="alert"
        aria-live="polite"
    >
        <svg class="h-5 w-5 shrink-0 text-green-500 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>{{ $status }}</span>
    </div>
@endif
