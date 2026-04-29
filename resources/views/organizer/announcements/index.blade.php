@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
    <div class="page-header">
        <div class="page-header-breadcrumb">
            <a href="{{ route('organizer.hackathons.index') }}">My Hackathons</a>
            <span class="separator">/</span>
            <a href="{{ route('organizer.hackathons.show', $hackathon) }}">{{ $hackathon->title }}</a>
            <span class="separator">/</span>
            <span>Announcements</span>
        </div>
        <div class="page-header-row">
            <h1 class="text-page-title">Announcements</h1>
            <a href="{{ route('organizer.announcements.create', $hackathon) }}">
                <x-button variant="primary">New Announcement</x-button>
            </a>
        </div>
    </div>

    <x-alert />

    <x-card>
        <x-table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Visibility</th>
                    <th>Status</th>
                    <th>Published / Scheduled At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($announcements as $announcement)
                    <tr>
                        <td style="font-weight: 500; color: var(--text-primary);">{{ $announcement->title }}</td>
                        <td>
                            @php
                                $visVariant = match($announcement->visibility) {
                                    'all' => 'neutral',
                                    'registered' => 'indigo',
                                    'segment' => 'amber',
                                    default => 'neutral',
                                };
                                $visText = match($announcement->visibility) {
                                    'all' => 'All Participants',
                                    'registered' => 'Registered Teams',
                                    'segment' => 'Segment: ' . ($announcement->segment->name ?? 'Unknown'),
                                    default => ucfirst($announcement->visibility),
                                };
                            @endphp
                            <x-badge :variant="$visVariant">{{ $visText }}</x-badge>
                        </td>
                        <td>
                            @php
                                $statusVariant = match($announcement->status) {
                                    'draft' => 'neutral',
                                    'scheduled' => 'warning',
                                    'published' => 'success',
                                    default => 'neutral',
                                };
                            @endphp
                            <x-badge :variant="$statusVariant">{{ ucfirst($announcement->status) }}</x-badge>
                        </td>
                        <td>
                            @if($announcement->status === 'draft')
                                <span class="text-muted">—</span>
                            @elseif($announcement->status === 'scheduled')
                                {{ $announcement->scheduled_at->format('M d, Y h:i A') }}
                            @else
                                {{ $announcement->published_at ? $announcement->published_at->format('M d, Y h:i A') : '—' }}
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px; align-items: center;">
                                <a href="{{ route('organizer.announcements.edit', [$hackathon, $announcement]) }}" class="btn btn-ghost" style="padding: 6px;" title="Edit">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                </a>

                                @if($announcement->status !== 'published')
                                    <form method="POST" action="{{ route('organizer.announcements.publish', [$hackathon, $announcement]) }}" style="margin: 0;">
                                        @csrf
                                        <button type="submit" class="btn btn-primary" style="padding: 4px 12px; font-size: 12px; height: 28px;">Publish</button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('organizer.announcements.destroy', [$hackathon, $announcement]) }}" style="margin: 0;" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-danger" style="padding: 6px;" title="Delete">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 32px; color: var(--text-muted);">
                            No announcements found. Create one to keep participants updated.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>
    </x-card>
@endsection
