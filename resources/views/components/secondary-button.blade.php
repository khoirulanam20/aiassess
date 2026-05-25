<button
    {{ $attributes->merge([
        'type' => 'button',
        'class' => 'inline-flex items-center justify-center px-4 py-2.5 min-h-[44px] bg-white border border-gray-200 rounded-lg font-semibold text-sm text-ink shadow-sm hover:bg-gray-50 active:bg-gray-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-500 focus-visible:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-150 dark:bg-navy-700 dark:text-white dark:border-gray-600 dark:hover:bg-navy-600',
    ]) }}
>
    {{ $slot }}
</button>
