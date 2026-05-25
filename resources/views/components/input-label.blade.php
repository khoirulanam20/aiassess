@props([
    'value' => null,
    'required' => false,
    'for' => null,
])

<label
    @if ($for) for="{{ $for }}" @endif
    {{ $attributes->merge(['class' => 'block text-sm font-medium text-ink dark:text-white']) }}
>
    {{ $value ?? $slot }}
    @if ($required)
        <span class="ml-1 text-red-500" aria-hidden="true">*</span>
        <span class="sr-only">({{ __('wajib diisi') }})</span>
    @endif
</label>
