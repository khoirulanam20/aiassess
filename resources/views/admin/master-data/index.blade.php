<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-ink">{{ __('Master Data') }}</h2>
    </x-slot>

    <p class="mb-6 text-sm text-gray-500">
        {{ __('Kelola data referensi departemen dan posisi untuk kandidat/karyawan perusahaan Anda.') }}
    </p>

    <div class="grid gap-6 sm:grid-cols-2">
        <a href="{{ route('admin.master-data.departments.index') }}" class="card transition-colors hover:border-sky-300">
            <div class="flex items-start gap-4">
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-50 text-sky-600">
                    <x-ui.icon name="squares" class="h-6 w-6" />
                </span>
                <div>
                    <h3 class="font-semibold text-ink">{{ __('Departemen') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Struktur unit kerja dalam perusahaan') }}</p>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.master-data.positions.index') }}" class="card transition-colors hover:border-sky-300">
            <div class="flex items-start gap-4">
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-50 text-purple-600">
                    <x-ui.icon name="cog" class="h-6 w-6" />
                </span>
                <div>
                    <h3 class="font-semibold text-ink">{{ __('Posisi') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Jabatan atau role karyawan') }}</p>
                </div>
            </div>
        </a>
    </div>
</x-app-layout>
