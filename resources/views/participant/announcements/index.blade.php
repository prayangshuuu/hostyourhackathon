@extends('layouts.app')

@section('title', 'Announcements')
@section('meta_description', 'Announcements for ' . $hackathon->title)

@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <span class="separator">/</span>
            <span>{{ $hackathon->title }}</span>
            <span class="separator">/</span>
            <span>Announcements</span>
        </div>
        <div class="page-header-row">
            <h1 class="text-page-title">Announcements</h1>
        </div>
    </div>

    @forelse ($announcements as $announcement)
        @php
            $visClass = match($announcement->visibility->value) {
                'all' => 'badge-visibility-all',
                'registered' => 'badge-visibility-registered',
                'segment' => 'badge-visibility-segment',
            };
        @endphp
        <div class="announcement-card">
            <div class="announcement-card-header">
                <div class="announcement-card-title">
                    {{ $announcement->title }}
                    <span class="badge {{ $visClass }}" style="font-size:var(--font-size-xs);">{{ ucfirst($announcement->visibility->value) }}</span>
                </div>
                <span class="announcement-card-date">
                    {{ $announcement->published_at->format('M d, Y') }}
                </span>
            </div>
            <div class="announcement-card-body">
                {{ str()->limit(strip_tags($announcement->body), 120) }}
                <a href="{{ route('participant.announcements.show', [$hackathon, $announcement]) }}" class="announcement-read-more">Read more</a>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <svg class="empty-state-icon" viewBox="0 0 48 48" fill="none">
                <circle cx="24" cy="24" r="20" stroke="currentColor" stroke-width="2"/>
                <path d="M24 14v12M24 30v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <p class="empty-state-title">No announcements yet</p>
        </div>
    @endforelse
@endsection
