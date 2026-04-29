@extends('layouts.app')

@section('title', $announcement->title)

@section('content')
    <div class="page-header" style="margin-bottom: 24px;">
        <div class="page-header-breadcrumb">
            <a href="{{ route('announcements.index') }}">Announcements</a>
            <span class="separator">/</span>
            <span>{{ $announcement->title }}</span>
        </div>
        <div class="page-header-row" style="flex-direction: column; align-items: flex-start; gap: 12px;">
            <h1 class="text-page-title" style="margin: 0;">{{ $announcement->title }}</h1>
            <div style="display: flex; align-items: center; gap: 16px; font-size: 13px; color: var(--text-muted);">
                <span>{{ $announcement->published_at ? $announcement->published_at->format('M d, Y h:i A') : 'Draft' }}</span>
                @php
                    $visVariant = match($announcement->visibility) {
                        'all' => 'neutral',
                        'registered' => 'indigo',
                        'segment' => 'amber',
                        default => 'neutral',
                    };
                @endphp
                <x-badge :variant="$visVariant">{{ ucfirst($announcement->visibility) }}</x-badge>
                <span>{{ $announcement->hackathon->title }}</span>
            </div>
        </div>
    </div>

    <x-card>
        <div style="font-size: 15px; color: var(--text-primary); line-height: 1.7; padding: 32px;">
            {!! nl2br(e($announcement->body)) !!}
        </div>
    </x-card>
@endsection
