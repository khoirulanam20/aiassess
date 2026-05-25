<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-ink">{{ __('Konfigurasi Assessment (Global)') }}</h2></x-slot>
    <p class="mb-4 text-sm text-gray-500">{{ __('Superadmin mengatur template assessment untuk seluruh platform SaaS.') }}</p>
    <div class="card overflow-hidden p-0">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Nama') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Slug') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Tipe') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Cooldown') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500">{{ __('Aksi') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($assessments as $a)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-ink">{{ $a->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $a->slug }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $a->type }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $a->cooldown_days }} {{ __('hari') }}</td>
                        <td class="px-6 py-4 text-right">
                            <x-ui.icon-action :href="route('admin.system.assessments.edit', $a)" variant="primary" :title="__('Edit assessment')">
                                <x-ui.icon name="pencil" />
                            </x-ui.icon-action>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
