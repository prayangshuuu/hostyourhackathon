@extends('layouts.app')

@section('title', 'Judging Dashboard')

@section('content')
    <div class="page-header">
        <h1 class="text-page-title">Judging Dashboard</h1>
    </div>

    <x-alert />

    {{-- ROW 1 — Stats --}}
    <div class="stat-grid-3">
        <div class="stat-card">
            <div class="stat-label">Total Assigned</div>
            <div class="stat-value">{{ $totalAssigned ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Scored</div>
            <div class="stat-value">{{ $totalScored ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Remaining</div>
            <div class="stat-value">{{ $totalRemaining ?? 0 }}</div>
        </div>
    </div>

    {{-- Assigned segments row --}}
    @if(isset($assignedSegments) && count($assignedSegments) > 0)
    <div style="margin-top: 24px; margin-bottom: 24px;">
        <span style="font-size: 13px; color: var(--text-secondary); margin-right: 8px;">Your segments:</span>
        <div style="display: inline-flex; gap: 6px; align-items: center; flex-wrap: wrap;">
            @foreach($assignedSegments as $segment)
                <span style="background: var(--accent-light); color: var(--accent); border: 1px solid rgba(99,102,241,0.2); border-radius: 99px; padding: 3px 10px; font-size: 12px; font-weight: 500;">
                    {{ $segment->name ?? $segment }}
                </span>
            @endforeach
        </div>
    </div>
    @else
    <div style="height: 24px;"></div>
    @endif

    {{-- ROW 2 — Submissions table --}}
    <x-card title="Assigned Submissions">
        <x-table>
            <thead>
                <tr>
                    <th>Team</th>
                    <th>Project Title</th>
                    <th>Segment</th>
                    <th>Hackathon</th>
                    <th>Score Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($submissions ?? [] as $submission)
                    <tr>
                        <td>{{ $submission->team->name ?? 'N/A' }}</td>
                        <td>{{ $submission->project_title ?? 'N/A' }}</td>
                        <td>{{ $submission->segment->name ?? 'All Segments' }}</td>
                        <td>{{ $submission->hackathon->title ?? 'N/A' }}</td>
                        <td>
                            @php
                                $status = $submission->score_status ?? 'pending';
                                $variant = match($status) {
                                    'scored' => 'success',
                                    'partial' => 'neutral',
                                    default => 'warning',
                                };
                            @endphp
                            <x-badge :variant="$variant">{{ ucfirst($status) }}</x-badge>
                        </td>
                        <td>
                            @if($status === 'scored')
                                <a href="{{ route('judge.score.create', $submission->id) }}">
                                    <x-button variant="secondary" size="sm">Edit Score</x-button>
                                </a>
                            @else
                                <a href="{{ route('judge.score.create', $submission->id) }}">
                                    <x-button variant="primary" size="sm">Score Submission</x-button>
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 24px; color: var(--text-muted);">
                            No submissions assigned to you yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>
    </x-card>
@endsection
