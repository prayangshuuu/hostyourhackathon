@extends('layouts.app')

@section('title', 'Leaderboard')
@section('meta_description', 'Leaderboard for ' . $hackathon->title)

@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <span class="separator">/</span>
            <span>{{ $hackathon->title }}</span>
            <span class="separator">/</span>
            <span>Leaderboard</span>
        </div>
        <div class="page-header-row">
            <h1 class="text-page-title">Leaderboard</h1>
        </div>
    </div>

    @if (! $canView)
        {{-- Results not public --}}
        <div class="card results-pending-card">
            <svg class="results-pending-icon" viewBox="0 0 48 48" fill="none">
                <circle cx="24" cy="24" r="20" stroke="currentColor" stroke-width="2"/>
                <path d="M24 14v12M24 30v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <p class="results-pending-title">Results Not Available Yet</p>
            <p class="results-pending-text">
                @if ($hackathon->results_at)
                    Results will be published after {{ $hackathon->results_at->format('M d, Y · h:i A') }}.
                @else
                    The organizer has not published the results yet.
                @endif
            </p>
        </div>
    @else
        {{-- Leaderboard Table --}}
        <div class="ds-table-wrapper">
            <table class="ds-table" id="leaderboard-table">
                <thead>
                    <tr>
                        <th style="width:60px;">Rank</th>
                        <th>Team</th>
                        <th>Project Title</th>
                        <th>Segment</th>
                        <th style="width:120px; text-align:right;">Total Score</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($submissions as $index => $submission)
                        @php
                            $rank = $index + 1;
                            $rankClass = '';
                            if ($rank === 1) $rankClass = 'rank-1';
                            elseif ($rank === 2) $rankClass = 'rank-2';
                            elseif ($rank === 3) $rankClass = 'rank-3';
                        @endphp
                        <tr>
                            <td>
                                <span class="rank-badge {{ $rankClass }}">{{ $rank }}</span>
                            </td>
                            <td>{{ $submission->team->name }}</td>
                            <td>{{ $submission->title }}</td>
                            <td>
                                @if ($submission->team->segment)
                                    <span class="segment-pill">{{ $submission->team->segment->name }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td style="text-align:right; font-weight:var(--font-weight-semibold); color:var(--color-accent);">
                                {{ $submission->scores_sum_score ?? 0 }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="ds-table-empty">No scored submissions yet.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
@endsection
