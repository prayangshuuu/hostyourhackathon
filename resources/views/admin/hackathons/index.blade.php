@extends('layouts.admin')

@section('title', 'Hackathons')

@section('content')
    <div class="page-header">
        <h1 class="text-page-title">All Hackathons</h1>
    </div>

    {{-- Tab toggle --}}
    <div style="display: flex; gap: 0; margin-bottom: 24px;">
        @php $currentTab = $tab ?? 'active'; @endphp
        <a href="{{ route('admin.hackathons.index', ['tab' => 'active']) }}" style="
            padding: 7px 16px; font-size: 13px; font-weight: 500; text-decoration: none;
            border: 1px solid; border-radius: var(--radius-md) 0 0 var(--radius-md);
            {{ $currentTab === 'active' ? 'background: var(--accent); color: white; border-color: var(--accent);' : 'background: var(--surface); color: var(--text-secondary); border-color: var(--border);' }}
        ">Active ({{ $active->count() }})</a>
        <a href="{{ route('admin.hackathons.index', ['tab' => 'archived']) }}" style="
            padding: 7px 16px; font-size: 13px; font-weight: 500; text-decoration: none;
            border: 1px solid; border-radius: 0 var(--radius-md) var(--radius-md) 0; margin-left: -1px;
            {{ $currentTab === 'archived' ? 'background: var(--accent); color: white; border-color: var(--accent);' : 'background: var(--surface); color: var(--text-secondary); border-color: var(--border);' }}
        ">Archived ({{ $archived->count() }})</a>
    </div>

    @php $hackathons = $currentTab === 'archived' ? $archived : $active; @endphp

    <x-card>
        <x-table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Creator</th>
                    @if($currentTab === 'active')
                        <th>Status</th>
                        <th>Teams</th>
                        <th>Submissions</th>
                        <th>Created</th>
                    @else
                        <th>Deleted At</th>
                    @endif
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hackathons as $hackathon)
                    <tr>
                        <td style="font-weight: 500; color: var(--text-primary);">{{ $hackathon->title }}</td>
                        <td style="font-size: 13px;">{{ $hackathon->creator?->name ?? 'Unknown' }}</td>
                        
                        @if($currentTab === 'active')
                            <td>
                                @php
                                    $statusVal = $hackathon->status instanceof \App\Enums\HackathonStatus ? $hackathon->status->value : $hackathon->status;
                                    $statusVariant = match($statusVal) {
                                        'draft' => 'neutral',
                                        'published' => 'info',
                                        'ongoing' => 'success',
                                        'ended' => 'amber',
                                        default => 'neutral',
                                    };
                                @endphp
                                <x-badge :variant="$statusVariant">{{ ucfirst($statusVal) }}</x-badge>
                            </td>
                            <td>{{ $hackathon->teams_count }}</td>
                            <td>{{ $hackathon->submissions_count }}</td>
                            <td style="font-size: 13px;">{{ $hackathon->created_at->format('M d, Y') }}</td>
                            <td>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <a href="{{ route('hackathons.show', $hackathon) }}" style="
                                        padding: 6px; border-radius: var(--radius-md); color: var(--text-secondary);
                                        display: inline-flex; text-decoration: none;
                                    " title="View" target="_blank" onmouseover="this.style.background='var(--surface-alt)'" onmouseout="this.style.background='transparent'">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.hackathons.destroy', $hackathon->id) }}" style="margin: 0;" onsubmit="return confirm('Soft-delete this hackathon?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="
                                            padding: 6px; border-radius: var(--radius-md); color: var(--danger);
                                            background: none; border: none; cursor: pointer; display: inline-flex;
                                        " title="Delete" onmouseover="this.style.background='var(--surface-alt)'" onmouseout="this.style.background='transparent'">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        @else
                            <td style="font-size: 13px;">{{ $hackathon->deleted_at?->format('M d, Y') ?? '—' }}</td>
                            <td>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <form method="POST" action="{{ route('admin.hackathons.restore', $hackathon->id) }}" style="margin: 0;">
                                        @csrf
                                        <x-button type="submit" variant="secondary" size="sm">Restore</x-button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.hackathons.force-delete', $hackathon->id) }}" style="margin: 0;" onsubmit="return confirm('PERMANENTLY delete this hackathon? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <x-button type="submit" variant="danger" size="sm">Force Delete</x-button>
                                    </form>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $currentTab === 'active' ? 7 : 4 }}" style="text-align: center; padding: 32px; color: var(--text-muted);">
                            No hackathons found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>
    </x-card>
@endsection
