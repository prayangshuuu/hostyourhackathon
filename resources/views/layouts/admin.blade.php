<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $appSettings->get('app_name', config('app.name', 'HostYourHackathon')) }} - @yield('title', 'Admin') - Admin</title>

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
        
        {{-- Admin Navbar --}}
        <nav style="grid-column: 1 / -1; grid-row: 1; height: 56px; background: var(--surface); border-bottom: 1px solid var(--border); position: fixed; top: {{ session('impersonating_from') ? '40px' : '0' }}; left: 0; width: 100%; z-index: 50; display: flex; align-items: center; justify-content: space-between; padding: 0 24px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <a href="/" style="display: flex; align-items: center; gap: 8px; text-decoration: none;">
                    @if($appSettings->get('app_logo'))
                        <img src="{{ Storage::url($appSettings->get('app_logo')) }}" alt="{{ $appSettings->get('app_name', config('app.name')) }}" style="height: 28px; width: auto; object-fit: contain;">
                    @else
                        <div style="width: 16px; height: 16px; background: var(--accent); border-radius: 2px;"></div>
                    @endif
                    <span style="font-size: 15px; font-weight: 600; color: var(--text-primary);">{{ $appSettings->get('app_name', config('app.name')) }}</span>
                </a>
                <span style="font-size: 12px; background: var(--danger-light); color: var(--danger); border: 1px solid rgba(220,38,38,0.2); border-radius: 99px; padding: 2px 8px;">Admin Panel</span>
            </div>

            {{-- Right side --}}
            <div style="display: flex; align-items: center; gap: 16px;">
                <div x-data="{ open: false }" style="position: relative;">
                    <button @click="open = !open" @click.away="open = false" style="width: 32px; height: 32px; border-radius: 50%; background: var(--accent-light); color: var(--accent); border: none; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </button>

                    <div x-show="open" style="display: none; position: absolute; right: 0; top: 48px; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 6px; min-width: 200px;" x-transition>
                        <div style="padding: 8px 12px; font-size: 14px; font-weight: 500; color: var(--text-primary);">
                            {{ Auth::user()->name }}
                        </div>
                        <div style="height: 1px; background: var(--border); margin: 4px 0;"></div>
                        <a href="{{ route('profile.show') }}" style="display: block; padding: 8px 12px; font-size: 14px; color: var(--text-secondary); text-decoration: none; border-radius: var(--radius-md);" onmouseover="this.style.background='var(--surface-alt)'" onmouseout="this.style.background='transparent'">
                            Profile
                        </a>
                        <div style="height: 1px; background: var(--border); margin: 4px 0;"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" style="width: 100%; text-align: left; padding: 8px 12px; font-size: 14px; color: var(--danger); background: transparent; border: none; cursor: pointer; border-radius: var(--radius-md);" onmouseover="this.style.background='var(--surface-alt)'" onmouseout="this.style.background='transparent'">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        {{-- Admin Sidebar --}}
        <aside style="grid-column: 1; grid-row: 2; width: 240px; background: var(--surface); border-right: 1px solid var(--border); position: fixed; top: {{ session('impersonating_from') ? '96px' : '56px' }}; left: 0; height: calc(100vh - {{ session('impersonating_from') ? '96px' : '56px' }}); overflow-y: auto; z-index: 40; padding: 24px 16px;">
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" style="display: block; padding: 8px 12px; border-radius: var(--radius-md); text-decoration: none; font-size: 14px; font-weight: 500; color: {{ request()->routeIs('admin.dashboard') ? 'var(--accent)' : 'var(--text-secondary)' }}; background: {{ request()->routeIs('admin.dashboard') ? 'var(--accent-light)' : 'transparent' }};">
                    Dashboard
                </a>
                <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" style="display: block; padding: 8px 12px; border-radius: var(--radius-md); text-decoration: none; font-size: 14px; font-weight: 500; color: {{ request()->routeIs('admin.users.*') ? 'var(--accent)' : 'var(--text-secondary)' }}; background: {{ request()->routeIs('admin.users.*') ? 'var(--accent-light)' : 'transparent' }};">
                    Users
                </a>
                <a href="{{ route('admin.hackathons.index') }}" class="sidebar-link {{ request()->routeIs('admin.hackathons.*') ? 'active' : '' }}" style="display: block; padding: 8px 12px; border-radius: var(--radius-md); text-decoration: none; font-size: 14px; font-weight: 500; color: {{ request()->routeIs('admin.hackathons.*') ? 'var(--accent)' : 'var(--text-secondary)' }}; background: {{ request()->routeIs('admin.hackathons.*') ? 'var(--accent-light)' : 'transparent' }};">
                    Hackathons
                </a>
                <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" style="display: block; padding: 8px 12px; border-radius: var(--radius-md); text-decoration: none; font-size: 14px; font-weight: 500; color: {{ request()->routeIs('admin.settings.*') ? 'var(--accent)' : 'var(--text-secondary)' }}; background: {{ request()->routeIs('admin.settings.*') ? 'var(--accent-light)' : 'transparent' }};">
                    Settings
                </a>
            </div>
        </aside>

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
