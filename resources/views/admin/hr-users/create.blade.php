<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-ink">{{ __('Tambah Akun HR/Admin') }}</h2></x-slot>
    <form method="POST" action="{{ route('admin.hr-users.store') }}" class="card max-w-xl space-y-4">
        @csrf
        <div><x-input-label for="organization_id" :value="__('Perusahaan')" />
            <select id="organization_id" name="organization_id" class="mt-1 block w-full rounded-lg border-gray-300" required>
                @foreach ($organizations as $org)
                    <option value="{{ $org->id }}">{{ $org->name }}</option>
                @endforeach
            </select>
        </div>
        <div><x-input-label for="name" :value="__('Nama')" /><x-text-input id="name" name="name" class="mt-1 block w-full" required /></div>
        <div><x-input-label for="email" :value="__('Email')" /><x-text-input id="email" name="email" type="email" class="mt-1 block w-full" required /></div>
        <div><x-input-label for="password" :value="__('Password')" /><x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required /></div>
        <div><x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" placeholder="Konfirmasi password" required /></div>
        <x-ui.form-actions
            :cancel-href="route('admin.hr-users.index')"
            :submit-title="__('Buat Akun HR')"
        />
    </form>
</x-app-layout>
