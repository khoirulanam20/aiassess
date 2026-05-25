@props([
    'show' => false,
    'title' => '',
    'maxWidth' => 'md',
    'closeable' => true,
])

@php
$maxWidthClasses = match ($maxWidth) {
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
    '3xl' => 'max-w-3xl',
    '4xl' => 'max-w-4xl',
    '5xl' => 'max-w-5xl',
    default => 'max-w-md',
};
@endphp

<div
    x-data="{
        show: @js($show),
        closeable: @js($closeable),
        focusables() {
            let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])';
            return [...$el.querySelectorAll(selector)]
                .filter(el => !el.hasAttribute('disabled'));
        },
        firstFocusable() { return this.focusables()[0] },
        lastFocusable() { return this.focusables().slice(-1)[0] },
        nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable() },
        prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable() },
        nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1) },
        prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) - 1 },
        close() { if (this.closeable) this.show = false },
    }"
    x-init="$watch('show', value => {
        if (value) {
            document.body.classList.add('overflow-y-hidden');
            $nextTick(() => { if (firstFocusable()) firstFocusable().focus() });
        } else {
            document.body.classList.remove('overflow-y-hidden');
        }
    })"
    x-show="show"
    x-cloak
    @keydown.escape.window="close()"
    @keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
    @keydown.shift.tab.prevent="prevFocusable().focus()"
    role="dialog"
    aria-modal="true"
    :aria-label="'{{ $title }}' || undefined"
    class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6"
>
    {{-- Backdrop --}}
    <div
        x-show="show"
        class="fixed inset-0 bg-navy-500/50 backdrop-blur-sm dark:bg-navy-900/70"
        @if ($closeable) @click="show = false" @endif
        x-transition:enter="transition-opacity duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        aria-hidden="true"
    ></div>

    {{-- Panel --}}
    <div
        x-show="show"
        class="{{ $maxWidthClasses }} relative z-10 w-full rounded-xl bg-white p-6 shadow-dropdown dark:bg-navy-800 dark:border dark:border-gray-700"
        x-transition:enter="transition-all duration-200"
        x-transition:enter-start="scale-95 opacity-0 translate-y-4"
        x-transition:enter-end="scale-100 opacity-100 translate-y-0"
        x-transition:leave="transition-all duration-150"
        x-transition:leave-start="scale-100 opacity-100 translate-y-0"
        x-transition:leave-end="scale-95 opacity-0 translate-y-4"
    >
        @if ($title)
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-ink dark:text-white" id="modal-title">{{ $title }}</h2>
                @if ($closeable)
                    <button
                        @click="show = false"
                        class="inline-flex items-center justify-center rounded-lg p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:text-gray-300 dark:hover:bg-navy-700 transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-500 focus-visible:ring-offset-2 min-w-[44px] min-h-[44px]"
                        aria-label="{{ __('Tutup') }}"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                @endif
            </div>
        @endif
        {{ $slot }}
    </div>
</div>
