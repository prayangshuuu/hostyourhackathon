<aside class="sidebar" style="{{ session('impersonating_from') ? 'top: calc(var(--height-nav) + 32px);' : '' }}">
    @if($isSingleMode)
        @if($singleHackathon)
            <div style="background: var(--accent-light); border-radius: var(--radius-md); padding: 12px; margin-bottom: 16px;">
                <div style="font-size: 13px; font-weight: 600; color: var(--accent); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.3;">
                    {{ $singleHackathon->title }}
                </div>
                <div style="margin-top: 8px;">
                    <x-badge variant="{{ $singleHackathon->status->value === 'ongoing' ? 'success' : 'neutral' }}" style="font-size: 10px; height: 18px;">
                        {{ ucfirst($singleHackathon->status->value) }}
                    </x-badge>
                </div>
            </div>
        @else
            <div style="background: var(--surface-alt); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 10px 12px; margin-bottom: 16px; text-align: center;">
                <span style="font-size: 12px; color: var(--text-muted); font-weight: 500;">No active hackathon</span>
            </div>
        @endif

        {{-- PARTICIPANT NAV --}}
        @if(auth()->user()->hasRole('participant'))
            <div class="sidebar-section-label">General</div>
            <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <x-heroicon-o-home />
                <span>Dashboard</span>
            </a>
            <a href="{{ route('single.segments.index') }}" class="sidebar-item {{ request()->routeIs('single.segments.*') ? 'active' : '' }}">
                <x-heroicon-o-puzzle-piece />
                <span>Browse Segments</span>
            </a>
            <a href="{{ route('single.teams.my') }}" class="sidebar-item {{ request()->routeIs('single.teams.*') ? 'active' : '' }}">
                <x-heroicon-o-users />
                <span>My Team</span>
            </a>
            <a href="{{ route('single.submissions.my') }}" class="sidebar-item {{ request()->routeIs('single.submissions.*') ? 'active' : '' }}">
                <x-heroicon-o-document-text />
                <span>My Submission</span>
            </a>
            @if($singleHackathon)
                <a href="{{ route('participant.announcements.index', $singleHackathon) }}" class="sidebar-item {{ request()->routeIs('participant.announcements.*') ? 'active' : '' }}">
                    <x-heroicon-o-megaphone />
                    <span>Announcements</span>
                </a>
            @endif
            <a href="{{ route('single.results') }}" class="sidebar-item {{ request()->routeIs('single.results') ? 'active' : '' }}">
                <x-heroicon-o-trophy />
                <span>Results</span>
            </a>
        @endif

        {{-- ORGANIZER NAV --}}
        @if(auth()->user()->hasRole(['organizer', 'super_admin']))
            @if($singleHackathon)
                <div class="sidebar-section-label">Management</div>
                <a href="{{ route('organizer.hackathons.show', $singleHackathon) }}" class="sidebar-item {{ request()->routeIs('organizer.hackathons.show') || request()->routeIs('organizer.segments.*') ? 'active' : '' }}">
                    <x-heroicon-o-puzzle-piece />
                    <span>Segments</span>
                </a>
                <a href="{{ route('organizer.teams.index', ['hackathon_id' => $singleHackathon->id]) }}" class="sidebar-item {{ request()->routeIs('organizer.teams.*') ? 'active' : '' }}">
                    <x-heroicon-o-users />
                    <span>Teams</span>
                </a>
                <a href="{{ route('organizer.submissions.index', ['hackathon_id' => $singleHackathon->id]) }}" class="sidebar-item {{ request()->routeIs('organizer.submissions.*') ? 'active' : '' }}">
                    <x-heroicon-o-document-text />
                    <span>Submissions</span>
                </a>
                <a href="{{ route('organizer.judges.index', $singleHackathon) }}" class="sidebar-item {{ request()->routeIs('organizer.judges.*') ? 'active' : '' }}">
                    <x-heroicon-o-star />
                    <span>Judges</span>
                </a>
                <a href="{{ route('organizer.announcements.index', $singleHackathon) }}" class="sidebar-item {{ request()->routeIs('organizer.announcements.*') ? 'active' : '' }}">
                    <x-heroicon-o-megaphone />
                    <span>Announcements</span>
                </a>
                <a href="{{ route('organizer.criteria.index', $singleHackathon) }}" class="sidebar-item {{ request()->routeIs('organizer.criteria.*') ? 'active' : '' }}">
                    <x-heroicon-o-chart-bar />
                    <span>Scoring</span>
                </a>
            @endif
        @endif

        {{-- JUDGE NAV --}}
        @if(auth()->user()->hasRole('judge'))
            <div class="sidebar-section-label">Judging</div>
            <a href="{{ route('judge.dashboard') }}" class="sidebar-item {{ request()->routeIs('judge.dashboard') ? 'active' : '' }}">
                <x-heroicon-o-home />
                <span>Dashboard</span>
            </a>
            <a href="{{ route('judge.dashboard') }}" class="sidebar-item">
                <x-heroicon-o-document-text />
                <span>Submissions</span>
            </a>
        @endif

    @else
        {{-- EXISTING MULTI-MODE SIDEBAR --}}
        <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <x-heroicon-o-home />
            <span>Dashboard</span>
        </a>

        @if(auth()->user()->hasRole('participant'))
            <div class="sidebar-section-label">Participation</div>
            <a href="{{ route('hackathons.index') }}" class="sidebar-item {{ request()->routeIs('hackathons.index') || request()->routeIs('hackathons.show') ? 'active' : '' }}">
                <x-heroicon-o-globe-alt />
                <span>Browse Hackathons</span>
            </a>
            <a href="{{ route('teams.index') }}" class="sidebar-item {{ request()->routeIs('teams.*') ? 'active' : '' }}">
                <x-heroicon-o-users />
                <span>My Teams</span>
            </a>
        @endif

        @if(auth()->user()->hasRole(['organizer', 'super_admin']))
            <div class="sidebar-section-label">Organizer</div>
            <a href="{{ route('organizer.hackathons.index') }}" class="sidebar-item {{ request()->routeIs('organizer.hackathons.*') ? 'active' : '' }}">
                <x-heroicon-o-calendar />
                <span>Manage Hackathons</span>
            </a>
            <a href="{{ route('organizer.teams.index') }}" class="sidebar-item {{ request()->routeIs('organizer.teams.*') ? 'active' : '' }}">
                <x-heroicon-o-users />
                <span>All Teams</span>
            </a>
            <a href="{{ route('organizer.submissions.index') }}" class="sidebar-item {{ request()->routeIs('organizer.submissions.*') ? 'active' : '' }}">
                <x-heroicon-o-document-text />
                <span>All Submissions</span>
            </a>
        @endif

        @if(auth()->user()->hasRole('judge'))
            <div class="sidebar-section-label">Judge</div>
            <a href="{{ route('judge.dashboard') }}" class="sidebar-item {{ request()->routeIs('judge.dashboard') ? 'active' : '' }}">
                <x-heroicon-o-scale />
                <span>Judging Panel</span>
            </a>
        @endif
    @endif

    {{-- ADMIN NAV --}}
    @if(auth()->user()->hasRole('super_admin'))
        <div class="sidebar-section-label">Platform Admin</div>
        <a href="{{ route('admin.dashboard') }}" class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <x-heroicon-o-chart-pie />
            <span>System Stats</span>
        </a>
        <a href="{{ route('admin.users.index') }}" class="sidebar-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <x-heroicon-o-user-group />
            <span>Users</span>
        </a>
        <a href="{{ route('admin.settings.index') }}" class="sidebar-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <x-heroicon-o-cog-6-tooth />
            <span>Settings</span>
        </a>
    @endif

    <div style="margin-top: auto; padding-top: 12px; border-top: 1px solid var(--border-subtle);">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-item" style="width: 100%;">
                <x-heroicon-o-arrow-left-on-rectangle />
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
