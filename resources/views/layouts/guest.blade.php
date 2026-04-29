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
    
    <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px;">
        <div style="width: 100%; max-width: 420px; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-xl); padding: 40px;">
            <div style="text-align: center; margin-bottom: 32px;">
                <a href="/" style="display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; background: var(--accent); border-radius: 8px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="3" y1="9" x2="21" y2="9"></line>
                        <line x1="9" y1="21" x2="9" y2="9"></line>
                    </svg>
                </a>
            </div>

            @if (session('status'))
                <div style="margin-bottom: 16px; font-size: 14px; color: var(--success);">
                    {{ session('status') }}
                </div>
            @endif

            {{ $slot ?? '' }}
            @yield('content')
        </div>
    </div>

</body>
</html>
