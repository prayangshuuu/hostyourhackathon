<aside style="width: 240px; height: calc(100vh - 56px); position: fixed; top: 56px; left: 0; background: var(--surface); border-right: 1px solid var(--border); padding: 16px 12px; overflow-y: auto; display: flex; flex-direction: column;">
    
    @php
        $role = Auth::user()->roles->first()->name ?? 'participant';
        $navItems = [];

        if ($role === 'participant') {
            $navItems = [
                ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline>'],
                ['label' => 'Browse Hackathons', 'route' => 'hackathons.index', 'icon' => '<circle cx="12" cy="12" r="10"></circle><path d="M12 2v20"></path><path d="M2 12h20"></path>'],
                ['label' => 'My Team', 'route' => 'teams.index', 'icon' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>'],
                ['label' => 'My Submission', 'route' => 'submissions.index', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline>'],
                ['label' => 'Announcements', 'route' => 'participant.announcements.index', 'icon' => '<path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3zm-8.27 4a2 2 0 0 1-3.46 0"></path>'],
            ];
        } elseif ($role === 'organizer') {
            $navItems = [
                ['label' => 'Dashboard', 'route' => 'organizer.dashboard', 'icon' => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline>'],
                ['label' => 'My Hackathons', 'route' => 'organizer.hackathons.index', 'icon' => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line>'],
                ['label' => 'Announcements', 'route' => 'organizer.announcements.index', 'icon' => '<path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3zm-8.27 4a2 2 0 0 1-3.46 0"></path>'],
                ['label' => 'Judges', 'route' => 'organizer.judges.index', 'icon' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>'],
                ['label' => 'Scoring', 'route' => 'organizer.scoring.index', 'icon' => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>'],
            ];
        } elseif ($role === 'judge') {
            $navItems = [
                ['label' => 'Dashboard', 'route' => 'judge.dashboard', 'icon' => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline>'],
                ['label' => 'Assigned Submissions', 'route' => 'judge.submissions.index', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline>'],
                ['label' => 'Scores', 'route' => 'judge.scores.index', 'icon' => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>'],
            ];
        } elseif ($role === 'super_admin') {
            $navItems = [
                ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline>'],
                ['label' => 'Users', 'route' => 'admin.users.index', 'icon' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>'],
                ['label' => 'Hackathons', 'route' => 'admin.hackathons.index', 'icon' => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line>'],
                ['label' => 'Settings', 'route' => 'admin.settings', 'icon' => '<circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>'],
            ];
        }
    @endphp

    <div style="font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.06em; padding: 0 10px; margin: 16px 0 6px;">Menu</div>
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
            <a href="{{ $href }}" style="display: flex; align-items: center; gap: 10px; padding: 8px 10px; border-radius: var(--radius-md); font-size: 14px; font-weight: 500; color: {{ $isActive ? 'var(--accent)' : 'var(--text-secondary)' }}; background: {{ $isActive ? 'var(--accent-light)' : 'transparent' }}; text-decoration: none;" onmouseover="if(!{{ $isActive ? 'true' : 'false' }}) { this.style.background='var(--surface-alt)'; this.style.color='var(--text-primary)'; }" onmouseout="if(!{{ $isActive ? 'true' : 'false' }}) { this.style.background='transparent'; this.style.color='var(--text-secondary)'; }">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    {!! $item['icon'] !!}
                </svg>
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
