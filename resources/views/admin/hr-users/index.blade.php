<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-ink">{{ __('Kelola HR / Admin') }}</h2>
            <x-ui.button-icon :href="route('admin.hr-users.create')" variant="solid" icon="plus">
                {{ __('Tambah HR/Admin') }}
            </x-ui.button-icon>
        </div>
    </x-slot>
    <div class="card overflow-hidden p-0">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Nama') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Email') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Perusahaan') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500">{{ __('Aksi') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($hrUsers as $hr)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-ink">{{ $hr->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $hr->email }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $hr->organization?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-right">
                            <x-ui.icon-action :href="route('admin.hr-users.edit', $hr)" variant="primary" :title="__('Edit akun HR')">
                                <x-ui.icon name="pencil" />
                            </x-ui.icon-action>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $hrUsers->links() }}
</x-app-layout>
