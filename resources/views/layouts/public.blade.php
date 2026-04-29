<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <meta name="description" content="@yield('meta_description', 'Discover and join hackathons on ' . config('app.name'))">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="public-shell">
    <div class="public-container">
        {{-- Navigation --}}
        <nav class="public-nav">
            <a href="{{ url('/') }}" class="public-nav-logo">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <rect width="24" height="24" rx="6" fill="var(--color-accent)"/>
                    <path d="M7 12h10M12 7v10" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
                </svg>
                {{ config('app.name') }}
            </a>
            <div class="public-nav-actions">
                <a href="{{ route('hackathons.index') }}" class="btn btn-secondary btn-sm">Browse</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-secondary btn-sm">Sign In</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Register</a>
                @endauth
            </div>
        </nav>

        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
