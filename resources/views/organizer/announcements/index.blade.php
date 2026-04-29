@extends('layouts.app')

@section('title', 'Announcements')
@section('meta_description', 'Manage announcements for ' . $hackathon->title)

@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-breadcrumb">
            <a href="{{ route('organizer.hackathons.index') }}">Hackathons</a>
            <span class="separator">/</span>
            <a href="{{ route('organizer.hackathons.show', $hackathon) }}">{{ $hackathon->title }}</a>
            <span class="separator">/</span>
            <span>Announcements</span>
        </div>
        <div class="page-header-row">
            <h1 class="text-page-title">Announcements</h1>
            <a href="{{ route('organizer.hackathons.announcements.create', $hackathon) }}" class="btn btn-primary" id="btn-new-announcement">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" style="margin-right:4px;"><path d="M8 3.333v9.334M3.333 8h9.334" stroke="currentColor" stroke-width="1.33" stroke-linecap="round"/></svg>
                New Announcement
            </a>
        </div>
    </div>

    {{-- Announcements Table --}}
    <div class="ds-table-wrapper">
        <table class="ds-table" id="announcements-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Visibility</th>
                    <th>Status</th>
                    <th>Scheduled / Published At</th>
                    <th style="width:80px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($announcements as $announcement)
                    @php
                        $visClass = match($announcement->visibility->value) {
                            'all' => 'badge-visibility-all',
                            'registered' => 'badge-visibility-registered',
                            'segment' => 'badge-visibility-segment',
                        };

                        $status = $announcement->status;
                        $statusClass = match($status) {
                            'draft' => 'badge-partial',
                            'scheduled' => 'badge-pending',
                            'published' => 'badge-scored',
                        };
                    @endphp
                    <tr>
                        <td style="font-weight:var(--font-weight-medium);">{{ $announcement->title }}</td>
                        <td><span class="badge {{ $visClass }}">{{ ucfirst($announcement->visibility->value) }}</span></td>
                        <td><span class="badge {{ $statusClass }}">{{ ucfirst($status) }}</span></td>
                        <td style="color:var(--color-text-muted); font-size:var(--font-size-sm);">
                            @if ($announcement->published_at)
                                {{ $announcement->published_at->format('M d, Y · h:i A') }}
                            @elseif ($announcement->scheduled_at)
                                {{ $announcement->scheduled_at->format('M d, Y · h:i A') }}
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <div style="display:flex; gap:4px;">
                                <a href="{{ route('organizer.hackathons.announcements.edit', [$hackathon, $announcement]) }}" class="btn-icon" title="Edit">
                                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M11.333 2A1.886 1.886 0 0 1 14 4.667l-8.667 8.666L2 14l.667-3.333 8.666-8.667Z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </a>
                                <form method="POST" action="{{ route('organizer.hackathons.announcements.destroy', [$hackathon, $announcement]) }}" style="margin:0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon" title="Delete" style="color:var(--color-danger);" onclick="return confirm('Delete this announcement?')">
                                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M2 4h12M5.333 4V2.667a1.333 1.333 0 0 1 1.334-1.334h2.666a1.333 1.333 0 0 1 1.334 1.334V4m2 0v9.333a1.333 1.333 0 0 1-1.334 1.334H4.667a1.333 1.333 0 0 1-1.334-1.334V4h9.334Z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="ds-table-empty">No announcements yet. Create one to get started.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
