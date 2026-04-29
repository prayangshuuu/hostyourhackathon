@extends('layouts.participant')

@section('title', $announcement->title)
@section('meta_description', str()->limit(strip_tags($announcement->body), 160))

@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <span class="separator">/</span>
            <span>{{ $hackathon->title }}</span>
            <span class="separator">/</span>
            <a href="{{ route('participant.announcements.index', $hackathon) }}">Announcements</a>
            <span class="separator">/</span>
            <span>{{ str()->limit($announcement->title, 30) }}</span>
        </div>
        <div class="page-header-row">
            <h1 class="text-page-title">{{ $announcement->title }}</h1>
        </div>
        <div class="announcement-meta">
            @php
                $visClass = match($announcement->visibility->value) {
                    'all' => 'badge-visibility-all',
                    'registered' => 'badge-visibility-registered',
                    'segment' => 'badge-visibility-segment',
                };
            @endphp
            <span>{{ $announcement->published_at->format('M d, Y · h:i A') }}</span>
            <span class="badge {{ $visClass }}">{{ ucfirst($announcement->visibility->value) }}</span>
        </div>
    </div>

    {{-- Content Card --}}
    <div class="card">
        <div class="announcement-full-body">
            {!! nl2br(e($announcement->body)) !!}
        </div>
    </div>
@endsection
