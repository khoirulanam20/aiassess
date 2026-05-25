<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-ink">{{ __('Edit Akun HR/Admin') }}</h2></x-slot>
    <form method="POST" action="{{ route('admin.hr-users.update', $hrUser) }}" class="card max-w-xl space-y-4">
        @csrf @method('PATCH')
        <div><x-input-label for="organization_id" :value="__('Perusahaan')" />
            <select id="organization_id" name="organization_id" class="mt-1 block w-full rounded-lg border-gray-300" required>
                @foreach ($organizations as $org)
                    <option value="{{ $org->id }}" @selected(old('organization_id', $hrUser->organization_id) == $org->id)>{{ $org->name }}</option>
                @endforeach
            </select>
        </div>
        <div><x-input-label for="name" :value="__('Nama')" /><x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', $hrUser->name)" required /></div>
        <div><x-input-label for="email" :value="__('Email')" /><x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $hrUser->email)" required /></div>
        <div><x-input-label for="password" :value="__('Password baru (kosongkan jika tidak diubah)')" /><x-text-input id="password" name="password" type="password" class="mt-1 block w-full" /></div>
        <x-ui.form-actions
            :cancel-href="route('admin.hr-users.index')"
            :submit-title="__('Simpan')"
        />
    </form>
</x-app-layout>
