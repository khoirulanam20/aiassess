@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'primary',
    'icon' => null,
    'disabled' => false,
])

@php
    $classes = 'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-sky-500/30 disabled:pointer-events-none disabled:opacity-50 ' . match ($variant) {
        'solid' => 'bg-sky-600 text-white hover:bg-sky-700',
        'solid-danger' => 'bg-red-600 text-white hover:bg-red-700',
        'primary' => 'border border-sky-600 text-sky-600 hover:bg-sky-50',
        'secondary' => 'border border-gray-300 text-gray-700 hover:bg-gray-50',
        'danger' => 'border border-red-600 text-red-600 hover:bg-red-50',
        'ghost' => 'text-gray-600 hover:bg-gray-100',
        default => 'border border-gray-300 text-gray-700 hover:bg-gray-50',
    };
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)
            <x-ui.icon :name="$icon" class="h-4 w-4" />
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" @disabled($disabled) {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)
            <x-ui.icon :name="$icon" class="h-4 w-4" />
        @endif
        {{ $slot }}
    </button>
@endif
