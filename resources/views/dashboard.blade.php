@extends('layouts.participant')

@section('title', 'Dashboard')
@section('meta_description', 'Your hackathon dashboard')

@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <h1 class="text-page-title">Dashboard</h1>
        <p style="font-size:var(--font-size-sm); color:var(--color-text-secondary); margin-top:4px;">Welcome back, {{ $user->name }}</p>
    </div>

    {{-- Row 1: Stat Cards --}}
    <div class="stat-grid" style="margin-bottom:32px;">
        <div class="stat-card">
            <div class="stat-label">Active Hackathons</div>
            <div class="stat-value">{{ $activeHackathons }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">My Teams</div>
            <div class="stat-value">{{ $teamCount }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Submissions</div>
            <div class="stat-value">{{ $submittedCount }}</div>
        </div>
    </div>

    {{-- Row 2: 8/4 Layout --}}
    <div class="content-grid-8-4" style="margin-bottom:32px;">
        {{-- Left: My Hackathons --}}
        <div>
            <h2 class="text-card-title" style="margin-bottom:16px;">My Hackathons</h2>
            <div class="ds-table-wrapper">
                <table class="ds-table" id="hackathon-table">
                    <thead>
                        <tr>
                            <th>Hackathon</th>
                            <th>Status</th>
                            <th>Deadline</th>
                            <th style="width:70px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($teams as $team)
                            @php
                                $h = $team->hackathon;
                                $statusClass = match($h->status->value) {
                                    'draft' => 'badge-partial',
                                    'published' => 'badge-scored',
                                    'ongoing' => 'badge-pending',
                                    'ended' => 'badge-partial',
                                    'archived' => 'badge-partial',
                                    default => 'badge-partial',
                                };
                                $nextDeadline = null;
                                if ($h->submission_closes_at && $h->submission_closes_at->isFuture()) {
                                    $nextDeadline = $h->submission_closes_at;
                                } elseif ($h->registration_closes_at && $h->registration_closes_at->isFuture()) {
                                    $nextDeadline = $h->registration_closes_at;
                                }
                            @endphp
                            <tr>
                                <td style="font-weight:var(--font-weight-medium);">{{ $h->title }}</td>
                                <td><span class="badge {{ $statusClass }}">{{ ucfirst($h->status->value) }}</span></td>
                                <td>
                                    @if ($nextDeadline)
                                        <span class="deadline-chip">
                                            <svg width="10" height="10" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.5"/><path d="M8 5v3l2 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                            {{ $nextDeadline->diffForHumans() }}
                                        </span>
                                    @else
                                        <span style="color:var(--color-text-muted); font-size:var(--font-size-xs);">—</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('hackathons.show', $h) }}" class="btn btn-secondary btn-sm">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="ds-table-empty">You haven't joined any hackathons yet.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Right: Upcoming Deadlines --}}
        <div>
            <h2 class="text-card-title" style="margin-bottom:16px;">Upcoming Deadlines</h2>
            <div class="card" style="padding:20px;">
                @if ($deadlines->count())
                    <div class="timeline">
                        @foreach ($deadlines as $deadline)
                            <div class="timeline-item {{ $deadline['past'] ? 'timeline-item-past' : '' }}">
                                <div class="timeline-dot {{ $deadline['past'] ? 'timeline-dot-past' : '' }}"></div>
                                <div class="timeline-label">{{ $deadline['label'] }}</div>
                                <div class="timeline-date">{{ $deadline['date']->format('M d, Y · h:i A') }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="color:var(--color-text-muted); font-size:var(--font-size-sm);">No upcoming deadlines.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Row 3: Recent Announcements --}}
    <div style="margin-bottom:32px;">
        <h2 class="text-card-title" style="margin-bottom:16px;">Recent Announcements</h2>
        @if ($announcements->count())
            <div class="announcement-row">
                @foreach ($announcements as $announcement)
                    @php
                        $visClass = match($announcement->visibility->value) {
                            'all' => 'badge-visibility-all',
                            'registered' => 'badge-visibility-registered',
                            'segment' => 'badge-visibility-segment',
                        };
                    @endphp
                    <div class="announcement-card">
                        <div class="announcement-card-header">
                            <div class="announcement-card-title">
                                {{ $announcement->title }}
                                <span class="badge {{ $visClass }}" style="font-size:var(--font-size-xs);">{{ ucfirst($announcement->visibility->value) }}</span>
                            </div>
                            <span class="announcement-card-date">
                                {{ $announcement->published_at->format('M d') }}
                            </span>
                        </div>
                        <div class="announcement-card-body">
                            {{ str()->limit(strip_tags($announcement->body), 160) }}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="card" style="padding:32px; text-align:center;">
                <p style="color:var(--color-text-muted); font-size:var(--font-size-sm);">No announcements yet.</p>
            </div>
        @endif
    </div>
@endsection
