@props(['active'])

@php
$classes = ($active ?? false)
    ? 'inline-flex items-center px-1 pt-1 border-b-2 border-sky-500 text-sm font-medium leading-5 text-ink focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-500 focus-visible:ring-offset-2 transition-colors duration-150 dark:border-sky-400 dark:text-white'
    : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus-visible:outline-none focus-visible:text-gray-700 focus-visible:border-gray-300 transition-colors duration-150 dark:text-gray-400 dark:hover:text-white dark:hover:border-gray-500';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
