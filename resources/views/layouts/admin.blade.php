<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>
    <meta name="description" content="@yield('meta_description', 'Admin panel')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="organizer-shell">

    {{-- Impersonation Banner --}}
    @if (session('impersonating_from'))
        <div class="impersonation-banner">
            You are impersonating {{ session('impersonating_name', Auth::user()->name) }}
            <a href="{{ route('admin.stop-impersonation') }}">Exit Impersonation</a>
        </div>
        <div style="height:36px;"></div>
    @endif

    {{-- Sidebar --}}
    <aside class="sidebar" role="navigation" aria-label="Admin navigation">
        <div class="sidebar-logo">
            <a href="{{ route('admin.dashboard') }}" style="text-decoration:none; display:flex; align-items:center; gap:8px;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <rect width="24" height="24" rx="6" fill="var(--color-accent)"/>
                    <path d="M7 12h10M12 7v10" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <span style="font-size:var(--font-size-base); font-weight:var(--font-weight-semibold); color:var(--color-text-primary);">
                    {{ config('app.name') }}
                </span>
                <span class="admin-label">Admin</span>
            </a>
        </div>

        <nav>
            <div class="sidebar-section-label">Overview</div>

            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M2 2h5.333v5.333H2V2Zm6.667 0H14v3.333H8.667V2ZM2 9.333h5.333V14H2V9.333Zm6.667-1.333H14V14H8.667V8Z" fill="currentColor"/></svg>
                Dashboard
            </a>

            <div class="sidebar-section-label">Manage</div>

            <a href="{{ route('admin.users.index') }}"
               class="sidebar-nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M6 7.333A2.667 2.667 0 1 0 6 2a2.667 2.667 0 0 0 0 5.333ZM2 13.333v-.666A3.333 3.333 0 0 1 5.333 9.333h1.334A3.333 3.333 0 0 1 10 12.667v.666M10.667 2.133a2.667 2.667 0 0 1 0 5.067M14 13.333v-.666a3.333 3.333 0 0 0-2.667-3.254" stroke="currentColor" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Users
            </a>

            <a href="{{ route('admin.hackathons.index') }}"
               class="sidebar-nav-item {{ request()->routeIs('admin.hackathons.*') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M13.333 2H2.667A.667.667 0 0 0 2 2.667v10.666c0 .369.298.667.667.667h10.666a.667.667 0 0 0 .667-.667V2.667A.667.667 0 0 0 13.333 2ZM6 12.667H3.333V10H6v2.667Zm0-4H3.333V6H6v2.667Zm0-4H3.333V2H6v2.667Zm6.667 8H7.333V10h5.334v2.667Zm0-4H7.333V6h5.334v2.667Zm0-4H7.333V2h5.334v2.667Z" fill="currentColor"/></svg>
                Hackathons
            </a>

            <div class="sidebar-section-label">System</div>

            <a href="{{ route('admin.settings') }}"
               class="sidebar-nav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 10a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z" stroke="currentColor" stroke-width="1.33"/><path d="M12.933 10a1.1 1.1 0 0 0 .22 1.213l.04.04a1.333 1.333 0 1 1-1.886 1.887l-.04-.04A1.1 1.1 0 0 0 10 13.32v.013A1.333 1.333 0 1 1 7.333 13.333v-.073a1.1 1.1 0 0 0-.72-1.007 1.1 1.1 0 0 0-1.213.22l-.04.04a1.333 1.333 0 1 1-1.887-1.886l.04-.04A1.1 1.1 0 0 0 3.733 9.373 1.1 1.1 0 0 0 2.667 10h-.014A1.333 1.333 0 1 1 2.667 7.333h.073a1.1 1.1 0 0 0 1.007-.72 1.1 1.1 0 0 0-.22-1.213l-.04-.04a1.334 1.334 0 1 1 1.886-1.887l.04.04A1.1 1.1 0 0 0 6.627 3.733V3.72 2.667a1.333 1.333 0 0 1 2.666 0v.073a1.1 1.1 0 0 0 .72 1.007 1.1 1.1 0 0 0 1.214-.22l.04-.04a1.333 1.333 0 1 1 1.886 1.886l-.04.04A1.1 1.1 0 0 0 12.893 6.627v.013.027a1.1 1.1 0 0 0 .72 1 1.1 1.1 0 0 0 .054.02h.013-.014a1.333 1.333 0 0 1 0 2.666h-.073a1.1 1.1 0 0 0-1.007.72l-.653-.073Z" stroke="currentColor" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Settings
            </a>
        </nav>

        <div class="sidebar-user">
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ Auth::user()->name }}</div>
                <div class="sidebar-user-email">{{ Auth::user()->email }}</div>
            </div>
            <a href="{{ route('dashboard') }}" class="btn-icon" title="Exit Admin" style="margin-left:auto;">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M6 14H3.333A.667.667 0 0 1 2.667 13.333V2.667A.667.667 0 0 1 3.333 2H6m4.667 9.333L14 8m0 0-3.333-3.333M14 8H6" stroke="currentColor" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="main-content">
        <div class="main-content-inner">
            @if (session('success'))
                <div class="alert alert-success" id="flash-success" role="alert">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 1.333A6.667 6.667 0 1 0 14.667 8 6.674 6.674 0 0 0 8 1.333Zm3.06 4.94-3.667 4a.667.667 0 0 1-.986 0L4.94 8.607a.667.667 0 1 1 .986-.9l.96 1.046 3.174-3.46a.667.667 0 0 1 .986.9l.014.02Z" fill="currentColor"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-error" id="flash-error" role="alert">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 1.333A6.667 6.667 0 1 0 14.667 8 6.674 6.674 0 0 0 8 1.333Zm2.473 8.527a.667.667 0 0 1-.946.946L8 9.28l-1.527 1.527a.667.667 0 0 1-.946-.946L7.054 8.333 5.527 6.807a.667.667 0 0 1 .946-.947L8 7.387l1.527-1.527a.667.667 0 0 1 .946.947L8.946 8.333l1.527 1.527Z" fill="currentColor"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            ['flash-success', 'flash-error'].forEach(function (id) {
                const el = document.getElementById(id);
                if (el) {
                    setTimeout(function () {
                        el.style.transition = 'opacity 300ms ease, transform 300ms ease';
                        el.style.opacity = '0';
                        el.style.transform = 'translateY(-8px)';
                        setTimeout(function () { el.remove(); }, 300);
                    }, 4000);
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
