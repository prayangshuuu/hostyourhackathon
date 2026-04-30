<aside style="width: 240px; height: calc(100vh - {{ session('impersonating_from') ? '96px' : '56px' }}); position: fixed; top: {{ session('impersonating_from') ? '96px' : '56px' }}; left: 0; background: var(--surface); border-right: 1px solid var(--border); padding: 16px 12px; overflow-y: auto; display: flex; flex-direction: column;">
    
    @php
        $role = Auth::user()->roles->first()->name ?? 'participant';
        $navItems = [];

        if ($role === 'participant') {
            $navItems = [
                ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'heroicon-o-home'],
                ['label' => 'Browse Hackathons', 'route' => 'hackathons.index', 'icon' => 'heroicon-o-globe-alt'],
                ['label' => 'My Teams', 'route' => 'teams.index', 'icon' => 'heroicon-o-user-group'],
            ];
        } elseif ($role === 'organizer') {
            $navItems = [
                ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'heroicon-o-home'],
                ['label' => 'My Hackathons', 'route' => 'organizer.hackathons.index', 'icon' => 'heroicon-o-calendar-days'],
                ['label' => 'Teams', 'route' => 'organizer.teams.index', 'icon' => 'heroicon-o-user-group'],
                ['label' => 'Submissions', 'route' => 'organizer.submissions.index', 'icon' => 'heroicon-o-document-text'],
            ];
        } elseif ($role === 'judge') {
            $navItems = [
                ['label' => 'Dashboard', 'route' => 'judge.dashboard', 'icon' => 'heroicon-o-home'],
            ];
        } elseif ($role === 'super_admin') {
            $navItems = [
                ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'heroicon-o-home'],
                ['label' => 'My Hackathons', 'route' => 'organizer.hackathons.index', 'icon' => 'heroicon-o-calendar-days'],
                ['label' => 'Teams', 'route' => 'organizer.teams.index', 'icon' => 'heroicon-o-user-group'],
                ['label' => 'Submissions', 'route' => 'organizer.submissions.index', 'icon' => 'heroicon-o-document-text'],
                ['label' => 'Users', 'route' => 'admin.users.index', 'icon' => 'heroicon-o-users'],
                ['label' => 'All Hackathons', 'route' => 'admin.hackathons.index', 'icon' => 'heroicon-o-globe-alt'],
                ['label' => 'Settings', 'route' => 'admin.settings.index', 'icon' => 'heroicon-o-cog-6-tooth'],
            ];
        }
    @endphp

    <div style="font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; padding: 8px 10px 4px; margin: 0;">Menu</div>
    <nav style="display: flex; flex-direction: column; gap: 4px;">
        @foreach ($navItems as $item)
            @php
                $href = '#';
                if (Route::has($item['route'])) {
                    try {
                        $href = route($item['route'], request()->route()->parameters());
                    } catch (\Illuminate\Routing\Exceptions\UrlGenerationException $e) {
                        $href = '#';
                    }
                }
                $isActive = Route::has($item['route']) ? request()->routeIs($item['route'].'*') : false;
            @endphp
            <a href="{{ $href }}" style="display: flex; align-items: center; gap: 8px; height: 34px; padding: 0 10px; border-radius: var(--radius-md); font-size: 15px; line-height: 1.5; font-weight: {{ $isActive ? '600' : '500' }}; color: {{ $isActive ? 'var(--accent)' : 'var(--text-secondary)' }}; background: {{ $isActive ? 'var(--accent-light)' : 'transparent' }}; text-decoration: none;" onmouseover="if(!{{ $isActive ? 'true' : 'false' }}) { this.style.background='var(--surface-alt)'; this.style.color='var(--text-primary)'; }" onmouseout="if(!{{ $isActive ? 'true' : 'false' }}) { this.style.background='transparent'; this.style.color='var(--text-secondary)'; }">
                @svg($item['icon'], 'w-[18px] h-[18px]')
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    <div style="margin-top: auto; padding-top: 16px; border-top: 1px solid var(--border); display: flex; justify-content: center;">
        @php
            $badgeColors = [
                'super_admin' => ['bg' => 'var(--accent-light)', 'color' => 'var(--accent)'],
                'organizer' => ['bg' => '#f3e8ff', 'color' => '#7c3aed'],
                'participant' => ['bg' => 'var(--surface-alt)', 'color' => 'var(--text-secondary)'],
                'judge' => ['bg' => 'var(--warning-light)', 'color' => 'var(--warning)'],
                'mentor' => ['bg' => '#f0fdfa', 'color' => '#0f766e'],
            ];
            $style = $badgeColors[$role] ?? $badgeColors['participant'];
        @endphp
        <span style="font-size: 11px; border-radius: 99px; padding: 4px 12px; font-weight: 600; background: {{ $style['bg'] }}; color: {{ $style['color'] }}; text-transform: capitalize;">
            {{ str_replace('_', ' ', $role) }}
        </span>
    </div>
</aside>
