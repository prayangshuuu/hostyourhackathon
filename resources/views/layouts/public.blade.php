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
<body style="background: var(--bg); font-family: 'Inter', sans-serif; color: var(--text-primary); margin: 0; padding: 0; padding-top: 56px;">
    
    <x-navbar />

    <main style="max-width: 1200px; margin: 0 auto; padding: 0 24px; min-height: calc(100vh - 56px);">
        @yield('content')
        {{ $slot ?? '' }}
    </main>

    @stack('scripts')
</body>
</html>
