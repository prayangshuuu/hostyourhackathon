<header class="topnav" style="{{ session('impersonating_from') ? 'top: 32px;' : '' }}">
    <a href="{{ route('home') }}" class="topnav-logo">
        <div class="topnav-logo-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <path d="m8 3 4 8 5-5 5 15H2L8 3z"/>
            </svg>
        </div>
        <span>{{ $appSettings->get('app_name', 'HostYourHackathon') }}</span>
    </a>

    <div class="topnav-spacer"></div>

    <div class="topnav-actions">
        <a href="{{ route('notifications.index') }}" class="btn btn-ghost btn-icon btn-sm" style="position: relative;">
            <x-heroicon-o-bell class="w-5 h-5" />
            @if($unreadCount > 0)
                <span style="position: absolute; top: 4px; right: 4px; width: 8px; height: 8px; background: var(--danger); border-radius: 99px; border: 2px solid var(--surface);"></span>
            @endif
        </a>
        
        <div style="width: 1px; height: 20px; background: var(--border); margin: 0 8px;"></div>

        <div style="display: flex; align-items: center; gap: 10px;">
            <div style="text-align: right; line-height: 1.2;">
                <div style="font-size: 13px; font-weight: 600; color: var(--text-primary);">{{ Auth::user()->name }}</div>
                <div style="font-size: 11px; color: var(--text-muted);">{{ ucfirst(Auth::user()->roles->first()?->name ?? 'User') }}</div>
            </div>
            <a href="{{ route('profile.show') }}" class="avatar avatar-md avatar-default">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </a>
        </div>
    </div>
</header>
