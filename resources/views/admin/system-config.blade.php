<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-ink dark:text-white">{{ __('System Configuration') }}</h2>
    </x-slot>

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @can('organizations.manage')
            <a href="{{ route('admin.organizations.index') }}" class="card card-hover">
                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-sky-50 text-sky-500 dark:bg-navy-700 dark:text-sky-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h3 class="font-semibold text-ink dark:text-white">{{ __('Perusahaan (Tenant)') }}</h3>
                <p class="mt-2 text-sm text-ink-muted">{{ __('Kelola perusahaan multi-tenant dan isolasi data.') }}</p>
            </a>
        @endcan
        @can('users.assign_role')
            <a href="{{ route('admin.hr-users.index') }}" class="card card-hover">
                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-green-50 text-green-600 dark:bg-navy-700 dark:text-green-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <h3 class="font-semibold text-ink dark:text-white">{{ __('HR / Admin') }}</h3>
                <p class="mt-2 text-sm text-ink-muted">{{ __('Buat dan assign akun HR per perusahaan.') }}</p>
            </a>
        @endcan
        @can('assessments.manage_global')
            <a href="{{ route('admin.system.assessments.index') }}" class="card card-hover">
                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-600 dark:bg-navy-700 dark:text-amber-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <h3 class="font-semibold text-ink dark:text-white">{{ __('Assessment Global') }}</h3>
                <p class="mt-2 text-sm text-ink-muted">{{ __('Konfigurasi template assessment seluruh platform.') }}</p>
            </a>
        @endcan
    </div>
</x-app-layout>
