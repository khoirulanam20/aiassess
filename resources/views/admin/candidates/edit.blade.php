<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-ink">{{ __('Edit Kandidat / Karyawan') }}</h2></x-slot>
    <form method="POST" action="{{ route('admin.candidates.update', $candidate) }}" class="card max-w-xl space-y-4">
        @csrf @method('PATCH')
        <div><x-input-label for="name" :value="__('Nama Lengkap')" /><x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', $candidate->name)" required /></div>
        <div><x-input-label for="email" :value="__('Email')" /><x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $candidate->email)" required /></div>
        <div><x-input-label for="employee_id" :value="__('ID Karyawan')" /><x-text-input id="employee_id" name="employee_id" class="mt-1 block w-full" :value="old('employee_id', $candidate->userDetail?->employee_id)" /></div>
        <div><x-input-label for="phone" :value="__('Telepon')" /><x-text-input id="phone" name="phone" class="mt-1 block w-full" :value="old('phone', $candidate->userDetail?->phone)" /></div>
        <div><x-input-label for="position" :value="__('Posisi')" /><x-text-input id="position" name="position" class="mt-1 block w-full" :value="old('position', $candidate->userDetail?->position)" /></div>
        <div><x-input-label for="department" :value="__('Departemen')" /><x-text-input id="department" name="department" class="mt-1 block w-full" :value="old('department', $candidate->userDetail?->department)" /></div>
        <x-ui.form-actions
            :cancel-href="route('admin.candidates.index')"
            :submit-title="__('Simpan')"
        />
    </form>
</x-app-layout>
