<nav style="height: 56px; background: var(--surface); border-bottom: 1px solid var(--border); position: fixed; top: 0; left: 0; width: 100%; z-index: 50; display: flex; align-items: center; justify-content: space-between; padding: 0 24px;">
    {{-- Left: Logo --}}
    <a href="/" style="display: flex; align-items: center; gap: 8px; text-decoration: none;">
        <div style="width: 16px; height: 16px; background: var(--accent); border-radius: 2px;"></div>
        <span style="font-size: 15px; font-weight: 600; color: var(--text-primary);">HostYourHackathon</span>
    </a>

    {{-- Right --}}
    <div style="display: flex; align-items: center; gap: 16px;">
        @auth
            {{-- Notification Bell --}}
            <button style="background: transparent; border: none; cursor: pointer; color: var(--text-secondary); display: flex; align-items: center;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
            </button>

            {{-- User Avatar Dropdown --}}
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
        @else
            <a href="{{ route('login') }}" style="font-size: 14px; font-weight: 500; color: var(--text-secondary); text-decoration: none;">Log in</a>
            <x-button href="{{ route('register') }}" variant="primary" size="md">Sign up</x-button>
        @endauth
    </div>
</nav>
