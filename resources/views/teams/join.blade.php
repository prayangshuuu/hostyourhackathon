<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Join {{ $team->name }} — {{ config('app.name') }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    {{-- Scripts --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="organizer-shell">
    <div class="centered-page">
        <div class="centered-card">

            {{-- Flash --}}
            @if (session('error'))
                <div class="alert alert-error" role="alert" style="margin-bottom:16px;">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 1.333A6.667 6.667 0 1 0 14.667 8 6.674 6.674 0 0 0 8 1.333Zm2.473 8.527a.667.667 0 0 1-.946.946L8 9.28l-1.527 1.527a.667.667 0 0 1-.946-.946L7.054 8.333 5.527 6.807a.667.667 0 0 1 .946-.947L8 7.387l1.527-1.527a.667.667 0 0 1 .946.947L8.946 8.333l1.527 1.527Z" fill="currentColor"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            <div class="card">
                {{-- Header --}}
                <div class="card-header" style="text-align:center;">
                    <p style="font-size:var(--font-size-xs); text-transform:uppercase; letter-spacing:0.05em; color:var(--color-text-muted); margin-bottom:8px;">
                        You've been invited to join
                    </p>
                    <h1 style="font-size:var(--font-size-xl); font-weight:var(--font-weight-semibold); color:var(--color-text-primary); margin-bottom:4px;">
                        {{ $team->name }}
                    </h1>
                </div>

                {{-- Team details --}}
                <div style="display:flex; flex-direction:column; gap:16px; margin-bottom:24px;">
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
                        <div style="font-size:var(--font-size-sm); color:var(--color-text-primary);">
                            {{ $team->members_count }} / {{ $team->hackathon->max_team_size }}
                            @if ($team->members_count >= $team->hackathon->max_team_size)
                                <span style="color:var(--color-danger); margin-left:6px;">Full</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div style="display:flex; gap:8px; justify-content:center;">
                    <a href="{{ route('teams.index') }}" class="btn btn-secondary">Cancel</a>
                    @if ($team->members_count < $team->hackathon->max_team_size)
                        <form method="POST" action="{{ route('teams.join.accept', $team->invite_code) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary" id="btn-join-team">Join Team</button>
                        </form>
                    @else
                        <button class="btn btn-primary" disabled>Team Full</button>
                    @endif
                </div>
            </div>

            {{-- Attribution --}}
            <p style="text-align:center; font-size:var(--font-size-xs); color:var(--color-text-muted); margin-top:16px;">
                {{ config('app.name') }}
            </p>
        </div>
    </div>
</body>
</html>
