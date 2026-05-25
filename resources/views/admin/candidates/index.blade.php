<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-ink">{{ __('Kandidat / Karyawan') }}</h2>
            @can('candidates.create')
                <x-ui.button-icon :href="route('admin.candidates.create')" variant="solid" icon="plus">
                    {{ __('Tambah Kandidat') }}
                </x-ui.button-icon>
            @endcan
        </div>
    </x-slot>
    <div class="card overflow-hidden p-0">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Nama') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Email') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('ID Karyawan') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Departemen') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500">{{ __('Aksi') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($candidates as $candidate)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-ink">{{ $candidate->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $candidate->email }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $candidate->userDetail?->employee_id ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $candidate->userDetail?->department ?? '—' }}</td>
                        <td class="px-6 py-4 text-right">
                            <x-ui.icon-action :href="route('admin.candidates.edit', $candidate)" variant="primary" :title="__('Edit kandidat')">
                                <x-ui.icon name="pencil" />
                            </x-ui.icon-action>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">{{ __('Belum ada kandidat.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $candidates->links() }}
</x-app-layout>
