<a
    {{ $attributes->merge([
        'class' => 'block w-full px-4 py-2.5 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-ink focus-visible:outline-none focus-visible:bg-gray-100 focus-visible:text-ink transition-colors duration-150 dark:text-gray-300 dark:hover:bg-navy-700 dark:hover:text-white dark:focus-visible:bg-navy-700 dark:focus-visible:text-white',
    ]) }}
    role="menuitem"
>{{ $slot }}</a>
