@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="page-header">
        <h1 class="text-page-title">Admin Dashboard</h1>
    </div>

    {{-- Stat Cards --}}
    <div class="stat-grid-4">
        <div class="stat-card">
            <div class="stat-label">Total Users</div>
            <div class="stat-value">{{ $totalUsers }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Hackathons</div>
            <div class="stat-value">{{ $totalHackathons }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Submissions</div>
            <div class="stat-value">{{ $totalSubmissions }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Teams</div>
            <div class="stat-value">{{ $totalTeams }}</div>
        </div>
    </div>

    {{-- 6/6 Layout --}}
    <div class="content-grid-6-6">
        {{-- Left: Hackathons by Status --}}
        <div>
            <h2 class="text-card-title" style="margin-bottom:16px;">Hackathons by Status</h2>
            <div class="card" style="padding:24px;">
                @php
                    $statuses = ['draft', 'published', 'ongoing', 'ended', 'archived'];
                @endphp
                @foreach ($statuses as $status)
                    @php $count = $statusCounts[$status] ?? 0; @endphp
                    <div class="bar-chart-row">
                        <div class="bar-chart-label">{{ $status }}</div>
                        <div class="bar-chart-track">
                            <div class="bar-chart-fill" style="width:{{ $maxStatusCount > 0 ? ($count / $maxStatusCount * 100) : 0 }}%;"></div>
                        </div>
                        <div class="bar-chart-count">{{ $count }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Right: Recent Registrations --}}
        <div>
            <h2 class="text-card-title" style="margin-bottom:16px;">Recent Registrations</h2>
            <div class="card" style="padding:16px 24px;">
                @foreach ($recentUsers as $user)
                    <div class="user-list-item">
                        <div class="user-list-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                        <div class="user-list-info">
                            <div class="user-list-name">{{ $user->name }}</div>
                            <div class="user-list-email">{{ $user->email }}</div>
                        </div>
                        <div class="user-list-meta">
                            @php $role = $user->roles->first()?->name ?? 'participant'; @endphp
                            <span class="badge badge-role-{{ $role }}">{{ $role }}</span>
                            <span class="user-list-date">{{ $user->created_at->format('M d') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
