@extends('layouts.app')

@section('title', 'Hackathons')

@section('content')
    <div class="page-header" style="margin-bottom:24px;">
        <h1 class="text-page-title">Hackathons</h1>
    </div>

    {{-- Tab Toggle --}}
    <div class="pill-toggle-group" style="margin-bottom:24px;" id="hackathon-tabs">
        <button class="pill-toggle {{ $tab === 'active' ? 'active' : '' }}" data-tab="active">Active</button>
        <button class="pill-toggle {{ $tab === 'archived' ? 'active' : '' }}" data-tab="archived">Archived</button>
    </div>

    {{-- Active Table --}}
    <div id="panel-active" class="{{ $tab === 'active' ? '' : 'hidden' }}" style="{{ $tab !== 'active' ? 'display:none;' : '' }}">
        <div class="ds-table-wrapper">
            <table class="ds-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Organizer</th>
                        <th>Status</th>
                        <th>Segments</th>
                        <th>Teams</th>
                        <th>Submissions</th>
                        <th>Created</th>
                        <th style="width:80px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($active as $hackathon)
                        @php
                            $statusClass = match($hackathon->status->value) {
                                'draft' => 'badge-partial',
                                'published' => 'badge-scored',
                                'ongoing' => 'badge-pending',
                                'ended' => 'badge-partial',
                                'archived' => 'badge-partial',
                                default => 'badge-partial',
                            };
                        @endphp
                        <tr>
                            <td style="font-weight:var(--font-weight-medium);">{{ $hackathon->title }}</td>
                            <td style="font-size:var(--font-size-sm); color:var(--color-text-muted);">{{ $hackathon->creator?->name ?? '—' }}</td>
                            <td><span class="badge {{ $statusClass }}">{{ ucfirst($hackathon->status->value) }}</span></td>
                            <td>{{ $hackathon->segments_count }}</td>
                            <td>{{ $hackathon->teams_count }}</td>
                            <td>{{ $hackathon->submissions_count }}</td>
                            <td style="font-size:var(--font-size-xs); color:var(--color-text-muted);">{{ $hackathon->created_at->format('M d, Y') }}</td>
                            <td>
                                <div style="display:flex; gap:4px;">
                                    <a href="{{ route('hackathons.show', $hackathon) }}" class="btn-icon" title="View" target="_blank">
                                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M1.333 8s2.667-5.333 6.667-5.333S14.667 8 14.667 8s-2.667 5.333-6.667 5.333S1.333 8 1.333 8Z" stroke="currentColor" stroke-width="1.2"/><circle cx="8" cy="8" r="2" stroke="currentColor" stroke-width="1.2"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.hackathons.force-delete', $hackathon->id) }}" style="margin:0;" onsubmit="return confirm('PERMANENTLY delete this hackathon? This cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon" title="Force Delete" style="color:var(--color-danger);">
                                            <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M2 4h12M5.333 4V2.667a1.333 1.333 0 0 1 1.334-1.334h2.666a1.333 1.333 0 0 1 1.334 1.334V4m2 0v9.333a1.333 1.333 0 0 1-1.334 1.334H4.667a1.333 1.333 0 0 1-1.334-1.334V4h9.334Z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8"><div class="ds-table-empty">No active hackathons.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Archived Table --}}
    <div id="panel-archived" style="{{ $tab !== 'archived' ? 'display:none;' : '' }}">
        <div class="ds-table-wrapper">
            <table class="ds-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Organizer</th>
                        <th>Deleted At</th>
                        <th style="width:80px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($archived as $hackathon)
                        <tr>
                            <td style="font-weight:var(--font-weight-medium);">{{ $hackathon->title }}</td>
                            <td style="font-size:var(--font-size-sm); color:var(--color-text-muted);">{{ $hackathon->creator?->name ?? '—' }}</td>
                            <td style="font-size:var(--font-size-xs); color:var(--color-text-muted);">{{ $hackathon->deleted_at->format('M d, Y') }}</td>
                            <td>
                                <div style="display:flex; gap:4px;">
                                    <form method="POST" action="{{ route('admin.hackathons.restore', $hackathon->id) }}" style="margin:0;">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary btn-sm" title="Restore">Restore</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><div class="ds-table-empty">No archived hackathons.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const pills = document.querySelectorAll('#hackathon-tabs .pill-toggle');
            pills.forEach(function (pill) {
                pill.addEventListener('click', function () {
                    pills.forEach(p => p.classList.remove('active'));
                    this.classList.add('active');
                    document.getElementById('panel-active').style.display = this.dataset.tab === 'active' ? '' : 'none';
                    document.getElementById('panel-archived').style.display = this.dataset.tab === 'archived' ? '' : 'none';
                });
            });
        });
    </script>
    @endpush
@endsection
