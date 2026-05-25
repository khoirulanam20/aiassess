<button
    {{ $attributes->merge([
        'type' => 'submit',
        'class' => 'inline-flex items-center justify-center px-4 py-2.5 min-h-[44px] bg-sky-500 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-sky-600 active:bg-sky-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-500 focus-visible:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-150 dark:bg-sky-600 dark:hover:bg-sky-500',
    ]) }}
>
    {{ $slot }}
</button>
