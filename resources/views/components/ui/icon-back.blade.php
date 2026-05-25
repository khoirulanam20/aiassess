@props([
    'href',
    'title' => __('Kembali'),
    'label' => __('Kembali ke halaman sebelumnya'),
])

<x-ui.icon-action
    :href="$href"
    :title="$title"
    :label="$label"
    variant="ghost"
    {{ $attributes }}
>
    <x-ui.icon name="arrow-left" aria-hidden="true" />
</x-ui.icon-action>
