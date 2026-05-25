<header class="flex h-16 shrink-0 items-center border-b border-gray-200 bg-white px-6 transition-colors dark:border-gray-700 dark:bg-navy-800 lg:px-8">
    <button @click="sidebarOpen = !sidebarOpen"
            class="-ml-2 mr-4 rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-navy-600 lg:hidden"
            aria-label="Buka sidebar">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    <div class="flex flex-1 items-center justify-end gap-3">
        <button x-data="{ lang: '{{ app()->getLocale() }}' }"
                @click="lang = lang === 'id' ? 'en' : 'id'; window.location = '{{ url('/lang') }}/' + lang"
                class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-gray-500 transition-colors hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-navy-600"
                :aria-label="lang === 'id' ? 'Ganti ke bahasa Inggris' : 'Switch to Indonesian'">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span x-text="lang.toUpperCase()" class="font-semibold"></span>
        </button>

        <div class="h-5 w-px bg-gray-200 dark:bg-gray-600"></div>

        <a href="{{ route('profile.edit') }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-gray-600 transition-colors hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-navy-600">
            <span class="hidden text-right sm:block">
                <span class="flex items-center justify-end gap-2">
                    <span class="block text-sm font-medium text-ink dark:text-white">{{ Auth::user()->name }}</span>
                    @if (Auth::user()->roles->isNotEmpty())
                        <span class="rounded-full bg-sky-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-sky-700 dark:bg-sky-900 dark:text-sky-300">
                            {{ Auth::user()->roles->first()->name }}
                        </span>
                    @endif
                </span>
                <span class="block text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</span>
            </span>
            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-sky-100 text-sm font-semibold text-sky-600 dark:bg-sky-900 dark:text-sky-300">
                {{ substr(Auth::user()->name, 0, 2) }}
            </span>
        </a>
    </div>
</header>
