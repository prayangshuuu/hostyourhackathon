@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <x-page-header title="Dashboard" description="Welcome back, {{ $user->name }}" />

    @if (session('success'))
        <div style="margin-bottom: 24px;">
            <x-alert type="success" :message="session('success')" />
        </div>
    @endif
    @if (session('error'))
        <div style="margin-bottom: 24px;">
            <x-alert type="error" :message="session('error')" />
        </div>
    @endif

    {{-- ROW 1: Stats --}}
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 24px;">
        {{-- Card 1: Active Hackathons --}}
        <div class="card" style="padding: 20px;">
            <div style="font-size: 28px; font-weight: 600; color: var(--text-primary);">{{ $myTeams->count() }}</div>
            <div style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">Active Hackathons</div>
        </div>

        {{-- Card 2: My Team --}}
        <div class="card" style="padding: 20px; display: flex; flex-direction: column; justify-content: center;">
            @if ($myTeams->count() > 0)
                <div style="font-size: 16px; font-weight: 600; color: var(--accent);">{{ $myTeams->first()->name }}</div>
                @if($myTeams->first()->segment)
                    <div style="margin-top: 4px;">
                        <x-badge variant="secondary">{{ $myTeams->first()->segment->name }}</x-badge>
                    </div>
                @endif
                <div style="font-size: 13px; color: var(--text-muted); margin-top: 8px;">My Team</div>
            @else
                <div style="font-size: 16px; font-weight: 600; color: var(--text-primary);">No team yet</div>
                <a href="{{ route('hackathons.index') }}" style="font-size: 13px; color: var(--accent); margin-top: 4px; text-decoration: none; font-weight: 500;">Join or create a team</a>
            @endif
        </div>

        {{-- Card 3: Submission --}}
        <div class="card" style="padding: 20px; display: flex; flex-direction: column; justify-content: center; align-items: flex-start;">
            @php
                $submissionStatus = 'Not started';
                $statusVariant = 'secondary';
                if ($mySubmissions->count() > 0) {
                    $sub = $mySubmissions->first();
                    if ($sub->is_draft) {
                        $submissionStatus = 'Draft';
                        $statusVariant = 'warning';
                    } else {
                        $submissionStatus = 'Submitted';
                        $statusVariant = 'success';
                    }
                }
            @endphp
            <x-badge :variant="$statusVariant">{{ $submissionStatus }}</x-badge>
            <div style="font-size: 14px; color: var(--text-muted); margin-top: 8px;">Submission</div>
        </div>
    </div>

    {{-- ROW 2: 2 columns (8/4) --}}
    <div style="display: grid; grid-template-columns: 8fr 4fr; gap: 24px; margin-bottom: 24px;">
        {{-- Left: My Hackathons --}}
        <div class="card" style="padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="font-size: 16px; font-weight: 600; color: var(--text-primary);">My Hackathons</h2>
            </div>
            
            @if ($myTeams->isEmpty())
                <div style="text-align: center; padding: 40px 0;">
                    <p style="color: var(--text-secondary); margin-bottom: 12px;">You haven't joined any hackathons</p>
                    <a href="{{ route('hackathons.index') }}" style="color: var(--accent); font-weight: 500; text-decoration: none;">Browse Hackathons</a>
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid var(--border);">
                                <th style="text-align: left; padding: 12px; font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Hackathon</th>
                                <th style="text-align: left; padding: 12px; font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Status</th>
                                <th style="text-align: left; padding: 12px; font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">My Team</th>
                                <th style="text-align: left; padding: 12px; font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Submission</th>
                                <th style="text-align: left; padding: 12px; font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Deadline</th>
                                <th style="text-align: right; padding: 12px; font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($myTeams as $team)
                                @php
                                    $h = $team->hackathon;
                                    $sub = $mySubmissions->where('team_id', $team->id)->first();
                                @endphp
                                <tr style="border-bottom: 1px solid var(--border);">
                                    <td style="padding: 16px 12px;">
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            @if ($h->logo)
                                                <img src="{{ Storage::url($h->logo) }}" alt="" style="width: 24px; height: 24px; border-radius: var(--radius-sm); object-fit: cover;">
                                            @else
                                                <div style="width: 24px; height: 24px; border-radius: var(--radius-sm); background: var(--surface-alt); border: 1px solid var(--border);"></div>
                                            @endif
                                            <span style="font-weight: 500; color: var(--text-primary); font-size: 14px;">{{ $h->title }}</span>
                                        </div>
                                    </td>
                                    <td style="padding: 16px 12px;">
                                        <x-badge :variant="$h->status->value === 'ongoing' ? 'success' : 'secondary'">{{ ucfirst($h->status->value) }}</x-badge>
                                    </td>
                                    <td style="padding: 16px 12px; font-size: 14px; color: var(--text-secondary);">
                                        {{ $team->name }}
                                    </td>
                                    <td style="padding: 16px 12px; font-size: 14px; color: var(--text-secondary);">
                                        {{ $sub ? ($sub->is_draft ? 'Draft' : 'Submitted') : '—' }}
                                    </td>
                                    <td style="padding: 16px 12px;">
                                        @if ($h->submission_closes_at)
                                            <div style="font-size: 12px; color: var(--text-secondary);">
                                                {{ $h->submission_closes_at->format('M j, Y') }}
                                            </div>
                                        @else
                                            <span style="color: var(--text-muted);">—</span>
                                        @endif
                                    </td>
                                    <td style="padding: 16px 12px; text-align: right;">
                                        <a href="{{ route('hackathons.show', $h->slug) }}" style="color: var(--accent); font-size: 14px; font-weight: 500; text-decoration: none;">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Right: Upcoming Deadlines --}}
        <div class="card" style="padding: 24px;">
            <h2 style="font-size: 16px; font-weight: 600; color: var(--text-primary); margin-bottom: 20px;">Upcoming Deadlines</h2>
            
            @if ($deadlines->isEmpty())
                <p style="color: var(--text-muted); font-size: 14px; text-align: center; padding: 20px 0;">No upcoming deadlines</p>
            @else
                <div style="position: relative; display: flex; flex-direction: column; gap: 16px;">
                    <div style="position: absolute; left: 3.5px; top: 8px; bottom: 8px; width: 1px; background: var(--border);"></div>
                    @foreach ($deadlines as $deadline)
                        <div style="display: flex; align-items: flex-start; position: relative; z-index: 1;">
                            <div style="width: 8px; height: 8px; border-radius: 50%; background: {{ $deadline['past'] ? 'var(--border)' : 'var(--accent)' }}; margin-top: 6px; margin-right: 12px; flex-shrink: 0;"></div>
                            <div style="flex: 1;">
                                <div style="font-size: 13px; font-weight: 500; color: {{ $deadline['past'] ? 'var(--text-muted)' : 'var(--text-primary)' }};">{{ $deadline['label'] }}</div>
                                <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">{{ $deadline['hackathon'] }}</div>
                            </div>
                            <div style="font-size: 12px; color: {{ $deadline['past'] ? 'var(--text-muted)' : 'var(--text-secondary)' }}; margin-left: 12px; text-align: right; white-space: nowrap;">
                                {{ $deadline['date']->format('M j, g:i A') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ROW 3: Recent Announcements --}}
    <div class="card" style="padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="font-size: 16px; font-weight: 600; color: var(--text-primary);">Recent Announcements</h2>
            @if ($hackathons->count() > 0)
                <a href="{{ route('participant.announcements.index', $hackathons->first()->id) }}" style="color: var(--accent); font-size: 14px; font-weight: 500; text-decoration: none;">View all</a>
            @endif
        </div>
        
        @if ($announcements->isEmpty())
            <p style="color: var(--text-muted); font-size: 14px; text-align: center; padding: 20px 0;">No announcements yet</p>
        @else
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px;">
                @foreach ($announcements as $announcement)
                    <div style="background: var(--surface-alt); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 16px; display: flex; flex-direction: column;">
                        <div style="font-size: 14px; font-weight: 600; color: var(--text-primary); margin-bottom: 6px;">{{ $announcement->title }}</div>
                        <div style="font-size: 13px; color: var(--text-secondary); margin-bottom: 16px; flex: 1;">
                            {{ Str::limit(strip_tags($announcement->content), 100) }}
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: auto;">
                            <div style="font-size: 12px; color: var(--text-muted);">
                                {{ $announcement->published_at ? $announcement->published_at->format('M j, Y') : 'Draft' }}
                            </div>
                            <x-badge :variant="$announcement->visibility === 'public' ? 'success' : 'secondary'">
                                {{ ucfirst(str_replace('_', ' ', $announcement->visibility)) }}
                            </x-badge>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
