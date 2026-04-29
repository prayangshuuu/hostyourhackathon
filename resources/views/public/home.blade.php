@extends('layouts.public')

@section('title', config('app.name') . ' — Run Your Hackathon, Your Way')
@section('meta_description', 'Open-source hackathon platform. Self-hosted, shared hosting ready.')

@section('content')
    {{-- Hero Section --}}
    <section style="padding: 80px 0 64px; text-align: center;">
        <div style="max-width: 680px; margin: 0 auto;">
            <h1 style="font-size: 36px; font-weight: 600; color: var(--text-primary); line-height: 1.25; margin: 0;">
                Run Your Hackathon, Your Way
            </h1>
            <p style="font-size: 16px; color: var(--text-secondary); margin-top: 12px; margin-bottom: 0; line-height: 1.6;">
                Open-source hackathon platform. Self-hosted, shared hosting ready.
            </p>
            <div style="display: flex; align-items: center; justify-content: center; gap: 12px; margin-top: 28px; flex-wrap: wrap;">
                <x-button href="{{ route('hackathons.index') }}" variant="primary" size="md">Browse Hackathons</x-button>
                <x-button href="https://github.com/prayangshuuu/hostyourhackathon" variant="secondary" size="md">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 6px;">
                        <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                    </svg>
                    View on GitHub
                </x-button>
            </div>
        </div>
    </section>

    {{-- Active Hackathons --}}
    <section style="padding-bottom: 80px;">
        <h2 style="font-size: 20px; font-weight: 600; color: var(--text-primary); margin: 0 0 24px 0;">Active Hackathons</h2>

        @if ($hackathons->count())
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;">
                @foreach ($hackathons as $hackathon)
                    <x-hackathon-card :hackathon="$hackathon" />
                @endforeach
            </div>

            <div style="text-align: right; margin-top: 16px;">
                <a href="{{ route('hackathons.index') }}" style="font-size: 14px; color: var(--accent); text-decoration: none; font-weight: 500;">
                    View all hackathons →
                </a>
            </div>
        @else
            <div style="
                background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg);
                padding: 48px 24px; text-align: center;
            ">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--text-muted)" stroke-width="1.5" style="margin-bottom: 12px;">
                    <rect x="3" y="3" width="18" height="18" rx="3"/>
                    <path d="M8 12h8M12 8v8" stroke-linecap="round"/>
                </svg>
                <p style="font-size: 14px; color: var(--text-muted); margin: 0;">
                    No active hackathons right now. Check back soon.
                </p>
            </div>
        @endif
    </section>
@endsection
