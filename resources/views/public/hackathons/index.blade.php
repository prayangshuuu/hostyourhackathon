@extends('layouts.public')

@section('title', 'Hackathons — ' . config('app.name'))
@section('meta_description', 'Browse and discover hackathons on ' . config('app.name'))

@section('content')
    <h1 style="font-size: 24px; font-weight: 600; color: var(--text-primary); margin: 32px 0 32px 0;">Hackathons</h1>

    <form method="GET" action="{{ route('hackathons.index') }}" style="
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 28px; flex-wrap: wrap; gap: 12px;
    ">
        <div style="display: flex; gap: 0;">
            @php
                $currentStatus = request('status', '');
                $pills = [
                    '' => 'All visible',
                    'published' => 'Published',
                    'ongoing' => 'Ongoing',
                    'ended' => 'Ended',
                    'archived' => 'Archived',
                ];
            @endphp
            @foreach ($pills as $value => $label)
                @php
                    $isActive = $currentStatus === $value;
                    $activeStyle = $isActive
                        ? 'background: var(--accent); color: white; border-color: var(--accent);'
                        : 'background: var(--surface); color: var(--text-secondary); border-color: var(--border);';
                @endphp
                <a href="{{ route('hackathons.index', array_filter(['status' => $value ?: null, 'search' => request('search')])) }}" style="
                    padding: 7px 16px; font-size: 13px; font-weight: 500;
                    border: 1px solid; text-decoration: none;
                    {{ $activeStyle }}
                    {{ $loop->first ? 'border-radius: var(--radius-md) 0 0 var(--radius-md);' : '' }}
                    {{ $loop->last ? 'border-radius: 0 var(--radius-md) var(--radius-md) 0;' : '' }}
                    {{ !$loop->first && !$loop->last ? 'border-radius: 0;' : '' }}
                    {{ !$loop->first ? 'margin-left: -1px;' : '' }}
                    transition: background 150ms ease;
                ">{{ $label }}</a>
            @endforeach
        </div>

        <div>
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search hackathons..."
                style="
                    width: 220px; padding: 8px 12px; font-size: 14px;
                    border: 1px solid var(--border); border-radius: var(--radius-md);
                    background: var(--surface); color: var(--text-primary);
                    font-family: Inter, sans-serif; outline: none;
                    transition: border-color 150ms ease;
                "
                onfocus="this.style.borderColor='var(--accent)'"
                onblur="this.style.borderColor='var(--border)'"
                onkeydown="if(event.key==='Enter'){this.form.submit()}"
            >
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
        </div>
    </form>

    @if (!$hasAnyHackathons)
        <div style="text-align: center; padding: 80px 24px;">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--text-muted)" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 20px;">
                <path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M4 13h16M16 21v-4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v4"/>
                <path d="M4 5h16M6 21h12"/>
            </svg>
            <p style="font-size: 17px; font-weight: 600; color: var(--text-primary); margin: 0;">No hackathons yet</p>
            <p style="font-size: 14px; color: var(--text-secondary); margin: 10px 0 0;">Check back soon.</p>
        </div>
    @else
        @if ($activeHackathons->isNotEmpty())
            <h2 style="font-size: 15px; font-weight: 600; color: var(--text-primary); margin: 0 0 16px 0;">Active Hackathons</h2>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 48px;">
                @foreach ($activeHackathons as $hackathon)
                    <x-hackathon-card :hackathon="$hackathon" />
                @endforeach
            </div>
        @endif

        @if ($pastHackathons->isNotEmpty())
            <h2 style="font-size: 15px; font-weight: 600; color: var(--text-primary); margin: 0 0 16px 0;">Past Hackathons</h2>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;">
                @foreach ($pastHackathons as $hackathon)
                    <x-hackathon-card :hackathon="$hackathon" inactive="true" />
                @endforeach
            </div>
        @endif
    @endif

    <div style="height: 64px;"></div>
@endsection
