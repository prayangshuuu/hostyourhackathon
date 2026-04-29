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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background: var(--bg); font-family: 'Inter', sans-serif; color: var(--text-primary); margin: 0; padding: 0;">
    
    <div style="display: grid; grid-template-columns: 240px 1fr; grid-template-rows: 56px 1fr; min-height: 100vh;">
        
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

                @yield('content')
                {{ $slot ?? '' }}
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
