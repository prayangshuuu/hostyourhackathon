@extends('layouts.app')

@section('title', $team->name)

@section('content')
    <x-page-header 
        :title="$team->name" 
        :description="$team->hackathon->title . ($team->segment ? ' · ' . $team->segment->name : '')"
        :breadcrumbs="['My Teams' => route('teams.index'), $team->name => null]"
    >
        <x-slot:actions>
            @if ($isLeader && !$teamManagementLocked)
                <x-button @click="renameMode = !renameMode" x-data="{ renameMode: false }" variant="secondary" icon="pencil-square">Rename</x-button>
            @endif
        </x-slot:actions>
    </x-page-header>

    @if ($teamManagementLocked)
        <div class="alert alert-info">
            <x-heroicon-o-lock-closed class="alert-icon" />
            <div>Team management is locked because the hackathon has ended.</div>
        </div>
    @endif

    <div class="grid-8-4">
        <div class="stack">
            <x-card title="Team Members" icon="users" noPadding>
                <x-table>
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Role</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($team->members->sortByDesc(fn ($m) => $m->role->value === 'leader') as $member)
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
                                <td class="td-actions">
                                    @if (!$teamManagementLocked)
                                        @if ($isLeader && $member->role->value !== 'leader')
                                            <form method="POST" action="{{ route('teams.members.remove', [$team, $member->user]) }}" onsubmit="return confirm('Remove member?')">
                                                @csrf <x-button type="submit" size="sm" variant="ghost" style="color: var(--danger);"><x-heroicon-o-user-minus style="width: 16px; height: 16px;" /></x-button>
                                            </form>
                                        @elseif ($member->user_id === Auth::id() && $member->role->value !== 'leader')
                                            <x-button @click="showLeaveModal = true" size="sm" variant="ghost" style="color: var(--danger);">Leave</x-button>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-table>
            </x-card>

            @if ($isMember && !$teamManagementLocked)
                <x-card title="Danger Zone" icon="exclamation-triangle" style="border-color: var(--danger-border);">
                    @if ($isLeader)
                        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px;">Disbanding will delete the team and all associated submission drafts.</p>
                        <x-button @click="showDisbandModal = true" variant="danger">Disband Team</x-button>
                    @else
                        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px;">You will no longer have access to this team's submission drafts.</p>
                        <x-button @click="showLeaveModal = true" variant="danger">Leave Team</x-button>
                    @endif
                </x-card>
            @endif
        </div>

        <div class="stack">
            @if (!$teamManagementLocked)
                <x-card title="Invite Link" icon="link">
                    <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 12px;">Teammates can join using this link.</p>
                    <div style="display: flex; gap: 8px;">
                        <input type="text" value="{{ route('teams.join', $team->invite_code) }}" readonly class="input" style="font-size: 12px;">
                        <x-button id="copy-btn" onclick="copyInvite()" variant="secondary" size="sm">Copy</x-button>
                    </div>
                </x-card>
            @endif

            <x-card title="Team Info" icon="information-circle">
                <div class="stack" style="gap: 12px;">
                    <div class="split">
                        <span style="font-size: 13px; color: var(--text-secondary);">Members</span>
                        <span style="font-size: 13px; font-weight: 600;">{{ $team->members_count }} / {{ $team->hackathon->max_team_size }}</span>
                    </div>
                    <div class="split">
                        <span style="font-size: 13px; color: var(--text-secondary);">Created</span>
                        <span style="font-size: 13px; font-weight: 600;">{{ $team->created_at->format('M j, Y') }}</span>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    {{-- Modals --}}
    <div x-data="{ showLeaveModal: false, showDisbandModal: false }">
        <template x-if="showLeaveModal">
            <div class="modal-backdrop" @click="showLeaveModal = false">
                <div class="modal" @click.stop>
                    <div class="modal-header">
                        <h3 class="modal-title">Leave Team</h3>
                    </div>
                    <div class="modal-body">Are you sure you want to leave <strong>{{ $team->name }}</strong>?</div>
                    <div class="modal-footer">
                        <x-button @click="showLeaveModal = false" variant="secondary">Cancel</x-button>
                        <form method="POST" action="{{ route('teams.members.remove', [$team, Auth::user()]) }}">
                            @csrf <x-button type="submit" variant="danger">Leave</x-button>
                        </form>
                    </div>
                </div>
            </div>
        </template>

        <template x-if="showDisbandModal">
            <div class="modal-backdrop" @click="showDisbandModal = false">
                <div class="modal" @click.stop>
                    <div class="modal-header">
                        <h3 class="modal-title">Disband Team</h3>
                    </div>
                    <div class="modal-body">This will permanently delete the team. Are you sure?</div>
                    <div class="modal-footer">
                        <x-button @click="showDisbandModal = false" variant="secondary">Cancel</x-button>
                        <form method="POST" action="{{ route('teams.destroy', $team) }}">
                            @csrf @method('DELETE')
                            <x-button type="submit" variant="danger">Disband</x-button>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <script>
        function copyInvite() {
            const link = "{{ route('teams.join', $team->invite_code) }}";
            navigator.clipboard.writeText(link);
            const btn = document.getElementById('copy-btn');
            btn.textContent = 'Copied!';
            setTimeout(() => btn.textContent = 'Copy', 2000);
        }
    </script>
@endsection
