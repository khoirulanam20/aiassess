@props([
    'href' => null,
    'type' => 'button',
    'title' => '',
    'variant' => 'default',
    'disabled' => false,
])

@php
    $classes = 'inline-flex h-9 w-9 items-center justify-center rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-sky-500/30 disabled:pointer-events-none disabled:opacity-50 ' . match ($variant) {
        'solid' => 'bg-sky-600 text-white hover:bg-sky-700',
        'solid-danger' => 'bg-red-600 text-white hover:bg-red-700',
        'primary' => 'text-sky-600 hover:bg-sky-50',
        'danger' => 'text-red-600 hover:bg-red-50',
        default => 'text-gray-500 hover:bg-gray-100 hover:text-gray-800',
    };

@endphp

@if ($href)
    <a href="{{ $href }}" title="{{ $title }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" title="{{ $title }}" @disabled($disabled) {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
