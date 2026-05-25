<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-ink">{{ __('Assessment History') }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ __('All your completed assessments') }}</p>
        </div>
    </x-slot>

    @if ($results->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="empty-title">{{ __('No Results Yet') }}</h3>
            <p class="empty-text">{{ __('Complete an assessment to see your results here.') }}</p>
            <div class="mt-6 flex justify-center">
                <x-ui.button-icon :href="route('assessments.index')" variant="solid" icon="play">
                    {{ __('Kerjakan Assessment') }}
                </x-ui.button-icon>
            </div>
        </div>
    @else
        <div class="card overflow-hidden p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/50">
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Assessment') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Date') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($results as $result)
                            <tr class="transition-colors hover:bg-gray-50/50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-8 w-8 items-center justify-center rounded-lg text-xs font-bold"
                                              style="background-color: {{ $result->assessment?->color ?? '#0F172A' }}20; color: {{ $result->assessment?->color ?? '#0F172A' }}">
                                            {{ substr($result->test_name, 0, 2) }}
                                        </span>
                                        <span class="font-medium text-ink">{{ $result->assessment?->name ?? $result->test_name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-500">{{ $result->created_at->format('d M Y H:i') }}</td>
                                <td class="px-6 py-4">
                                    <span class="badge-green">{{ __('Completed') }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <x-ui.icon-action
                                        :href="route('assessments.result', [$result->test_name, $result->id])"
                                        variant="primary"
                                        :title="__('Lihat hasil')"
                                    >
                                        <x-ui.icon name="eye" />
                                    </x-ui.icon-action>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if ($results->hasPages())
            <div class="mt-6">
                {{ $results->links() }}
            </div>
        @endif
    @endif
</x-app-layout>
