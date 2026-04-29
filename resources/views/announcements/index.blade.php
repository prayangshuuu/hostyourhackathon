@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
    <div class="page-header">
        <div class="page-header-row">
            <h1 class="text-page-title">Announcements</h1>
        </div>
    </div>

    {{-- Filter pills row --}}
    <div class="filter-pills" style="display: flex; gap: 8px; margin-bottom: 24px;">
        @php
            $currentFilter = request('filter', 'all');
        @endphp
        <a href="{{ route('announcements.index', ['filter' => 'all']) }}" 
           class="badge {{ $currentFilter === 'all' ? 'badge-primary' : 'badge-neutral' }}" 
           style="padding: 6px 12px; font-size: 14px; text-decoration: none; {{ $currentFilter === 'all' ? 'background: var(--accent); color: white;' : '' }}">
            All
        </a>
        <a href="{{ route('announcements.index', ['filter' => 'registered']) }}" 
           class="badge {{ $currentFilter === 'registered' ? 'badge-primary' : 'badge-neutral' }}" 
           style="padding: 6px 12px; font-size: 14px; text-decoration: none; {{ $currentFilter === 'registered' ? 'background: var(--accent); color: white;' : '' }}">
            Registered
        </a>
        <a href="{{ route('announcements.index', ['filter' => 'segment']) }}" 
           class="badge {{ $currentFilter === 'segment' ? 'badge-primary' : 'badge-neutral' }}" 
           style="padding: 6px 12px; font-size: 14px; text-decoration: none; {{ $currentFilter === 'segment' ? 'background: var(--accent); color: white;' : '' }}">
            Segment
        </a>
    </div>

    <div>
        @forelse($announcements as $announcement)
            <a href="{{ route('announcements.show', $announcement) }}" style="text-decoration: none; display: block;">
                <div class="announcement-card" style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 20px; margin-bottom: 12px; transition: border-color 150ms ease;" onmouseover="this.style.borderColor='var(--accent)'" onmouseout="this.style.borderColor='var(--border)'">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <h2 style="margin: 0; font-size: 15px; font-weight: 600; color: var(--text-primary);">{{ $announcement->title }}</h2>
                            @php
                                $visVariant = match($announcement->visibility) {
                                    'all' => 'neutral',
                                    'registered' => 'indigo',
                                    'segment' => 'amber',
                                    default => 'neutral',
                                };
                            @endphp
                            <x-badge :variant="$visVariant">{{ ucfirst($announcement->visibility) }}</x-badge>
                        </div>
                        <span style="font-size: 12px; color: var(--text-muted); white-space: nowrap;">
                            {{ $announcement->published_at ? $announcement->published_at->diffForHumans() : '' }}
                        </span>
                    </div>
                    
                    <p style="font-size: 14px; color: var(--text-secondary); margin: 8px 0 0 0; line-height: 1.5;">
                        {{ Str::limit(strip_tags($announcement->body), 140) }}
                    </p>
                    
                    <span style="color: var(--accent); font-size: 13px; margin-top: 8px; display: block; font-weight: 500;">
                        Read more &rarr;
                    </span>
                </div>
            </a>
        @empty
            <div style="text-align: center; padding: 48px 24px;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-muted); margin-bottom: 16px; display: inline-block;"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                <p style="font-size: 14px; color: var(--text-muted); margin: 0;">No announcements yet</p>
            </div>
        @endforelse
        
        @if(method_exists($announcements, 'links'))
            <div style="margin-top: 24px;">
                {{ $announcements->links() }}
            </div>
        @endif
    </div>
@endsection
