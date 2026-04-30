@extends('layouts.app')

@section('title', 'Judging Dashboard')
@section('meta_description', 'Your judging assignments and scoring progress')

@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-row">
            <div>
                <h1 class="text-page-title">Judging Dashboard</h1>
                @if ($assignedSegments->filter()->count())
                    <div style="display:flex; gap:6px; flex-wrap:wrap; margin-top:8px;">
                        @foreach ($assignedSegments->filter() as $segment)
                            <span class="segment-pill">{{ $segment->name }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="page-header-description">All segments</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $totalAssigned }}</div>
            <div class="stat-label">Total Assigned</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $scored }}</div>
            <div class="stat-label">Scored</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $remaining + $partial }}</div>
            <div class="stat-label">Remaining</div>
        </div>
    </div>

    {{-- Submissions Table --}}
    <div class="ds-table-wrapper">
        <table class="ds-table" id="submissions-table">
            <thead>
                <tr>
                    <th>Team Name</th>
                    <th>Segment</th>
                    <th>Project Title</th>
                    <th>Score Status</th>
                    <th style="width:120px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($submissions as $submission)
                    @php
                        $scoreCount = $submission->scores->count();
                        $criteriaCount = $submission->criteria_count;

                        if ($scoreCount === 0) {
                            $statusClass = 'badge-pending';
                            $statusLabel = 'Pending';
                        } elseif ($scoreCount >= $criteriaCount) {
                            $statusClass = 'badge-scored';
                            $statusLabel = 'Scored';
                        } else {
                            $statusClass = 'badge-partial';
                            $statusLabel = 'Partial';
                        }
                    @endphp
                    <tr>
                        <td>{{ $submission->team->name }}</td>
                        <td>
                            @if ($submission->team->segment)
                                <span class="segment-pill">{{ $submission->team->segment->name }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $submission->title }}</td>
                        <td><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                        <td>
                            @if ($scoreCount >= $criteriaCount && $scoreCount > 0)
                                <a href="{{ route('judge.score.create', $submission) }}" class="btn btn-secondary btn-sm">Edit Score</a>
                            @else
                                <a href="{{ route('judge.score.create', $submission) }}" class="btn btn-primary btn-sm">Score</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="ds-table-empty">No submissions assigned yet.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
