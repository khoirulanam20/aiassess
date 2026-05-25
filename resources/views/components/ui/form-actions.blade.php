@props([
    'cancelHref' => null,
    'cancelTitle' => __('Batal'),
    'submitTitle' => __('Simpan'),
    'submitDisabled' => false,
])

<div {{ $attributes->merge(['class' => 'flex items-center gap-2']) }}>
    <x-ui.button-icon
        type="submit"
        variant="solid"
        icon="check"
        :disabled="$submitDisabled"
    >
        {{ $submitTitle }}
    </x-ui.button-icon>
    @if ($cancelHref)
        <x-ui.button-icon :href="$cancelHref" variant="secondary" icon="arrow-left">
            {{ $cancelTitle }}
        </x-ui.button-icon>
    @endif
    {{ $slot }}
</div>
