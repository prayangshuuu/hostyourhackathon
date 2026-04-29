<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Organizer') — {{ config('app.name') }}</title>
    <meta name="description" content="@yield('meta_description', 'Manage your hackathons on ' . config('app.name'))">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    {{-- Scripts --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="organizer-shell">

    {{-- Impersonation Banner --}}
    @if (session('impersonating_from'))
        <div class="impersonation-banner">
            You are impersonating {{ session('impersonating_name', Auth::user()->name) }}
            <a href="{{ route('admin.stop-impersonation') }}" onclick="event.preventDefault(); document.getElementById('stop-impersonation-form').submit();">Exit Impersonation</a>
            <form id="stop-impersonation-form" method="POST" action="{{ route('admin.stop-impersonation') }}" style="display:none;">@csrf</form>
        </div>
        <div style="height:36px;"></div>
    @endif

    {{-- Sidebar --}}
    @include('components.organizer.sidebar')

    {{-- Main Content --}}
    <div class="main-content">
        <div class="main-content-inner">

            {{-- Top Bar with Notification Bell --}}
            <div style="display:flex; justify-content:flex-end; margin-bottom:8px;">
                @include('components.notification-bell')
            </div>

            {{-- Flash Messages --}}
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
            @if (session('warning'))
                <div class="alert alert-warning" id="flash-warning" role="alert">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M14.267 12.467 8.8 2.8a.933.933 0 0 0-1.6 0L1.733 12.467a.867.867 0 0 0 .8 1.2h10.934a.867.867 0 0 0 .8-1.2ZM8 11.333a.667.667 0 1 1 0-1.333.667.667 0 0 1 0 1.333Zm.667-3.333a.667.667 0 0 1-1.334 0V5.333a.667.667 0 0 1 1.334 0V8Z" fill="currentColor"/></svg>
                    {{ session('warning') }}
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    {{-- Auto-dismiss flash messages --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            ['flash-success', 'flash-error', 'flash-warning'].forEach(function (id) {
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
