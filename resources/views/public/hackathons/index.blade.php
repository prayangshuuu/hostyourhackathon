@extends('layouts.public')

@section('title', 'Hackathons — ' . config('app.name'))
@section('meta_description', 'Browse and discover hackathons on ' . config('app.name'))

@section('content')
    <h1 style="font-size: 24px; font-weight: 600; color: var(--text-primary); margin: 32px 0 32px 0;">Hackathons</h1>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('hackathons.index') }}" style="
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 28px; flex-wrap: wrap; gap: 12px;
    ">
        {{-- Status Pills --}}
        <div style="display: flex; gap: 0;">
            @php
                $currentStatus = request('status', '');
                $pills = [
                    '' => 'All',
                    'ongoing' => 'Ongoing',
                    'published' => 'Published',
                    'ended' => 'Ended',
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

        {{-- Search --}}
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

    {{-- Card Grid --}}
    @if ($hackathons->count())
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;">
            @foreach ($hackathons as $hackathon)
                <x-hackathon-card :hackathon="$hackathon" />
            @endforeach
        </div>

        {{-- Pagination --}}
        @if ($hackathons->hasPages())
            <div style="margin-top: 32px; display: flex; justify-content: center;">
                {{ $hackathons->links() }}
            </div>
        @endif
    @else
        <div style="
            background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg);
            padding: 64px 24px; text-align: center;
        ">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--text-muted)" stroke-width="1.5" style="margin-bottom: 12px;">
                <circle cx="11" cy="11" r="8"/>
                <path d="M21 21l-4.35-4.35" stroke-linecap="round"/>
            </svg>
            <p style="font-size: 15px; color: var(--text-secondary); margin: 0; font-weight: 500;">No hackathons found</p>
            <p style="font-size: 13px; color: var(--text-muted); margin: 6px 0 0 0;">Try adjusting your filters or search term.</p>
        </div>
    @endif

    <div style="height: 64px;"></div>
@endsection
