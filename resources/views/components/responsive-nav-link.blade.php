@props(['active'])

@php
$classes = ($active ?? false)
    ? 'block w-full ps-3 pe-4 py-3 border-l-4 border-sky-500 text-start text-base font-medium text-sky-700 bg-sky-50 focus-visible:outline-none focus-visible:text-sky-800 focus-visible:bg-sky-100 focus-visible:border-sky-600 transition-colors duration-150 dark:bg-navy-700 dark:text-sky-400 dark:border-sky-400 dark:focus-visible:bg-navy-600'
    : 'block w-full ps-3 pe-4 py-3 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus-visible:outline-none focus-visible:text-gray-800 focus-visible:bg-gray-50 focus-visible:border-gray-300 transition-colors duration-150 dark:text-gray-400 dark:hover:text-white dark:hover:bg-navy-700 dark:hover:border-gray-500';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
