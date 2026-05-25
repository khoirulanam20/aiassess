<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-ink">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl px-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div class="card">
                    <dt class="text-sm font-medium text-gray-500">{{ __('Total Users') }}</dt>
                    <dd class="mt-1 text-3xl font-bold text-ink">{{ $totalUsers }}</dd>
                </div>
                <div class="card">
                    <dt class="text-sm font-medium text-gray-500">{{ __('Total Results') }}</dt>
                    <dd class="mt-1 text-3xl font-bold text-ink">{{ $totalResults }}</dd>
                </div>
                <div class="card">
                    <dt class="text-sm font-medium text-gray-500">{{ __('Active Assessments') }}</dt>
                    <dd class="mt-1 text-3xl font-bold text-ink">{{ $assessments->count() }}</dd>
                </div>
            </div>

            <div class="mt-8">
                <h3 class="text-lg font-semibold text-ink">{{ __('Recent Results') }}</h3>
                <div class="mt-4 overflow-hidden rounded-xl border border-gray-200 bg-white">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('User') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Assessment') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Date') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($recentResults as $result)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-ink">{{ $result['user']['name'] ?? __('Unknown') }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-ink">{{ $result['assessment']['name'] ?? $result['test_name'] }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ \Carbon\Carbon::parse($result['created_at'])->format('d M Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-500">{{ __('No results yet.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
