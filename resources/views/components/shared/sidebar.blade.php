<aside class="fixed inset-y-0 left-0 z-30 hidden w-64 flex-col bg-navy-500 lg:flex">
    <div class="flex h-16 items-center gap-3 border-b border-white/10 px-6">
        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-sky-500 text-sm font-bold text-white">S</span>
        <span class="text-base font-bold text-white">Skillana</span>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        <x-shared.sidebar-nav />
    </nav>

    <div class="border-t border-white/10 px-3 py-3">
        <button x-data="{ dark: localStorage.getItem('dark') === 'true' }"
                x-init="if (dark) document.documentElement.classList.add('dark')"
                @click="dark = !dark; document.documentElement.classList.toggle('dark'); localStorage.setItem('dark', dark)"
                class="sidebar-link mb-1 w-full">
            <svg x-show="!dark" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            <svg x-show="dark" x-cloak class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span x-text="dark ? '{{ __('Light Mode') }}' : '{{ __('Dark Mode') }}'"></span>
        </button>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-link w-full">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span>{{ __('Log Out') }}</span>
            </button>
        </form>
    </div>
</aside>

<aside x-show="sidebarOpen" x-cloak class="fixed inset-0 z-40 lg:hidden">
    <div class="fixed inset-0 bg-navy-500/60 backdrop-blur-sm" @click="sidebarOpen = false"></div>
    <div class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-navy-500 shadow-xl">
        <div class="flex h-16 items-center justify-between border-b border-white/10 px-6">
            <span class="flex items-center gap-2.5 text-base font-bold text-white">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-sky-500 text-xs font-bold text-white">S</span>
                Skillana
            </span>
            <button @click="sidebarOpen = false" class="text-white/60 hover:text-white">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
            <x-shared.sidebar-nav />
        </nav>
        <div class="border-t border-white/10 px-3 py-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-link w-full">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>{{ __('Log Out') }}</span>
                </button>
            </form>
        </div>
    </div>
</aside>
