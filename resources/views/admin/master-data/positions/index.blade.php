<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <x-ui.button-icon :href="route('admin.master-data.index')" variant="ghost" icon="arrow-left">
                    {{ __('Master Data') }}
                </x-ui.button-icon>
                <h2 class="mt-2 text-xl font-semibold text-ink">{{ __('Posisi') }}</h2>
            </div>
            @can('create', \App\Models\Position::class)
                <x-ui.button-icon :href="route('admin.master-data.positions.create')" variant="solid" icon="plus">
                    {{ __('Tambah Posisi') }}
                </x-ui.button-icon>
            @endcan
        </div>
    </x-slot>

    <div class="card overflow-hidden p-0">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Nama') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Departemen') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Kode') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Karyawan') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500">{{ __('Aksi') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($positions as $position)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-ink">{{ $position->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $position->department?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $position->code ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $position->user_details_count }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if ($position->is_active)
                                <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">{{ __('Aktif') }}</span>
                            @else
                                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">{{ __('Nonaktif') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="inline-flex items-center justify-end gap-1">
                                @can('update', $position)
                                    <x-ui.icon-action :href="route('admin.master-data.positions.edit', $position)" variant="primary" :title="__('Edit posisi')">
                                        <x-ui.icon name="pencil" />
                                    </x-ui.icon-action>
                                @endcan
                                @can('delete', $position)
                                    <form method="POST" action="{{ route('admin.master-data.positions.destroy', $position) }}" class="inline" onsubmit="return confirm('{{ __('Hapus posisi ini?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.icon-action type="submit" variant="danger" :title="__('Hapus posisi')">
                                            <x-ui.icon name="trash" />
                                        </x-ui.icon-action>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">{{ __('Belum ada posisi.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $positions->links() }}
</x-app-layout>
