{{-- Participant Sidebar --}}
<aside class="sidebar" role="navigation" aria-label="Participant navigation">
    {{-- Logo --}}
    <div class="sidebar-logo">
        <a href="{{ route('dashboard') }}" style="text-decoration:none; display:flex; align-items:center; gap:10px;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="24" height="24" rx="6" fill="var(--color-accent)"/>
                <path d="M7 12h10M12 7v10" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <span style="font-size:var(--font-size-md); font-weight:var(--font-weight-semibold); color:var(--color-text-primary);">
                {{ config('app.name') }}
            </span>
        </a>
    </div>

    {{-- Navigation --}}
    <nav>
        <div class="sidebar-section-label">General</div>

        <a href="{{ route('dashboard') }}"
           class="sidebar-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M2 2h5.333v5.333H2V2Zm6.667 0H14v3.333H8.667V2ZM2 9.333h5.333V14H2V9.333Zm6.667-1.333H14V14H8.667V8Z" fill="currentColor"/></svg>
            Dashboard
        </a>

        <div class="sidebar-section-label">Participate</div>

        <a href="{{ route('teams.index') }}"
           class="sidebar-nav-item {{ request()->routeIs('teams.*') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M6 7.333A2.667 2.667 0 1 0 6 2a2.667 2.667 0 0 0 0 5.333ZM2 13.333v-.666A3.333 3.333 0 0 1 5.333 9.333h1.334A3.333 3.333 0 0 1 10 12.667v.666M10.667 2.133a2.667 2.667 0 0 1 0 5.067M14 13.333v-.666a3.333 3.333 0 0 0-2.667-3.254" stroke="currentColor" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/></svg>
            My Teams
        </a>

        <a href="#"
           class="sidebar-nav-item {{ request()->routeIs('submissions.*') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M9.333 1.333H4A1.333 1.333 0 0 0 2.667 2.667v10.666A1.333 1.333 0 0 0 4 14.667h8a1.333 1.333 0 0 0 1.333-1.334V5.333l-4-4Z" stroke="currentColor" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/><path d="M9.333 1.333v4h4M10.667 8.667H5.333M10.667 11.333H5.333M6.667 6H5.333" stroke="currentColor" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Submissions
        </a>

        <a href="#"
           class="sidebar-nav-item {{ request()->routeIs('participant.announcements.*') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M12 5.333A4 4 0 0 0 4 5.333c0 4.667-2 6-2 6h12s-2-1.333-2-6ZM9.153 14a1.333 1.333 0 0 1-2.306 0" stroke="currentColor" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Announcements
            @php $unreadCount = Auth::user()->unreadNotifications()->count(); @endphp
            @if ($unreadCount > 0)
                <span class="sidebar-badge">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
            @endif
        </a>
    </nav>

    {{-- User section at bottom --}}
    <div class="sidebar-user">
        <div class="sidebar-user-avatar" aria-hidden="true">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
        <div class="sidebar-user-info">
            <div class="sidebar-user-name">{{ Auth::user()->name }}</div>
            <div class="sidebar-user-email">{{ Auth::user()->email }}</div>
        </div>
        <form method="POST" action="{{ route('logout') }}" style="margin-left:auto;">
            @csrf
            <button type="submit" class="btn-icon" aria-label="Log out" title="Log out">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M6 14H3.333A.667.667 0 0 1 2.667 13.333V2.667A.667.667 0 0 1 3.333 2H6m4.667 9.333L14 8m0 0-3.333-3.333M14 8H6" stroke="currentColor" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </form>
    </div>
</aside>
