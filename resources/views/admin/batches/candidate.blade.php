<x-app-layout>
    <x-slot name="header">
        <div>
            <x-ui.button-icon :href="route('admin.batches.show', $batch)" variant="ghost" icon="arrow-left">
                {{ __('Kembali ke batch') }}
            </x-ui.button-icon>
            <h2 class="mt-2 text-xl font-semibold text-ink">{{ $candidate->name }}</h2>
            <p class="mt-1 text-sm text-gray-500">{{ __('Batch') }}: {{ $batch->name }}</p>
        </div>
    </x-slot>

    <div class="card overflow-hidden p-0">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Assessment') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Selesai') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500">{{ __('Aksi') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($assignments as $assignment)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-ink">{{ $assignment->assessment->name }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if ($assignment->status === 'completed')
                                <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">{{ __('Selesai') }}</span>
                            @else
                                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800">{{ __('Menunggu') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $assignment->completed_at?->format('d M Y H:i') ?? '—' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if ($assignment->result)
                                <x-ui.icon-action
                                    :href="route('admin.batches.candidates.results.show', [$batch, $candidate, $assignment->result])"
                                    variant="primary"
                                    :title="__('Lihat hasil')"
                                >
                                    <x-ui.icon name="chart" />
                                </x-ui.icon-action>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
