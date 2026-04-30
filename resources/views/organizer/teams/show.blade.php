@extends('layouts.app')

@section('title', $team->name)

@section('content')
    <div style="margin-bottom: 16px; font-size: 13px; color: var(--text-muted);">
        <a href="{{ route('organizer.teams.index') }}" style="color: inherit;">Teams</a>
        <span> / </span>
        <span>{{ $team->name }}</span>
    </div>

    <h1 class="text-page-title" style="margin-bottom: 8px;">{{ $team->name }}</h1>
    <p style="color: var(--text-secondary); margin-bottom: 24px;">{{ $team->hackathon->title }}</p>

    @if (session('success'))
        <div style="margin-bottom: 24px;"><x-alert type="success" :message="session('success')" /></div>
    @endif
    @if (session('error'))
        <div style="margin-bottom: 24px;"><x-alert type="error" :message="session('error')" /></div>
    @endif

    @if ($team->is_banned)
        <div class="card" style="padding: 16px; margin-bottom: 24px; border-color: var(--danger);">
            <strong>Banned</strong>
            <p style="margin: 8px 0 0; color: var(--text-secondary);">{{ $team->banned_reason ?? '—' }}</p>
        </div>
    @endif

    <div class="card" style="padding: 24px; margin-bottom: 24px;">
        <h2 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">Edit team</h2>
        <form method="POST" action="{{ route('organizer.teams.update', $team) }}">
            @csrf @method('PUT')
            <div style="margin-bottom: 12px;">
                <label class="form-label" for="name">Name</label>
                <input type="text" name="name" id="name" class="form-input" value="{{ old('name', $team->name) }}" required>
            </div>
            <div style="margin-bottom: 16px;">
                <label class="form-label" for="segment_id">Segment</label>
                <select name="segment_id" id="segment_id" class="form-input">
                    <option value="">—</option>
                    @foreach ($team->hackathon->segments as $seg)
                        <option value="{{ $seg->id }}" @selected(old('segment_id', $team->segment_id) == $seg->id)>{{ $seg->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>

    <div class="card" style="padding: 24px; margin-bottom: 24px;">
        <h2 style="font-size: 16px; font-weight: 600; margin-bottom: 12px;">Members</h2>
        <ul style="margin: 0; padding-left: 18px;">
            @foreach ($team->members as $m)
                <li>{{ $m->user->name }} ({{ $m->role->value }})</li>
            @endforeach
        </ul>
    </div>

    @can('ban', $team)
        @if (! $team->is_banned)
            <div class="card" style="padding: 24px; margin-bottom: 24px;">
                <h2 style="font-size: 16px; font-weight: 600; margin-bottom: 12px;">Ban team</h2>
                <form method="POST" action="{{ route('organizer.teams.ban', $team) }}">
                    @csrf
                    <textarea name="reason" class="form-input" rows="3" placeholder="Reason" required>{{ old('reason') }}</textarea>
                    <button type="submit" class="btn" style="margin-top: 12px; background: var(--danger); color: white; border: none;">Ban team</button>
                </form>
            </div>
        @endif
    @endcan

    @can('unban', $team)
        @if ($team->is_banned)
            <form method="POST" action="{{ route('organizer.teams.unban', $team) }}">
                @csrf
                <button type="submit" class="btn btn-secondary">Unban team</button>
            </form>
        @endif
    @endcan
@endsection
