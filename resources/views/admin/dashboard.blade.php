<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-ink dark:text-white">{{ __('Admin Dashboard') }}</h2>
    </x-slot>

    <div>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <div class="kpi-card">
                <p class="kpi-label">{{ __('Total Users') }}</p>
                <p class="kpi-value">{{ $totalUsers }}</p>
            </div>
            <div class="kpi-card">
                <p class="kpi-label">{{ __('Total Results') }}</p>
                <p class="kpi-value">{{ $totalResults }}</p>
            </div>
            <div class="kpi-card">
                <p class="kpi-label">{{ __('Active Assessments') }}</p>
                <p class="kpi-value">{{ $assessments->count() }}</p>
            </div>
        </div>

        <div class="mt-8">
            <h3 class="card-title mb-4 text-ink dark:text-white">{{ __('Recent Results') }}</h3>
            <div class="card overflow-hidden p-0">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <caption class="sr-only">{{ __('Recent Results') }}</caption>
                        <thead class="bg-surface-secondary dark:bg-navy-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-ink-muted">{{ __('User') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-ink-muted">{{ __('Assessment') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-ink-muted">{{ __('Date') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($recentResults as $result)
                                <tr class="hover:bg-surface-secondary dark:hover:bg-navy-700">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-ink dark:text-white">{{ $result['user']['name'] ?? __('Unknown') }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-ink dark:text-white">{{ $result['assessment']['name'] ?? $result['test_name'] }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-ink-muted">{{ \Carbon\Carbon::parse($result['created_at'])->format('d M Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-sm text-ink-muted">{{ __('No results yet.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
