@props([
    'cancelHref' => null,
    'cancelTitle' => __('Batal'),
    'submitTitle' => __('Simpan'),
    'submitDisabled' => false,
])

<div {{ $attributes->merge(['class' => 'flex flex-col-reverse sm:flex-row items-center gap-3 sm:gap-2']) }}>
    <x-ui.button-icon
        type="submit"
        variant="solid"
        icon="check"
        :disabled="$submitDisabled"
        size="default"
    >
        {{ $submitTitle }}
    </x-ui.button-icon>
    @if ($cancelHref)
        <x-ui.button-icon :href="$cancelHref" variant="secondary" icon="arrow-left" size="default">
            {{ $cancelTitle }}
        </x-ui.button-icon>
    @endif
    {{ $slot }}
</div>
