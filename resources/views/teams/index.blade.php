@extends('layouts.app')

@section('title', 'My Teams')

@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <span class="separator">/</span>
            <span>My Teams</span>
        </div>
        <div class="page-header-row">
            <div>
                <h1 class="text-page-title">My Teams</h1>
                <p class="page-header-description">Teams you've created or joined.</p>
            </div>
        </div>
    </div>

    @if ($teams->count())
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(340px, 1fr)); gap:16px;">
            @foreach ($teams as $team)
                <a href="{{ route('teams.show', $team) }}" class="card card-clickable" style="text-decoration:none;">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                        <h3 class="text-card-title">{{ $team->name }}</h3>
                        <span class="badge badge-{{ $team->hackathon->status->value }}">{{ ucfirst($team->hackathon->status->value) }}</span>
                    </div>
                    <p style="font-size:var(--font-size-sm); color:var(--color-text-secondary); margin-bottom:12px;">
                        {{ $team->hackathon->title }}
                        @if ($team->segment)
                            · {{ $team->segment->name }}
                        @endif
                    </p>
                    <div style="display:flex; align-items:center; gap:16px; font-size:var(--font-size-xs); color:var(--color-text-muted);">
                        <span>{{ $team->members_count }} / {{ $team->hackathon->max_team_size }} members</span>
                        <span>Joined {{ $team->created_at->format('M d') }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="card">
            <div class="ds-table-empty">
                <div style="text-align:center;">
                    <p style="margin-bottom:8px;">You haven't joined any teams yet.</p>
                    <p style="font-size:var(--font-size-xs); color:var(--color-text-muted);">Ask an organizer for a hackathon link or use an invite code to join a team.</p>
                </div>
            </div>
        </div>
    @endif
@endsection
