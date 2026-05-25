<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-ink dark:text-white">{{ __('Assessment History') }}</h1>
            <p class="mt-1 text-sm text-ink-muted">{{ __('All your completed assessments') }}</p>
        </div>
    </x-slot>

    @if ($results->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="empty-title">{{ __('No Results Yet') }}</h3>
            <p class="empty-text">{{ __('Complete an assessment to see your results here.') }}</p>
            <div class="mt-6 flex justify-center">
                <a href="{{ route('assessments.index') }}" class="btn-primary inline-flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
                    {{ __('Kerjakan Assessment') }}
                </a>
            </div>
        </div>
    @else
        <div class="card overflow-hidden p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <caption class="sr-only">{{ __('Assessment History') }}</caption>
                    <thead>
                        <tr class="border-b border-gray-100 bg-surface-secondary dark:border-gray-700 dark:bg-navy-700">
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-ink-muted">{{ __('Assessment') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-ink-muted">{{ __('Date') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-ink-muted">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-ink-muted">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($results as $result)
                            <tr class="transition-colors hover:bg-surface-secondary dark:hover:bg-navy-700">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-8 w-8 items-center justify-center rounded-lg text-xs font-bold"
                                              style="background-color: {{ $result->assessment?->color ?? '#0F172A' }}20; color: {{ $result->assessment?->color ?? '#0F172A' }}">
                                            {{ substr($result->test_name, 0, 2) }}
                                        </span>
                                        <span class="font-medium text-ink dark:text-white">{{ $result->assessment?->name ?? $result->test_name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-ink-muted">{{ $result->created_at->format('d M Y H:i') }}</td>
                                <td class="px-6 py-4">
                                    <span class="badge-green">{{ __('Completed') }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('assessments.result', [$result->test_name, $result->id]) }}"
                                       class="btn-primary btn-sm inline-flex items-center gap-1" title="{{ __('Lihat hasil') }}">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        {{ __('Lihat') }}
                                    </a>
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
