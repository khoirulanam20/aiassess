@props([
    'title' => null,
    'padding' => true,
    'hover' => false,
])

@php
$hoverClasses = $hover ? 'hover:shadow-card-hover hover:border-gray-300 dark:hover:border-gray-500 cursor-pointer transition-shadow duration-150' : '';

$paddingClasses = match ($padding) {
    'none' => '',
    'sm' => 'p-4',
    'default', true => 'p-6',
    'lg' => 'p-8',
    default => 'p-6',
};
@endphp

<div {{ $attributes->merge([
    'class' => "rounded-xl border border-gray-200 bg-white shadow-card dark:border-gray-700 dark:bg-navy-800 $hoverClasses $paddingClasses",
]) }}>
    @if ($title)
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-ink dark:text-white">{{ $title }}</h3>
        </div>
    @endif
    {{ $slot }}
</div>
