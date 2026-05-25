@props([
    'disabled' => false,
    'error' => null,
    'id' => null,
])

@php
$baseClasses = 'block w-full rounded-lg border shadow-sm transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-50 dark:disabled:bg-navy-700 text-sm min-h-[44px] px-3 py-2.5';

$normalClasses = 'border-gray-200 text-ink bg-white focus-visible:ring-sky-500 focus-visible:border-sky-500 dark:bg-navy-800 dark:text-white dark:border-gray-600 dark:focus-visible:ring-sky-400 dark:placeholder-gray-500';

$errorClasses = 'border-red-500 text-ink bg-white focus-visible:ring-red-500 focus-visible:border-red-500 dark:border-red-400 dark:bg-navy-800 dark:text-white';

$classes = $error ? trim("$baseClasses $errorClasses") : trim("$baseClasses $normalClasses");
@endphp

<input
    @disabled($disabled)
    @if ($id) id="{{ $id }}" @endif
    {{ $attributes->merge(['class' => $classes]) }}
    @if ($error) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
>
