@php
    $user = auth()->user();
    $isSuperadmin = $user?->hasRole('superadmin');
    $isAdmin = $user?->hasRole('admin');
@endphp

@if ($isAdmin)
    <div class="my-4 border-t border-white/10"></div>
    <p class="px-3 text-xs font-medium uppercase tracking-wider text-white/40">Admin / HR</p>

    @can('admin.dashboard')
        <a href="{{ route('admin.dashboard') }}"
           class="{{ request()->routeIs('admin.dashboard') ? 'sidebar-link-active border-l-[3px] border-sky-400' : 'sidebar-link' }}">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Dashboard</span>
        </a>
    @endcan

    @can('organizations.view_own')
        <a href="{{ route('admin.company.edit') }}"
           class="{{ request()->routeIs('admin.company.*') ? 'sidebar-link-active border-l-[3px] border-sky-400' : 'sidebar-link' }}">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span>Perusahaan</span>
        </a>
    @endcan

    @if ($isAdmin && auth()->user()->can('master_data.view'))
        <a href="{{ route('admin.master-data.index') }}"
           class="{{ request()->routeIs('admin.master-data.*') ? 'sidebar-link-active border-l-[3px] border-sky-400' : 'sidebar-link' }}">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
            </svg>
            <span>Master Data</span>
        </a>
    @endif

    @can('candidates.view')
        <a href="{{ route('admin.candidates.index') }}"
           class="{{ request()->routeIs('admin.candidates.*') ? 'sidebar-link-active border-l-[3px] border-sky-400' : 'sidebar-link' }}">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span>Kandidat / Karyawan</span>
        </a>
    @endcan

    @can('batches.view')
        <a href="{{ route('admin.batches.index') }}"
           class="{{ request()->routeIs('admin.batches.*') ? 'sidebar-link-active border-l-[3px] border-sky-400' : 'sidebar-link' }}">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <span>Batch Assessment</span>
        </a>
    @endcan

    @can('assessments.manage_org')
        <a href="{{ route('admin.assessments.org.index') }}"
           class="{{ request()->routeIs('admin.assessments.org.*') ? 'sidebar-link-active border-l-[3px] border-sky-400' : 'sidebar-link' }}">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
            </svg>
            <span>Konfig Assessment</span>
        </a>
    @endcan
@endif

@if ($isSuperadmin)
    <div class="my-4 border-t border-white/10"></div>
    <p class="px-3 text-xs font-medium uppercase tracking-wider text-white/40">Superadmin</p>

    @can('admin.dashboard')
        <a href="{{ route('admin.dashboard') }}"
           class="{{ request()->routeIs('admin.dashboard') ? 'sidebar-link-active border-l-[3px] border-sky-400' : 'sidebar-link' }}">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Dashboard Sistem</span>
        </a>
    @endcan

    @can('organizations.manage')
        <a href="{{ route('admin.organizations.index') }}"
           class="{{ request()->routeIs('admin.organizations.*') ? 'sidebar-link-active border-l-[3px] border-sky-400' : 'sidebar-link' }}">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span>Perusahaan (Tenant)</span>
        </a>
    @endcan

    @can('users.assign_role')
        <a href="{{ route('admin.hr-users.index') }}"
           class="{{ request()->routeIs('admin.hr-users.*') ? 'sidebar-link-active border-l-[3px] border-sky-400' : 'sidebar-link' }}">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span>Kelola HR/Admin</span>
        </a>
    @endcan

    @can('system.config')
        <a href="{{ route('admin.system.config') }}"
           class="{{ request()->routeIs('admin.system.config') ? 'sidebar-link-active border-l-[3px] border-sky-400' : 'sidebar-link' }}">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span>System Config</span>
        </a>
    @endcan

    @can('assessments.manage_global')
        <a href="{{ route('admin.system.assessments.index') }}"
           class="{{ request()->routeIs('admin.system.assessments.*') ? 'sidebar-link-active border-l-[3px] border-sky-400' : 'sidebar-link' }}">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span>Assessment Global</span>
        </a>
    @endcan
@endif
