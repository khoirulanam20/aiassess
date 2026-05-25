<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-ink">{{ __('Batch Assessment') }}</h2>
            @can('create', \App\Models\AssessmentBatch::class)
                <x-ui.button-icon :href="route('admin.batches.create')" variant="solid" icon="plus">
                    {{ __('Buat Batch') }}
                </x-ui.button-icon>
            @endcan
        </div>
    </x-slot>

    <div class="card overflow-hidden p-0">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Nama Batch') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Penugasan') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Dibuat') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500">{{ __('Aksi') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($batches as $batch)
                    <tr>
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-ink">{{ $batch->name }}</p>
                            @if ($batch->description)
                                <p class="text-xs text-gray-500 line-clamp-1">{{ $batch->description }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if ($batch->status === 'active')
                                <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">{{ __('Aktif') }}</span>
                            @else
                                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">{{ __('Ditutup') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $batch->completed_count }} / {{ $batch->assignments_count }} {{ __('selesai') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $batch->created_at->format('d M Y') }}
                            <span class="block text-xs">{{ $batch->creator?->name }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <x-ui.icon-action :href="route('admin.batches.show', $batch)" variant="primary" :title="__('Detail batch')">
                                <x-ui.icon name="eye" />
                            </x-ui.icon-action>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                            {{ __('Belum ada batch. Buat batch untuk menugaskan assessment ke kandidat.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $batches->links() }}
</x-app-layout>
