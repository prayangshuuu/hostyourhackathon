<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background: var(--bg); color: var(--text-primary);">
    @if(session('impersonating_from'))
        <div style="position: fixed; top: 0; width: 100%; z-index: 200; height: 40px; background: #fef3c7; border-bottom: 1px solid #fde68a; color: #92400e; font-size: 13px; font-weight: 500; display: flex; align-items: center; justify-content: space-between; padding: 0 24px;">
            <div>Impersonating: {{ Auth::user()->name }} ({{ Auth::user()->email }})</div>
            <a href="{{ route('admin.impersonate.exit') }}" style="color: #92400e; text-decoration: underline;">Exit Impersonation</a>
        </div>
    @endif
    
    <div style="display: grid; grid-template-columns: 240px 1fr; grid-template-rows: 56px 1fr; min-height: 100vh; {{ session('impersonating_from') ? 'margin-top: 40px;' : '' }}">
        
        @include('partials.navbar')
        @include('partials.sidebar')

        <main style="grid-column: 2; grid-row: 2; padding: 32px; background: var(--bg);">
            <div style="max-width: 1200px; margin: 0 auto;">
                @if (session('success'))
                    <x-alert type="success" :message="session('success')" />
                @endif
                @if (session('error'))
                    <x-alert type="error" :message="session('error')" />
                @endif
                @if (session('warning'))
                    <x-alert type="warning" :message="session('warning')" />
                @endif
                @if (session('info'))
                    <x-alert type="info" :message="session('info')" />
                @endif

                @yield('content')
                {{ $slot ?? '' }}
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
