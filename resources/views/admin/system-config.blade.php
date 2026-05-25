<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-ink">{{ __('System Configuration') }}</h2>
    </x-slot>

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @can('organizations.manage')
            <a href="{{ route('admin.organizations.index') }}" class="card hover:border-sky-300 transition-colors">
                <h3 class="font-semibold text-ink">{{ __('Perusahaan (Tenant)') }}</h3>
                <p class="mt-2 text-sm text-gray-500">{{ __('Kelola perusahaan multi-tenant dan isolasi data.') }}</p>
            </a>
        @endcan
        @can('users.assign_role')
            <a href="{{ route('admin.hr-users.index') }}" class="card hover:border-sky-300 transition-colors">
                <h3 class="font-semibold text-ink">{{ __('HR / Admin') }}</h3>
                <p class="mt-2 text-sm text-gray-500">{{ __('Buat dan assign akun HR per perusahaan.') }}</p>
            </a>
        @endcan
        @can('assessments.manage_global')
            <a href="{{ route('admin.system.assessments.index') }}" class="card hover:border-sky-300 transition-colors">
                <h3 class="font-semibold text-ink">{{ __('Assessment Global') }}</h3>
                <p class="mt-2 text-sm text-gray-500">{{ __('Konfigurasi template assessment seluruh platform.') }}</p>
            </a>
        @endcan
    </div>
</x-app-layout>
