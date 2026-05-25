<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-ink">{{ __('Edit Perusahaan') }}</h2></x-slot>
    <form method="POST" action="{{ route('admin.organizations.update', $organization) }}" class="card max-w-xl space-y-4">
        @csrf @method('PATCH')
        <div><x-input-label for="name" :value="__('Nama Perusahaan')" /><x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', $organization->name)" required /></div>
        <div><x-input-label for="email" :value="__('Email')" /><x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $organization->email)" /></div>
        <div><x-input-label for="phone" :value="__('Telepon')" /><x-text-input id="phone" name="phone" class="mt-1 block w-full" :value="old('phone', $organization->phone)" /></div>
        <div><x-input-label for="industry" :value="__('Industri')" /><x-text-input id="industry" name="industry" class="mt-1 block w-full" :value="old('industry', $organization->industry)" /></div>
        <div><x-input-label for="address" :value="__('Alamat')" /><textarea id="address" name="address" rows="3" class="mt-1 block w-full rounded-lg border-gray-300">{{ old('address', $organization->address) }}</textarea></div>
        <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $organization->is_active) ? 'checked' : '' }} class="rounded border-gray-300"><span class="text-sm">{{ __('Aktif') }}</span></label>
        <div class="flex gap-3">
            <x-ui.form-actions
                :cancel-href="route('admin.organizations.index')"
                :submit-title="__('Simpan')"
            />
        </div>
    </form>
</x-app-layout>
