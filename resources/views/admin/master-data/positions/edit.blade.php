<x-app-layout>
    <x-slot name="header">
        <x-ui.button-icon :href="route('admin.master-data.positions.index')" variant="ghost" icon="arrow-left">
            {{ __('Daftar posisi') }}
        </x-ui.button-icon>
        <h2 class="mt-2 text-xl font-semibold text-ink">{{ __('Edit Posisi') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.master-data.positions.update', $position) }}" class="card max-w-xl space-y-4">
        @csrf
        @method('PATCH')
        @include('admin.master-data.partials.position-form', compact('departments', 'position'))
        <x-ui.form-actions
            :cancel-href="route('admin.master-data.positions.index')"
            :submit-title="__('Simpan')"
        />
    </form>
</x-app-layout>
