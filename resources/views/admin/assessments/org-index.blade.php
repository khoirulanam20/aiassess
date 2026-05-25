<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-ink">{{ __('Assessment Perusahaan') }}: {{ $organization->name }}</h2></x-slot>
    <p class="mb-4 text-sm text-gray-500">{{ __('HR mengatur assessment yang aktif dan konfigurasi untuk perusahaan Anda.') }}</p>
    <div class="card overflow-hidden p-0">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Assessment') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">{{ __('Cooldown') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500">{{ __('Aksi') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($orgAssessments as $oa)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-ink">{{ $oa->displayName() }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="{{ $oa->is_enabled ? 'text-green-600' : 'text-gray-400' }}">{{ $oa->is_enabled ? __('Aktif') : __('Nonaktif') }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $oa->effectiveCooldownDays() }} {{ __('hari') }}</td>
                        <td class="px-6 py-4 text-right">
                            <x-ui.icon-action :href="route('admin.assessments.org.edit', $oa)" variant="primary" :title="__('Konfigurasi assessment')">
                                <x-ui.icon name="cog" />
                            </x-ui.icon-action>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
