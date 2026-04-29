@extends('layouts.public')

@section('title', config('app.name') . ' — Host and Join Hackathons')
@section('meta_description', 'Discover, organize, and participate in hackathons with ' . config('app.name'))

@section('content')
    {{-- Hero Section --}}
    <section class="hero">
        <h1 class="hero-title">Where Ideas Become Reality</h1>
        <p class="hero-subtitle">
            Discover hackathons, build with your team, and ship projects that matter.
            All in one platform built for organizers and participants.
        </p>
        <div class="hero-actions">
            <a href="{{ route('hackathons.index') }}" class="btn btn-primary">Browse Hackathons</a>
            <a href="#upcoming" class="btn btn-secondary">Learn More</a>
        </div>
    </section>

    {{-- Upcoming Hackathons --}}
    <section id="upcoming" style="margin-top:64px; padding-bottom:80px;">
        <h2 class="text-page-title" style="margin-bottom:24px;">Upcoming Hackathons</h2>

        @if ($hackathons->count())
            <div class="grid-3">
                @foreach ($hackathons as $hackathon)
                    @include('components.hackathon-card', ['hackathon' => $hackathon])
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <svg class="empty-state-icon" viewBox="0 0 48 48" fill="none">
                    <circle cx="24" cy="24" r="20" stroke="currentColor" stroke-width="2"/>
                    <path d="M16 24h16M24 16v16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <p class="empty-state-title">No hackathons yet</p>
            </div>
        @endif
    </section>
@endsection
