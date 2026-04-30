@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="page-header">
        <h1 class="text-page-title">Admin Dashboard</h1>
    </div>

    {{-- Stat Cards --}}
    <div class="stat-grid-4">
        <x-card class="stat-card">
            <div class="stat-icon-wrap">@svg('heroicon-o-users', 'w-5 h-5')</div>
            <div class="stat-value">{{ $totalUsers ?? 0 }}</div>
            <div class="stat-label">Total Users</div>
        </x-card>
        <x-card class="stat-card">
            <div class="stat-icon-wrap">@svg('heroicon-o-calendar-days', 'w-5 h-5')</div>
            <div class="stat-value">{{ $totalHackathons ?? 0 }}</div>
            <div class="stat-label">Total Hackathons</div>
        </x-card>
        <x-card class="stat-card">
            <div class="stat-icon-wrap">@svg('heroicon-o-document-text', 'w-5 h-5')</div>
            <div class="stat-value">{{ $totalSubmissions ?? 0 }}</div>
            <div class="stat-label">Total Submissions</div>
        </x-card>
        <x-card class="stat-card">
            <div class="stat-icon-wrap">@svg('heroicon-o-user-group', 'w-5 h-5')</div>
            <div class="stat-value">{{ $totalTeams ?? 0 }}</div>
            <div class="stat-label">Total Teams</div>
        </x-card>
    </div>

    {{-- 6/6 Layout --}}
    <div class="content-grid-6-6">
        {{-- Left: Hackathons by Status --}}
        <div>
            <x-card title="Hackathons by Status">
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    @php
                        $statuses = ['draft', 'published', 'ongoing', 'ended', 'archived'];
                        $maxStatusCount = isset($maxStatusCount) && $maxStatusCount > 0 ? $maxStatusCount : max(1, collect($statusCounts ?? [])->max() ?? 1);
                    @endphp
                    @foreach ($statuses as $status)
                        @php $count = $statusCounts[$status] ?? 0; @endphp
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                <span style="font-size: 13px; font-weight: 500; color: var(--text-primary); text-transform: capitalize;">{{ $status }}</span>
                                <span style="font-size: 13px; color: var(--text-muted);">{{ $count }}</span>
                            </div>
                            <div style="height: 6px; background: var(--surface-alt); border-radius: 99px; width: 100%; overflow: hidden;">
                                <div style="height: 6px; background: var(--accent); border-radius: 99px; width: {{ ($count / $maxStatusCount) * 100 }}%; transition: width 300ms ease;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-card>
        </div>

        {{-- Right: Recent Registrations --}}
        <div>
            <x-card title="Recent Registrations">
                <div style="display: flex; flex-direction: column;">
                    @forelse ($recentUsers ?? [] as $user)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid var(--border-subtle); {{ $loop->last ? 'border-bottom: none; padding-bottom: 0;' : '' }}">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--surface-alt); display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 600; color: var(--text-secondary);">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight: 500; color: var(--text-primary); font-size: 13px;">{{ $user->name }}</div>
                                    <div style="font-size: 12px; color: var(--text-muted);">{{ $user->email }}</div>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                @php
                                    $role = $user->roles->first()?->name ?? 'participant';
                                    $variant = match($role) {
                                        'super_admin' => 'danger',
                                        'organizer' => 'indigo',
                                        'judge' => 'amber',
                                        default => 'neutral',
                                    };
                                @endphp
                                <x-badge :variant="$variant">{{ ucfirst($role) }}</x-badge>
                                <span style="font-size: 12px; color: var(--text-muted); width: 60px; text-align: right;">
                                    {{ $user->created_at->format('M d') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <x-empty-state icon="heroicon-o-users" title="No recent users" description="Newly registered accounts will appear here." />
                    @endforelse
                </div>
            </x-card>
        </div>
    </div>
@endsection
