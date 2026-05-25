@props([
    'href',
    'title' => __('Kembali'),
])

<x-ui.icon-action :href="$href" :title="$title" {{ $attributes }}>
    <x-ui.icon name="arrow-left" />
</x-ui.icon-action>
