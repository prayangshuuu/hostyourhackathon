@extends('layouts.organizer')

@section('title', $hackathon->title)

@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <span class="separator">/</span>
            <a href="{{ route('organizer.hackathons.index') }}">Hackathons</a>
            <span class="separator">/</span>
            <span>{{ Str::limit($hackathon->title, 40) }}</span>
        </div>
        <div class="page-header-row">
            <div>
                <h1 class="text-page-title">{{ $hackathon->title }}</h1>
                <p class="page-header-description">{{ $hackathon->tagline }}</p>
            </div>
            <div style="display:flex; gap:8px;">
                @if ($nextStatus)
                    <form method="POST" action="{{ route('organizer.hackathons.status', $hackathon) }}" id="form-status-transition">
                        @csrf
                        <input type="hidden" name="status" value="{{ $nextStatus->value }}">
                        <button type="submit" class="btn btn-primary btn-sm"
                                onclick="return confirm('Transition status to {{ $nextStatus->value }}?')">
                            Move to {{ ucfirst($nextStatus->value) }}
                        </button>
                    </form>
                @endif
                <a href="{{ route('organizer.hackathons.edit', $hackathon) }}" class="btn btn-secondary btn-sm">Edit</a>
            </div>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:16px;" class="section-spacing">
        <div class="card" style="padding:16px;">
            <div style="font-size:var(--font-size-xs); color:var(--color-text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Status</div>
            <span class="badge badge-{{ $hackathon->status->value }}">{{ ucfirst($hackathon->status->value) }}</span>
        </div>
        <div class="card" style="padding:16px;">
            <div style="font-size:var(--font-size-xs); color:var(--color-text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Teams</div>
            <div class="text-card-title">{{ $hackathon->teams_count }}</div>
        </div>
        <div class="card" style="padding:16px;">
            <div style="font-size:var(--font-size-xs); color:var(--color-text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Segments</div>
            <div class="text-card-title">{{ $hackathon->segments_count }}</div>
        </div>
        <div class="card" style="padding:16px;">
            <div style="font-size:var(--font-size-xs); color:var(--color-text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Created</div>
            <div style="font-size:var(--font-size-sm); color:var(--color-text-primary);">{{ $hackathon->created_at->format('M d, Y') }}</div>
        </div>
    </div>

    {{-- Detail Card --}}
    <div class="card section-spacing">
        <div class="card-header flex-between">
            <h2 class="text-card-title">Details</h2>
            <div style="display:flex; align-items:center; gap:8px;">
                <span class="color-preview" style="background:{{ $hackathon->primary_color }};"></span>
                <span style="font-size:var(--font-size-sm); color:var(--color-text-secondary);">{{ $hackathon->primary_color }}</span>
            </div>
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px;">
            <div>
                <div class="form-label">Team Size</div>
                <div class="text-body">{{ $hackathon->min_team_size }} – {{ $hackathon->max_team_size }} members</div>
            </div>
            <div>
                <div class="form-label">Solo Participants</div>
                <div class="text-body">{{ $hackathon->allow_solo ? 'Allowed' : 'Not allowed' }}</div>
            </div>
            @if ($hackathon->registration_opens_at)
                <div>
                    <div class="form-label">Registration</div>
                    <div class="text-body">
                        {{ $hackathon->registration_opens_at->format('M d, Y H:i') }}
                        @if ($hackathon->registration_closes_at)
                            → {{ $hackathon->registration_closes_at->format('M d, Y H:i') }}
                        @endif
                    </div>
                </div>
            @endif
            @if ($hackathon->submission_opens_at)
                <div>
                    <div class="form-label">Submissions</div>
                    <div class="text-body">
                        {{ $hackathon->submission_opens_at->format('M d, Y H:i') }}
                        @if ($hackathon->submission_closes_at)
                            → {{ $hackathon->submission_closes_at->format('M d, Y H:i') }}
                        @endif
                    </div>
                </div>
            @endif
            @if ($hackathon->results_at)
                <div>
                    <div class="form-label">Results</div>
                    <div class="text-body">{{ $hackathon->results_at->format('M d, Y H:i') }}</div>
                </div>
            @endif
        </div>
        @if ($hackathon->description)
            <div style="margin-top:24px; border-top:1px solid var(--color-border-subtle); padding-top:16px;">
                <div class="form-label">Description</div>
                <div class="text-body" style="white-space:pre-wrap;">{{ $hackathon->description }}</div>
            </div>
        @endif
    </div>

    {{-- Segments Section --}}
    <div class="card section-spacing">
        <div class="card-header flex-between">
            <h2 class="text-card-title">Segments</h2>
        </div>

        {{-- Add segment form --}}
        <form method="POST" action="{{ route('organizer.hackathons.segments.store', $hackathon) }}"
              style="display:flex; gap:10px; margin-bottom:20px;" id="form-add-segment">
            @csrf
            <div style="flex:1;">
                <label for="segment_name" class="sr-only">Segment name</label>
                <input type="text" name="name" id="segment_name"
                       class="form-input @error('name') is-invalid @enderror"
                       placeholder="Segment name" required>
                @error('name') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div style="flex:1;">
                <label for="segment_description" class="sr-only">Segment description</label>
                <input type="text" name="description" id="segment_description"
                       class="form-input"
                       placeholder="Description (optional)">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Add</button>
        </form>

        {{-- Segment list --}}
        @if ($hackathon->segments->count())
            <div class="ds-table-wrapper">
                <table class="ds-table" id="segments-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th style="text-align:right; width:100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($hackathon->segments as $segment)
                            <tr>
                                <td style="font-weight:var(--font-weight-medium);">{{ $segment->name }}</td>
                                <td style="color:var(--color-text-secondary);">{{ $segment->description ?? '—' }}</td>
                                <td>
                                    <div class="inline-actions" style="justify-content:flex-end;">
                                        <form method="POST"
                                              action="{{ route('organizer.hackathons.segments.destroy', [$hackathon, $segment]) }}"
                                              onsubmit="return confirm('Delete this segment? Related records will be unlinked.')"
                                              style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-icon" aria-label="Delete segment {{ $segment->name }}" title="Delete"
                                                    style="color:var(--color-danger);">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M2 4h12M5.333 4V2.667a.667.667 0 0 1 .667-.667h4a.667.667 0 0 1 .667.667V4m2 0v9.333a.667.667 0 0 1-.667.667H4a.667.667 0 0 1-.667-.667V4h9.334Z" stroke="currentColor" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="ds-table-empty">
                <span>No segments yet. Add one above.</span>
            </div>
        @endif
    </div>

    {{-- Organizers Section --}}
    <div class="card section-spacing">
        <div class="card-header flex-between">
            <h2 class="text-card-title">Co-Organizers</h2>
        </div>

        {{-- Invite form --}}
        <form method="POST" action="{{ route('organizer.hackathons.organizers.invite', $hackathon) }}"
              style="display:flex; gap:10px; margin-bottom:20px;" id="form-invite-organizer">
            @csrf
            <div style="flex:1;">
                <label for="organizer_email" class="sr-only">Co-organizer email</label>
                <input type="email" name="email" id="organizer_email"
                       class="form-input @error('email') is-invalid @enderror"
                       placeholder="user@example.com" required>
                @error('email') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Invite</button>
        </form>

        {{-- Creator --}}
        <div style="margin-bottom:8px;">
            <div style="display:flex; align-items:center; gap:10px; padding:8px 0;">
                <div class="sidebar-user-avatar" aria-hidden="true" style="width:28px; height:28px; font-size:var(--font-size-xs);">
                    {{ strtoupper(substr($hackathon->creator->name, 0, 1)) }}
                </div>
                <div>
                    <span style="font-size:var(--font-size-sm); font-weight:var(--font-weight-medium); color:var(--color-text-primary);">{{ $hackathon->creator->name }}</span>
                    <span style="font-size:var(--font-size-xs); color:var(--color-text-muted); margin-left:6px;">Owner</span>
                </div>
            </div>
        </div>

        {{-- Co-organizers --}}
        @foreach ($hackathon->organizers as $organizer)
            <div style="display:flex; align-items:center; gap:10px; padding:8px 0; border-top:1px solid var(--color-border-subtle);">
                <div class="sidebar-user-avatar" aria-hidden="true" style="width:28px; height:28px; font-size:var(--font-size-xs);">
                    {{ strtoupper(substr($organizer->name, 0, 1)) }}
                </div>
                <div style="flex:1;">
                    <span style="font-size:var(--font-size-sm); font-weight:var(--font-weight-medium); color:var(--color-text-primary);">{{ $organizer->name }}</span>
                    <span style="font-size:var(--font-size-xs); color:var(--color-text-muted); margin-left:6px;">{{ $organizer->email }}</span>
                </div>
                <form method="POST" action="{{ route('organizer.hackathons.organizers.remove', [$hackathon, $organizer]) }}"
                      onsubmit="return confirm('Remove this co-organizer?')" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-icon" aria-label="Remove {{ $organizer->name }}" title="Remove"
                            style="color:var(--color-danger);">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M4 8h8" stroke="currentColor" stroke-width="1.33" stroke-linecap="round"/></svg>
                    </button>
                </form>
            </div>
        @endforeach

        @if ($hackathon->organizers->isEmpty())
            <p style="font-size:var(--font-size-sm); color:var(--color-text-muted); padding-top:8px; border-top:1px solid var(--color-border-subtle);">
                No co-organizers yet. Invite one by email above.
            </p>
        @endif
    </div>
@endsection
