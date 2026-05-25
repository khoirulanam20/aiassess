<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold text-ink">{{ $batch->name }}</h2>
                @if ($batch->description)
                    <p class="mt-1 text-sm text-gray-500">{{ $batch->description }}</p>
                @endif
            </div>
            <div class="flex items-center gap-2">
                @if ($batch->status === 'active')
                    @can('update', $batch)
                        <form method="POST" action="{{ route('admin.batches.close', $batch) }}" class="inline" onsubmit="return confirm('{{ __('Tutup batch ini?') }}')">
                            @csrf
                            @method('PATCH')
                            <x-ui.button-icon type="submit" variant="secondary" icon="lock-closed">
                                {{ __('Tutup Batch') }}
                            </x-ui.button-icon>
                        </form>
                    @endcan
                @endif
                <x-ui.button-icon :href="route('admin.batches.index')" variant="ghost" icon="arrow-left">
                    {{ __('Daftar batch') }}
                </x-ui.button-icon>
            </div>
        </div>
    </x-slot>

    @include('admin.batches.partials.share-modal', [
        'open' => session('share_created', false),
        'candidateName' => session('share_candidate_name', ''),
        'shareUrl' => session('share_url', route('shared.batch.entry')),
        'accessCode' => session('access_code', ''),
    ])

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <div class="card">
            <p class="text-xs font-medium uppercase text-gray-500">{{ __('Status') }}</p>
            <p class="mt-1 text-lg font-semibold text-ink">
                {{ $batch->status === 'active' ? __('Aktif') : __('Ditutup') }}
            </p>
        </div>
        <div class="card">
            <p class="text-xs font-medium uppercase text-gray-500">{{ __('Total penugasan') }}</p>
            <p class="mt-1 text-lg font-semibold text-ink">{{ $batch->assignments->count() }}</p>
        </div>
        <div class="card">
            <p class="text-xs font-medium uppercase text-gray-500">{{ __('Selesai') }}</p>
            <p class="mt-1 text-lg font-semibold text-green-600">
                {{ $batch->assignments->where('status', 'completed')->count() }}
            </p>
        </div>
    </div>

    @if ($assessmentSummary->isNotEmpty())
        <section class="card mb-6">
            <h3 class="mb-3 font-semibold text-ink">{{ __('Ringkasan per assessment') }}</h3>
            <div class="flex flex-wrap gap-2">
                @foreach ($assessmentSummary as $row)
                    <span class="rounded-lg bg-gray-100 px-3 py-1.5 text-sm text-gray-800">
                        {{ $row['assessment']->name }}: {{ $row['completed'] }}/{{ $row['total'] }}
                    </span>
                @endforeach
            </div>
        </section>
    @endif

    <div class="card overflow-hidden p-0">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Kandidat') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Progress') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500">{{ __('Aksi') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($candidates as $row)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-ink">{{ $row['user']->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $row['completed'] }} / {{ $row['total'] }} {{ __('selesai') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="inline-flex items-center justify-end gap-1">
                                @can('results.share_create')
                                    <form method="POST" action="{{ route('admin.batches.candidates.share', [$batch, $row['user']]) }}" class="inline">
                                        @csrf
                                        <x-ui.icon-action
                                            type="submit"
                                            variant="primary"
                                            :title="$row['share'] ? __('Buat kode akses baru') : __('Share ke kandidat')"
                                        >
                                            <x-ui.icon :name="$row['share'] ? 'refresh' : 'share'" />
                                        </x-ui.icon-action>
                                    </form>
                                @endcan
                                <x-ui.icon-action
                                    :href="route('admin.batches.candidates.show', [$batch, $row['user']])"
                                    variant="primary"
                                    :title="__('Detail assessment kandidat')"
                                >
                                    <x-ui.icon name="eye" />
                                </x-ui.icon-action>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-500">{{ __('Belum ada kandidat di batch ini.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
