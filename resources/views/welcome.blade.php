<x-guest-layout>
    <section class="relative overflow-hidden bg-gradient-to-br from-navy-500 via-navy-500 to-navy-700">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wMyI+PHBhdGggZD0iTTM2IDM0djItSDI0di0yaDEyek0zNiAyNHYySDI0di0yaDEyeiIvPjwvZz48L2c+PC9zdmc+')] opacity-40"></div>
        <div class="relative mx-auto max-w-7xl px-6 py-24 sm:py-32 lg:px-8">
            <div class="text-center">
                <span class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-sky-500 text-2xl font-bold text-white shadow-lg shadow-sky-500/30">S</span>
                <h1 class="text-4xl font-bold tracking-tight text-white sm:text-6xl">
                    {{ __('Discover Your Potential') }}
                </h1>
                <p class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-gray-300">
                    {{ __('Comprehensive psychometric assessments to understand your personality, cognitive abilities, and professional strengths.') }}
                </p>
                <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                    <a href="{{ route('shared.batch.entry') }}" class="rounded-xl bg-emerald-500 px-8 py-4 text-base font-semibold text-white shadow-lg shadow-emerald-500/30 transition-all duration-150 hover:bg-emerald-600 hover:shadow-xl active:scale-[0.98]">
                        {{ __('Kerjakan Assessment') }}
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-xl bg-sky-500 px-8 py-4 text-base font-semibold text-white shadow-lg shadow-sky-500/30 transition-all duration-150 hover:bg-sky-600 hover:shadow-xl hover:shadow-sky-500/40 active:scale-[0.98]">
                            {{ __('Go to Dashboard') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-xl border border-white/20 px-8 py-4 text-base font-semibold text-white transition-all duration-150 hover:bg-white/10 active:scale-[0.98]">
                            {{ __('Sign In HR / Admin') }}
                        </a>
                    @endauth
                </div>
                <p class="mx-auto mt-4 max-w-md text-sm text-gray-400">
                    {{ __('Kandidat: gunakan tombol di atas dan masukkan kode 6 digit dari HR.') }}
                </p>
            </div>
        </div>
    </section>

    <section class="border-b border-gray-200 bg-white py-20">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-ink sm:text-3xl">{{ __('Why Choose Skillana?') }}</h2>
                <p class="mx-auto mt-4 max-w-xl text-gray-500">{{ __('Our assessments are scientifically designed to provide accurate insights') }}</p>
            </div>
            <div class="mt-16 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <div class="text-center">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-sky-50 text-sky-500">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="font-semibold text-ink">{{ __('Scientifically Validated') }}</h3>
                    <p class="mt-2 text-sm text-gray-500">{{ __('All assessments are based on established psychological frameworks') }}</p>
                </div>
                <div class="text-center">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-sky-50 text-sky-500">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="font-semibold text-ink">{{ __('Instant Results') }}</h3>
                    <p class="mt-2 text-sm text-gray-500">{{ __('Get your complete assessment results immediately after completion') }}</p>
                </div>
                <div class="text-center">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-sky-50 text-sky-500">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="font-semibold text-ink">{{ __('11 Assessment Types') }}</h3>
                    <p class="mt-2 text-sm text-gray-500">{{ __('From personality to cognitive, we cover every aspect of potential') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-surface-secondary py-20">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-ink sm:text-3xl">{{ __('Assessment Types') }}</h2>
                <p class="mx-auto mt-4 max-w-xl text-gray-500">{{ __('Choose from 11 different assessment types') }}</p>
            </div>
            <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @php
                    $items = [
                        ['MBTI', 'Kepribadian', '#1E3A5F'],
                        ['DISC', 'Perilaku', '#2563EB'],
                        ['PAPI', 'Kerja', '#059669'],
                        ['MSDT', 'Manajemen', '#DC2626'],
                        ['SPM', 'Logika', '#7C3AED'],
                        ['BI', 'Bisnis', '#F59E0B'],
                        ['Agility', 'Adaptasi', '#EC4899'],
                        ['Video', 'Interview', '#F43F5E'],
                    ];
                @endphp
                @foreach ($items as $item)
                    <div class="rounded-xl border border-gray-200 bg-white p-5 text-center shadow-card transition-all duration-200 hover:shadow-card-hover">
                        <span class="mx-auto mb-3 flex h-10 w-10 items-center justify-center rounded-lg text-sm font-bold"
                              style="background-color: {{ $item[2] }}15; color: {{ $item[2] }}">{{ substr($item[0], 0, 2) }}</span>
                        <h4 class="font-semibold text-ink">{{ $item[0] }}</h4>
                        <p class="mt-1 text-xs text-gray-500">{{ $item[1] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-white py-20">
        <div class="mx-auto max-w-2xl px-6 text-center lg:px-8">
            <h2 class="text-2xl font-bold text-ink sm:text-3xl">{{ __('Ready to Get Started?') }}</h2>
            <p class="mt-4 text-gray-500">{{ __('Begin your self-discovery journey today with our comprehensive assessments.') }}</p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                <x-ui.button-icon :href="route('shared.batch.entry')" variant="solid" icon="play" class="py-3 px-6">
                    {{ __('Kerjakan Assessment') }}
                </x-ui.button-icon>
                @auth
                    <x-ui.button-icon :href="route('dashboard')" variant="secondary" icon="squares" class="py-3 px-6">
                        {{ __('Dashboard') }}
                    </x-ui.button-icon>
                @else
                    <x-ui.button-icon :href="route('login')" variant="secondary" icon="arrow-right" class="py-3 px-6">
                        {{ __('Masuk HR / Admin') }}
                    </x-ui.button-icon>
                @endauth
            </div>
        </div>
    </section>
</x-guest-layout>
