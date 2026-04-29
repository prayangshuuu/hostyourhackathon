@extends('layouts.app')

@section('title', 'Create Team')

@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <span class="separator">/</span>
            <a href="{{ route('teams.index') }}">Teams</a>
            <span class="separator">/</span>
            <span>{{ $hackathon->title }}</span>
            <span class="separator">/</span>
            <span>Create Team</span>
        </div>
        <div class="page-header-row">
            <div>
                <h1 class="text-page-title">Create Team</h1>
                <p class="page-header-description">Register a team for {{ $hackathon->title }}.</p>
            </div>
        </div>
    </div>

    <div class="card" style="max-width:640px;">
        <div class="card-header">
            <h2 class="text-card-title">Team Details</h2>
        </div>

        <form method="POST" action="{{ route('teams.store', $hackathon) }}" id="form-create-team">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">Team Name</label>
                <input type="text" name="name" id="name"
                       class="form-input @error('name') is-invalid @enderror"
                       value="{{ old('name') }}"
                       placeholder="e.g. Code Crusaders" required>
                @error('name') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            @if ($hackathon->segments->count())
                <div class="form-group">
                    <label for="segment_id" class="form-label">Segment / Track</label>
                    <select name="segment_id" id="segment_id" class="form-select @error('segment_id') is-invalid @enderror">
                        <option value="">— No segment —</option>
                        @foreach ($hackathon->segments as $segment)
                            <option value="{{ $segment->id }}" {{ old('segment_id') == $segment->id ? 'selected' : '' }}>
                                {{ $segment->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('segment_id') <p class="form-error">{{ $message }}</p> @enderror
                    <p class="form-helper">Choose the track your team will compete in.</p>
                </div>
            @endif

            <div style="display:flex; gap:8px; justify-content:flex-end; padding-top:8px;">
                <a href="{{ route('teams.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary" id="btn-submit-team">Create Team</button>
            </div>
        </form>
    </div>

    {{-- Info card --}}
    <div class="card" style="max-width:640px; margin-top:24px;">
        <div style="display:flex; gap:12px;">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" style="flex-shrink:0; margin-top:2px; color:var(--color-text-muted);">
                <path d="M8 1.333A6.667 6.667 0 1 0 14.667 8 6.674 6.674 0 0 0 8 1.333Zm0 10.334a.667.667 0 1 1 0-1.334.667.667 0 0 1 0 1.334ZM8.667 8a.667.667 0 0 1-1.334 0V5.333a.667.667 0 0 1 1.334 0V8Z" fill="currentColor"/>
            </svg>
            <div>
                <p style="font-size:var(--font-size-sm); color:var(--color-text-secondary); margin-bottom:4px;">
                    Team size: <strong style="color:var(--color-text-primary); font-weight:var(--font-weight-medium);">{{ $hackathon->min_team_size }}–{{ $hackathon->max_team_size }} members</strong>
                </p>
                <p style="font-size:var(--font-size-sm); color:var(--color-text-secondary);">
                    Solo participation: <strong style="color:var(--color-text-primary); font-weight:var(--font-weight-medium);">{{ $hackathon->allow_solo ? 'Allowed' : 'Not allowed' }}</strong>
                </p>
            </div>
        </div>
    </div>
@endsection
