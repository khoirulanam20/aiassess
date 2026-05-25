<x-app-layout>
    <x-slot name="header">
        <x-ui.button-icon :href="route('admin.master-data.departments.index')" variant="ghost" icon="arrow-left">
            {{ __('Daftar departemen') }}
        </x-ui.button-icon>
        <h2 class="mt-2 text-xl font-semibold text-ink">{{ __('Edit Departemen') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.master-data.departments.update', $department) }}" class="card max-w-xl space-y-4">
        @csrf
        @method('PATCH')
        @include('admin.master-data.partials.department-form', ['department' => $department])
        <x-ui.form-actions
            :cancel-href="route('admin.master-data.departments.index')"
            :submit-title="__('Simpan')"
        />
    </form>
</x-app-layout>
