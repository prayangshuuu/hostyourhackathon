@extends('layouts.app')

@section('title', 'Segments — ' . $hackathon->title)

@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <span class="separator">/</span>
            <a href="{{ route('organizer.hackathons.index') }}">My Hackathons</a>
            <span class="separator">/</span>
            <a href="{{ route('organizer.hackathons.show', $hackathon) }}">{{ Str::limit($hackathon->title, 30) }}</a>
            <span class="separator">/</span>
            <span>Segments</span>
        </div>
        <div class="page-header-row">
            <div>
                <h1 class="text-page-title">Segments</h1>
                <p class="page-header-description">Create and manage different categories or tracks for your hackathon.</p>
            </div>
            <a href="{{ route('organizer.segments.create', $hackathon) }}" class="btn btn-primary btn-sm">
                Add Segment
            </a>
        </div>
    </div>

    {{-- Segments Table --}}
    <div class="card">
        <div class="ds-table-container">
            <table class="ds-table" id="segments-table">
                <thead>
                    <tr>
                        <th style="width: 40px;"></th>
                        <th>Name</th>
                        <th>Teams</th>
                        <th>Submissions</th>
                        <th>Status</th>
                        <th>Window Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="sortable-segments">
                    @forelse ($segments as $segment)
                        <tr data-id="{{ $segment->id }}">
                            <td class="drag-handle" style="cursor: grab; color: var(--color-text-muted);">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="4" y1="9" x2="20" y2="9"></line>
                                    <line x1="4" y1="15" x2="20" y2="15"></line>
                                </svg>
                            </td>
                            <td>
                                <div style="font-weight: 500; color: var(--color-text-primary);">{{ $segment->name }}</div>
                            </td>
                            <td>
                                <span class="badge badge-neutral">{{ $segment->teamCount() }}</span>
                            </td>
                            <td>
                                <span class="badge badge-neutral">{{ $segment->submissionCount() }}</span>
                            </td>
                            <td>
                                @if ($segment->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-neutral">Inactive</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $regOpen = $segment->isRegistrationOpen();
                                    $subOpen = $segment->isSubmissionOpen();
                                @endphp
                                @if ($subOpen)
                                    <span class="badge badge-success">Submission Open</span>
                                @elseif ($regOpen)
                                    <span class="badge badge-primary">Registration Open</span>
                                @else
                                    <span class="badge badge-neutral">Closed</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div style="display: flex; justify-content: flex-end; gap: 8px;">
                                    <a href="{{ route('organizer.segments.show', [$hackathon, $segment]) }}" class="btn btn-secondary btn-xs">Manage</a>
                                    <a href="{{ route('organizer.segments.edit', [$hackathon, $segment]) }}" class="btn-icon" title="Edit">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M11 2l3 3L5 14H2v-3L11 2z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('organizer.segments.destroy', [$hackathon, $segment]) }}" onsubmit="return confirm('Are you sure you want to delete this segment? This will also remove its prizes, criteria, and associations.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-icon text-danger" title="Delete">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 4h12M5 4V2h6v2M4 4v10h8V4"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="ds-table-empty">
                                No segments found. Create your first segment to start categorizing teams.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        const el = document.getElementById('sortable-segments');
        if (el) {
            Sortable.create(el, {
                handle: '.drag-handle',
                animation: 150,
                onEnd: function () {
                    const order = Array.from(el.children).map(row => row.dataset.id);
                    fetch('{{ route('organizer.segments.reorder', $hackathon) }}', {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ order })
                    });
                }
            });
        }
    </script>
    @endpush
@endsection
