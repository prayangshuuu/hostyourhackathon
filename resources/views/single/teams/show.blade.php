@extends('layouts.app')

@section('title', 'My Team')

@section('content')
    <x-page-header 
        title="My Team" 
        :description="$team->name"
        :breadcrumbs="['Dashboard' => route('dashboard'), 'My Team' => null]"
    />

    <div class="grid-8-4">
        <div class="stack">
            <x-card title="Team Members" icon="users" noPadding>
                <x-table>
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Role</th>
                            <th style="text-align: right;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($team->members as $member)
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div class="avatar avatar-sm avatar-default">{{ strtoupper(substr($member->user->name, 0, 1)) }}</div>
                                        <div style="font-weight: 600;">{{ $member->user->name }}</div>
                                    </div>
                                </td>
                                <td>
                                    <x-badge variant="{{ $member->role->value === 'leader' ? 'indigo' : 'neutral' }}">
                                        {{ ucfirst($member->role->value) }}
                                    </x-badge>
                                </td>
                                <td style="text-align: right;">
                                    <x-badge variant="success" dot>Active</x-badge>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-table>
            </x-card>

            <x-card title="Submission" icon="document-text">
                @if($team->submission)
                    <div class="split">
                        <div>
                            <h4 style="font-weight: 700; color: var(--text-primary);">{{ $team->submission->title }}</h4>
                            <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">{{ Str::limit($team->submission->description, 100) }}</p>
                        </div>
                        <x-button href="{{ route('submissions.show', $team->submission) }}" variant="secondary" size="sm">View Submission</x-button>
                    </div>
                @else
                    <div class="empty-state" style="padding: 24px;">
                        <p style="font-size: 14px; color: var(--text-muted);">No project submitted yet.</p>
                        @if($team->hackathon->isSubmissionOpen())
                            <div style="margin-top: 16px;">
                                <x-button href="{{ route('submissions.create', $team->hackathon) }}" variant="primary" size="sm">Start Submission</x-button>
                            </div>
                        @endif
                    </div>
                @endif
            </x-card>
        </div>

        <div class="stack">
            <x-card title="Invite Code" icon="link">
                <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 12px;">Share this code with your teammates to join.</p>
                <div style="display: flex; gap: 8px;">
                    <input type="text" value="{{ $team->invite_code }}" readonly class="input" style="font-weight: 700; text-align: center; letter-spacing: 2px;">
                    <x-button onclick="copyCode('{{ $team->invite_code }}')" variant="secondary" size="sm">Copy</x-button>
                </div>
            </x-card>

            <x-card title="Hackathon Details" icon="information-circle">
                <div class="stack" style="gap: 12px;">
                    <div class="split">
                        <span style="font-size: 13px; color: var(--text-secondary);">Track</span>
                        <span style="font-size: 13px; font-weight: 600; color: var(--accent);">{{ $team->segment->name }}</span>
                    </div>
                    <div class="split">
                        <span style="font-size: 13px; color: var(--text-secondary);">Registration</span>
                        <x-badge variant="success">Confirmed</x-badge>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    <script>
        function copyCode(code) {
            navigator.clipboard.writeText(code);
            alert('Code copied to clipboard!');
        }
    </script>
@endsection
