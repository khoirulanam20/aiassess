@props(['show' => false, 'title' => '', 'maxWidth' => 'md'])

@php
    $maxWidthClasses = match ($maxWidth) {
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        default => 'max-w-md',
    };
@endphp

<div x-data="{ show: @js($show) }"
     x-show="show"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center px-4"
     x-transition:enter="transition duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    <div class="fixed inset-0 bg-navy-500/40 backdrop-blur-sm" @click="show = false"></div>
    <div class="{{ $maxWidthClasses }} relative w-full rounded-xl bg-white p-6 shadow-dropdown"
         x-transition:enter="transition duration-200"
         x-transition:enter-start="scale-95 opacity-0"
         x-transition:enter-end="scale-100 opacity-100"
         x-transition:leave="transition duration-150"
         x-transition:leave-start="scale-100 opacity-100"
         x-transition:leave-end="scale-95 opacity-0">
        @if ($title)
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-ink">{{ $title }}</h3>
                <button @click="show = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif
        {{ $slot }}
    </div>
</div>
