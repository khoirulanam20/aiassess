@props([
    'href' => null,
    'type' => 'button',
    'title' => '',
    'variant' => 'default',
    'disabled' => false,
    'label' => null,
])

@php
$baseClasses = 'inline-flex items-center justify-center rounded-lg transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-500 focus-visible:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed min-w-[44px] min-h-[44px]';

$variantClasses = match ($variant) {
    'solid' => 'bg-sky-500 text-white hover:bg-sky-600 active:bg-sky-700 dark:bg-sky-600 dark:hover:bg-sky-500',
    'solid-danger' => 'bg-red-600 text-white hover:bg-red-700 active:bg-red-800 dark:bg-red-700 dark:hover:bg-red-600',
    'primary' => 'text-sky-600 hover:bg-sky-50 dark:text-sky-400 dark:hover:bg-navy-700',
    'danger' => 'text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-navy-700',
    default => 'text-gray-500 hover:bg-gray-100 hover:text-gray-800 dark:text-gray-400 dark:hover:bg-navy-700 dark:hover:text-white',
};

$classes = trim("$baseClasses $variantClasses");
@endphp

@if ($href)
    <a
        href="{{ $href }}"
        title="{{ $title }}"
        {{ $attributes->merge(['class' => $classes]) }}
        @if ($label) aria-label="{{ $label }}" @elseif ($title) aria-label="{{ $title }}" @endif
        role="button"
    >{{ $slot }}</a>
@else
    <button
        type="{{ $type }}"
        title="{{ $title }}"
        @disabled($disabled)
        {{ $attributes->merge(['class' => $classes]) }}
        @if ($label) aria-label="{{ $label }}" @elseif ($title) aria-label="{{ $title }}" @endif
        @disabled($disabled) aria-disabled="{{ $disabled ? 'true' : 'false' }}"
    >{{ $slot }}</button>
@endif
