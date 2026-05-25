@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'primary',
    'icon' => null,
    'disabled' => false,
    'size' => 'default',
])

@php
$baseClasses = 'inline-flex items-center justify-center gap-2 font-medium rounded-lg transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-500 focus-visible:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

$sizeClasses = match ($size) {
    'sm' => 'px-3 py-2 text-sm min-h-[40px]',
    'default' => 'px-4 py-2.5 text-sm min-h-[44px]',
    'lg' => 'px-5 py-3 text-base min-h-[48px]',
    default => 'px-4 py-2.5 text-sm min-h-[44px]',
};

$variantClasses = match ($variant) {
    'solid' => 'bg-sky-500 text-white hover:bg-sky-600 active:bg-sky-700 dark:bg-sky-600 dark:hover:bg-sky-500',
    'solid-danger' => 'bg-red-600 text-white hover:bg-red-700 active:bg-red-800 dark:bg-red-700 dark:hover:bg-red-600',
    'primary' => 'border border-sky-500 text-sky-600 hover:bg-sky-50 dark:border-sky-400 dark:text-sky-400 dark:hover:bg-navy-700',
    'secondary' => 'border border-gray-200 text-gray-600 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-navy-700',
    'danger' => 'border border-red-500 text-red-600 hover:bg-red-50 dark:border-red-400 dark:text-red-400 dark:hover:bg-navy-700',
    'ghost' => 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-navy-700',
    default => 'border border-gray-200 text-gray-600 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-navy-700',
};

$classes = trim("$baseClasses $sizeClasses $variantClasses");
@endphp

@if ($href)
    <a
        href="{{ $href }}"
        role="button"
        {{ $attributes->merge(['class' => $classes]) }}
    >
        @if ($icon)
            <x-ui.icon :name="$icon" class="h-4 w-4 shrink-0" aria-hidden="true" />
        @endif
        {{ $slot }}
    </a>
@else
    <button
        type="{{ $type }}"
        @disabled($disabled)
        {{ $attributes->merge(['class' => $classes]) }}
        @disabled($disabled) aria-disabled="{{ $disabled ? 'true' : 'false' }}"
    >
        @if ($icon)
            <x-ui.icon :name="$icon" class="h-4 w-4 shrink-0" aria-hidden="true" />
        @endif
        {{ $slot }}
    </button>
@endif
