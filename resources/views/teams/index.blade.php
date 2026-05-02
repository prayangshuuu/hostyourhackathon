@extends('layouts.app')

@section('title', 'My Teams')

@section('content')
    <x-page-header 
        title="My Teams" 
        description="Teams you've created or joined across all hackathons."
        :breadcrumbs="['My Teams' => null]"
    />

    @if ($teams->count())
        <div class="grid-3">
            @foreach ($teams as $team)
                <div class="card" style="display: flex; flex-direction: column;">
                    <div class="card-body" style="flex: 1;">
                        <div class="split" style="margin-bottom: 12px;">
                            <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary);">{{ $team->name }}</h3>
                            <x-badge variant="{{ $team->hackathon->status->value === 'ongoing' ? 'success' : 'neutral' }}">
                                {{ ucfirst($team->hackathon->status->value) }}
                            </x-badge>
                        </div>
                        <div style="font-size: 13px; color: var(--text-secondary); margin-bottom: 16px;">
                            <div style="font-weight: 600; color: var(--text-primary);">{{ $team->hackathon->title }}</div>
                            @if ($team->segment)
                                <div style="color: var(--accent); margin-top: 2px;">{{ $team->segment->name }}</div>
                            @endif
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px; font-size: 12px; color: var(--text-muted);">
                            <div style="display: flex; align-items: center; gap: 4px;">
                                <x-heroicon-o-users style="width: 14px; height: 14px;" />
                                {{ $team->members_count }} members
                            </div>
                            <div style="display: flex; align-items: center; gap: 4px;">
                                <x-heroicon-o-calendar style="width: 14px; height: 14px;" />
                                {{ $team->created_at->format('M j') }}
                            </div>
                        </div>
                    </div>
                    <div class="card-footer" style="background: var(--surface-alt);">
                        <x-button href="{{ $isSingleMode ? route('single.teams.my') : route('teams.show', $team) }}" variant="secondary" size="sm" fullWidth>
                            Manage Team
                        </x-button>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <x-heroicon-o-users class="empty-state-icon" style="width: 48px; height: 48px;" />
            <h3 class="empty-state-title">No teams yet</h3>
            <p class="empty-state-description">You haven't joined any teams. Browse active hackathons to get started.</p>
            <div class="empty-state-action">
                <x-button href="{{ $isSingleMode ? route('single.segments.index') : route('hackathons.index') }}" variant="primary">Browse Hackathons</x-button>
            </div>
        </div>
    @endif
@endsection
