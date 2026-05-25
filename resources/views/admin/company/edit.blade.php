<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-ink">{{ __('Profil Perusahaan') }}</h2></x-slot>
    <form method="POST" action="{{ route('admin.company.update') }}" class="card max-w-xl space-y-4">
        @csrf @method('PATCH')
        <div><x-input-label for="name" :value="__('Nama Perusahaan')" /><x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', $organization->name)" required /></div>
        <div><x-input-label for="email" :value="__('Email HR')" /><x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $organization->email)" /></div>
        <div><x-input-label for="phone" :value="__('Telepon')" /><x-text-input id="phone" name="phone" class="mt-1 block w-full" :value="old('phone', $organization->phone)" /></div>
        <div><x-input-label for="industry" :value="__('Industri')" /><x-text-input id="industry" name="industry" class="mt-1 block w-full" :value="old('industry', $organization->industry)" /></div>
        <div><x-input-label for="address" :value="__('Alamat')" /><textarea id="address" name="address" rows="3" class="mt-1 block w-full rounded-lg border-gray-300">{{ old('address', $organization->address) }}</textarea></div>
        <x-ui.form-actions :submit-title="__('Simpan Perubahan')" />
    </form>
</x-app-layout>
