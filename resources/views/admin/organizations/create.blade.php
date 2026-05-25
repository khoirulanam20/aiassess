<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-ink">{{ __('Tambah Perusahaan') }}</h2></x-slot>
    <form method="POST" action="{{ route('admin.organizations.store') }}" class="card max-w-xl space-y-4">
        @csrf
        <div><x-input-label for="name" :value="__('Nama Perusahaan')" /><x-text-input id="name" name="name" class="mt-1 block w-full" required />@error('name')<x-input-error :messages="[$message]" />@enderror</div>
        <div><x-input-label for="email" :value="__('Email')" /><x-text-input id="email" name="email" type="email" class="mt-1 block w-full" />@error('email')<x-input-error :messages="[$message]" />@enderror</div>
        <div><x-input-label for="phone" :value="__('Telepon')" /><x-text-input id="phone" name="phone" class="mt-1 block w-full" /></div>
        <div><x-input-label for="industry" :value="__('Industri')" /><x-text-input id="industry" name="industry" class="mt-1 block w-full" /></div>
        <div><x-input-label for="address" :value="__('Alamat')" /><textarea id="address" name="address" rows="3" class="mt-1 block w-full rounded-lg border-gray-300"></textarea></div>
        <x-ui.form-actions
            :cancel-href="route('admin.organizations.index')"
            :submit-title="__('Simpan')"
        />
    </form>
</x-app-layout>
