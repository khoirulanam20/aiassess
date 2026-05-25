<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-ink">{{ __('Perusahaan (Multi-Tenant)') }}</h2>
            <x-ui.button-icon :href="route('admin.organizations.create')" variant="solid" icon="plus">
                {{ __('Tambah Perusahaan') }}
            </x-ui.button-icon>
        </div>
    </x-slot>
    <div class="card overflow-hidden p-0">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Nama') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Email') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Pengguna') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500">{{ __('Aksi') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($organizations as $org)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-ink">{{ $org->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $org->email ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $org->users_count }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="{{ $org->is_active ? 'text-green-600' : 'text-red-600' }}">{{ $org->is_active ? __('Aktif') : __('Nonaktif') }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <x-ui.icon-action :href="route('admin.organizations.edit', $org)" variant="primary" :title="__('Edit perusahaan')">
                                <x-ui.icon name="pencil" />
                            </x-ui.icon-action>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $organizations->links() }}
</x-app-layout>
