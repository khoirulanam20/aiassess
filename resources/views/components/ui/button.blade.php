@props([
    'variant' => 'primary',
    'size' => 'default',
    'href' => null,
    'disabled' => false,
    'type' => 'button',
])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-500 focus-visible:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

$sizeClasses = match ($size) {
    'xs' => 'px-2.5 py-1.5 text-xs min-h-[36px]',
    'sm' => 'px-3 py-2 text-sm min-h-[40px]',
    'default' => 'px-4 py-2.5 text-sm min-h-[44px]',
    'lg' => 'px-5 py-3 text-base min-h-[48px]',
    'xl' => 'px-6 py-3.5 text-base min-h-[52px]',
    default => 'px-4 py-2.5 text-sm min-h-[44px]',
};

$variantClasses = match ($variant) {
    'primary' => 'bg-sky-500 text-white hover:bg-sky-600 active:bg-sky-700 dark:bg-sky-600 dark:hover:bg-sky-500',
    'secondary' => 'bg-white text-ink border border-gray-200 hover:bg-gray-50 active:bg-gray-100 dark:bg-navy-700 dark:text-white dark:border-gray-600 dark:hover:bg-navy-600',
    'ghost' => 'text-gray-600 hover:bg-gray-100 active:bg-gray-200 dark:text-gray-400 dark:hover:bg-navy-700',
    'danger' => 'bg-red-600 text-white hover:bg-red-500 active:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600',
    default => 'bg-sky-500 text-white hover:bg-sky-600 active:bg-sky-700 dark:bg-sky-600 dark:hover:bg-sky-500',
};

$classes = trim("$baseClasses $sizeClasses $variantClasses");
@endphp

@if ($href)
    <a
        href="{{ $href }}"
        role="button"
        {{ $attributes->merge(['class' => $classes]) }}
    >{{ $slot }}</a>
@else
    <button
        type="{{ $type }}"
        @disabled($disabled)
        {{ $attributes->merge(['class' => $classes]) }}
        {{ $disabled ? 'aria-disabled="true"' : '' }}
    >{{ $slot }}</button>
@endif
