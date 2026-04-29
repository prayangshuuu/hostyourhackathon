@extends('layouts.admin')

@section('title', 'Hackathons')

@section('content')
    <div class="page-header">
        <h1 class="text-page-title">All Hackathons</h1>
    </div>

    {{-- Tab toggle --}}
    <div class="filter-pills" style="display: flex; gap: 8px; margin-bottom: 24px;">
        @php
            $currentTab = request('tab', 'active');
        @endphp
        <a href="{{ route('admin.hackathons.index', ['tab' => 'active']) }}" 
           class="badge {{ $currentTab === 'active' ? 'badge-primary' : 'badge-neutral' }}" 
           style="padding: 6px 12px; font-size: 14px; text-decoration: none; {{ $currentTab === 'active' ? 'background: var(--accent); color: white;' : '' }}">
            Active
        </a>
        <a href="{{ route('admin.hackathons.index', ['tab' => 'archived']) }}" 
           class="badge {{ $currentTab === 'archived' ? 'badge-primary' : 'badge-neutral' }}" 
           style="padding: 6px 12px; font-size: 14px; text-decoration: none; {{ $currentTab === 'archived' ? 'background: var(--accent); color: white;' : '' }}">
            Archived
        </a>
    </div>

    <x-card>
        <x-table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Organizer</th>
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
                        <td>{{ $hackathon->organizer->name ?? 'Unknown' }}</td>
                        
                        @if($currentTab === 'active')
                            <td>
                                @php
                                    $statusVariant = match($hackathon->status) {
                                        'draft' => 'neutral',
                                        'published' => 'indigo',
                                        'ongoing' => 'success',
                                        'ended' => 'amber',
                                        default => 'neutral',
                                    };
                                @endphp
                                <x-badge :variant="$statusVariant">{{ ucfirst($hackathon->status) }}</x-badge>
                            </td>
                            <td>{{ $hackathon->teams_count ?? $hackathon->teams()->count() }}</td>
                            <td>{{ $hackathon->submissions_count ?? $hackathon->submissions()->count() }}</td>
                            <td style="font-size: 13px;">{{ $hackathon->created_at->format('M d, Y') }}</td>
                            <td>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <a href="{{ route('public.hackathons.show', $hackathon->slug ?? $hackathon->id) }}" class="btn btn-ghost" style="padding: 6px;" title="View" target="_blank">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.hackathons.destroy', $hackathon->id) }}" style="margin: 0;" onsubmit="return confirm('Are you sure you want to FORCE DELETE this hackathon? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-ghost btn-danger" style="padding: 6px;" title="Force Delete">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        @else
                            <td style="font-size: 13px;">{{ $hackathon->deleted_at->format('M d, Y') }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.hackathons.restore', $hackathon->id) }}" style="margin: 0;">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary btn-sm">Restore</button>
                                </form>
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
        
        @if(method_exists($hackathons, 'links'))
            <div style="padding: 16px; border-top: 1px solid var(--border-subtle);">
                <style>
                    nav[role="navigation"] { display: flex; align-items: center; justify-content: space-between; }
                    .pagination-btn { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: var(--radius-md); border: 1px solid var(--border); color: var(--text-primary); text-decoration: none; font-size: 14px; transition: background 150ms ease; }
                    .pagination-btn:hover { background: var(--surface-alt); }
                    .pagination-btn.active { background: var(--accent); color: white; border-color: var(--accent); }
                </style>
                {{ $hackathons->links() }}
            </div>
        @endif
    </x-card>
@endsection
