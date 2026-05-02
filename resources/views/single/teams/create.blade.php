@extends('layouts.public')

@section('title', 'Join ' . $hackathon->title)

@section('content')
<div style="max-width: 600px; margin: 48px auto; padding: 0 24px;">
    <div style="text-align: center; margin-bottom: 40px;">
        <h1 style="font-size: 28px; font-weight: 800; color: var(--text-primary);">Create Your Team</h1>
        <p style="font-size: 16px; color: var(--text-secondary); margin-top: 8px;">
            Join {{ $hackathon->title }} by forming a new team.
        </p>
    </div>

    <x-card>
        <form method="POST" action="{{ route('single.teams.store') }}" class="stack">
            @csrf
            
            <x-input 
                label="Team Name" 
                name="name" 
                placeholder="Enter a creative name" 
                required 
                :error="$errors->first('name')"
                value="{{ old('name') }}"
            />

            <x-input 
                label="Select Track / Segment" 
                name="segment_id" 
                type="select" 
                required 
                :error="$errors->first('segment_id')"
            >
                <option value="">Choose a track...</option>
                @foreach($segments as $segment)
                    <option value="{{ $segment->id }}" {{ (old('segment_id') == $segment->id || request('segment_id') == $segment->id) ? 'selected' : '' }} {{ $segment->isFull() ? 'disabled' : '' }}>
                        {{ $segment->name }} {{ $segment->isFull() ? '(Full)' : '' }}
                    </option>
                @endforeach
            </x-input>

            <div style="padding-top: 12px;">
                <x-button type="submit" variant="primary" fullWidth size="lg">Create Team</x-button>
            </div>
            
            <p style="font-size: 13px; color: var(--text-muted); text-align: center; margin-top: 16px;">
                By creating a team, you agree to follow the hackathon rules and code of conduct.
            </p>
        </form>
    </x-card>

    <div style="margin-top: 32px; text-align: center;">
        <p style="font-size: 14px; color: var(--text-secondary);">
            Already have a team? Ask your leader for an invite link.
        </p>
        <div style="margin-top: 16px;">
            <a href="{{ route('single.segments.index') }}" style="color: var(--accent); font-weight: 600; font-size: 14px;">← Back to Segments</a>
        </div>
    </div>
</div>
@endsection
