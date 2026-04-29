@extends('layouts.participant')

@section('title', $team->name)

@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <span class="separator">/</span>
            <a href="{{ route('teams.index') }}">Teams</a>
            <span class="separator">/</span>
            <span>{{ $team->hackathon->title }}</span>
            <span class="separator">/</span>
            <span>{{ Str::limit($team->name, 30) }}</span>
        </div>
        <div class="page-header-row">
            <div>
                <h1 class="text-page-title">{{ $team->name }}</h1>
                <p class="page-header-description">{{ $team->hackathon->title }}{{ $team->segment ? ' · ' . $team->segment->name : '' }}</p>
            </div>
            @if ($isLeader)
                <a href="#" onclick="document.getElementById('rename-section').style.display = document.getElementById('rename-section').style.display === 'none' ? 'block' : 'none'; return false;"
                   class="btn btn-secondary btn-sm">Rename</a>
            @endif
        </div>
    </div>

    {{-- Inline rename form (hidden by default) --}}
    @if ($isLeader)
        <div id="rename-section" style="display:none; margin-bottom:24px;">
            <form method="POST" action="{{ route('teams.update', $team) }}" style="display:flex; gap:10px; max-width:480px;">
                @csrf @method('PUT')
                <label for="rename-input" class="sr-only">Team name</label>
                <input type="text" name="name" id="rename-input" class="form-input" value="{{ $team->name }}" required>
                <button type="submit" class="btn btn-primary btn-sm">Save</button>
            </form>
        </div>
    @endif

    {{-- 2-Column Layout: 8/4 split --}}
    <div class="content-grid-8-4">

        {{-- Left: Member Table --}}
        <div>
            <div class="ds-table-wrapper">
                <table class="ds-table" id="members-table">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th style="text-align:right; width:80px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($team->members->sortByDesc(fn ($m) => $m->role->value === 'leader') as $member)
                            <tr>
                                <td>
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <div class="sidebar-user-avatar" aria-hidden="true" style="width:28px; height:28px; font-size:var(--font-size-xs);">
                                            {{ strtoupper(substr($member->user->name, 0, 1)) }}
                                        </div>
                                        <span style="font-weight:var(--font-weight-medium);">{{ $member->user->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $member->role->value }}">
                                        {{ ucfirst($member->role->value) }}
                                    </span>
                                </td>
                                <td style="color:var(--color-text-secondary);">
                                    {{ $member->joined_at->format('M d, Y') }}
                                </td>
                                <td>
                                    <div class="inline-actions" style="justify-content:flex-end;">
                                        @if ($isLeader && $member->role->value !== 'leader')
                                            {{-- Leader can kick members --}}
                                            <form method="POST" action="{{ route('teams.members.destroy', [$team, $member]) }}"
                                                  onsubmit="return confirm('Remove {{ $member->user->name }} from the team?')" style="display:inline;">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn-icon" aria-label="Remove {{ $member->user->name }}" title="Remove"
                                                        style="color:var(--color-danger);">
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M4 8h8" stroke="currentColor" stroke-width="1.33" stroke-linecap="round"/></svg>
                                                </button>
                                            </form>
                                        @elseif ($member->user_id === Auth::id() && $member->role->value !== 'leader')
                                            {{-- Non-leader member can leave --}}
                                            <button type="button" class="btn-icon" aria-label="Leave team" title="Leave"
                                                    onclick="document.getElementById('modal-leave').classList.add('is-open')"
                                                    style="color:var(--color-danger);">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M6 14H3.333A.667.667 0 0 1 2.667 13.333V2.667A.667.667 0 0 1 3.333 2H6m4.667 9.333L14 8m0 0-3.333-3.333M14 8H6" stroke="currentColor" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Danger Zone --}}
            @if ($isMember)
                <div class="danger-zone">
                    <div class="danger-zone-title">Danger Zone</div>
                    @if ($isLeader)
                        <p class="danger-zone-description">Disbanding will permanently remove all members and delete the team.</p>
                        <button type="button" class="btn btn-danger btn-sm" id="btn-disband"
                                onclick="document.getElementById('modal-disband').classList.add('is-open')">
                            Disband Team
                        </button>
                    @else
                        <p class="danger-zone-description">Leave this team. You can join another team for this hackathon afterward.</p>
                        <button type="button" class="btn btn-danger btn-sm" id="btn-leave"
                                onclick="document.getElementById('modal-leave').classList.add('is-open')">
                            Leave Team
                        </button>
                    @endif
                </div>
            @endif
        </div>

        {{-- Right: Sidebar Card --}}
        <div>
            {{-- Invite Link Card --}}
            <div class="card section-spacing">
                <div class="card-header">
                    <h3 class="text-card-title">Invite Link</h3>
                </div>
                <p style="font-size:var(--font-size-sm); color:var(--color-text-secondary); margin-bottom:12px;">
                    Share this link to invite teammates.
                </p>
                <div style="display:flex; gap:6px;">
                    <label for="invite-link" class="sr-only">Invite link</label>
                    <input type="text" id="invite-link" class="form-input" readonly
                           value="{{ route('teams.join', $team->invite_code) }}"
                           style="font-size:var(--font-size-xs);">
                    <div class="tooltip-trigger">
                        <button type="button" class="btn-icon" id="btn-copy-invite" aria-label="Copy invite link" title="Copy"
                                onclick="copyInviteLink()">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M10.667 5.333H12a.667.667 0 0 1 .667.667v7.333a.667.667 0 0 1-.667.667H5.333a.667.667 0 0 1-.666-.667V12M10.667 2H4a.667.667 0 0 0-.667.667V10c0 .368.299.667.667.667h6.667A.667.667 0 0 0 11.333 10V2.667A.667.667 0 0 0 10.667 2Z" stroke="currentColor" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <span class="tooltip-text" id="tooltip-copied">Copied!</span>
                    </div>
                </div>
            </div>

            {{-- Team Info --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="text-card-title">Team Info</h3>
                </div>
                <div style="display:flex; flex-direction:column; gap:12px;">
                    <div>
                        <div class="form-label">Hackathon</div>
                        <div style="font-size:var(--font-size-sm); color:var(--color-text-primary);">{{ $team->hackathon->title }}</div>
                    </div>
                    @if ($team->segment)
                        <div>
                            <div class="form-label">Segment</div>
                            <div style="font-size:var(--font-size-sm); color:var(--color-text-primary);">{{ $team->segment->name }}</div>
                        </div>
                    @endif
                    <div>
                        <div class="form-label">Members</div>
                        <div style="font-size:var(--font-size-sm); color:var(--color-text-primary);">{{ $team->members->count() }} / {{ $team->hackathon->max_team_size }}</div>
                    </div>
                    <div>
                        <div class="form-label">Created</div>
                        <div style="font-size:var(--font-size-sm); color:var(--color-text-primary);">{{ $team->created_at->format('M d, Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Leave Modal --}}
    @if ($isMember && !$isLeader)
        @php
            $selfMember = $team->members->firstWhere('user_id', Auth::id());
        @endphp
        <div class="modal-overlay" id="modal-leave" onclick="if(event.target===this) this.classList.remove('is-open')">
            <div class="modal-box">
                <h3 class="modal-title">Leave Team</h3>
                <p class="modal-body">Are you sure you want to leave <strong>{{ $team->name }}</strong>? You can join a different team for this hackathon afterward.</p>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('modal-leave').classList.remove('is-open')">Cancel</button>
                    <form method="POST" action="{{ route('teams.members.destroy', [$team, $selfMember]) }}" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Leave Team</button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Disband Modal --}}
    @if ($isLeader)
        <div class="modal-overlay" id="modal-disband" onclick="if(event.target===this) this.classList.remove('is-open')">
            <div class="modal-box">
                <h3 class="modal-title">Disband Team</h3>
                <p class="modal-body">This will remove all members and delete <strong>{{ $team->name }}</strong>. This action cannot be undone.</p>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('modal-disband').classList.remove('is-open')">Cancel</button>
                    <form method="POST" action="{{ route('teams.destroy', $team) }}" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Disband Team</button>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
    function copyInviteLink() {
        const input = document.getElementById('invite-link');
        const tooltip = document.getElementById('tooltip-copied');
        navigator.clipboard.writeText(input.value).then(function () {
            tooltip.classList.add('is-visible');
            setTimeout(function () {
                tooltip.classList.remove('is-visible');
            }, 1500);
        });
    }
</script>
@endpush
