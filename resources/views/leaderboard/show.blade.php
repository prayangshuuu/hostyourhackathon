@extends('layouts.public')

@section('title', 'Leaderboard - ' . $hackathon->title)

@section('content')
    <div class="page-header" style="text-align: center; margin-top: 40px; margin-bottom: 40px;">
        <h1 class="text-page-title">Leaderboard</h1>
        <p class="text-helper" style="font-size: 16px; margin-top: 8px;">{{ $hackathon->title }}</p>
    </div>

    @if(!$hackathon->leaderboard_public && !(Auth::check() && (Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('organizer'))))
        <div style="max-width: 600px; margin: 0 auto; text-align: center;">
            <x-card>
                <div style="padding: 40px 20px;">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-muted); margin-bottom: 16px; display: inline-block;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    <h2 class="text-card-title" style="margin-bottom: 8px;">Results are not yet public</h2>
                    <p class="text-helper" style="font-size: 15px;">
                        Results will be announced on {{ $hackathon->results_at ? $hackathon->results_at->format('M d, Y h:i A') : 'a later date' }}.
                    </p>
                </div>
            </x-card>
        </div>
    @else
        <div style="max-width: 1000px; margin: 0 auto;">
            <x-card>
                <x-table>
                    <thead>
                        <tr>
                            <th style="width: 80px; text-align: center;">Rank</th>
                            <th>Team</th>
                            <th>Project</th>
                            <th>Segment</th>
                            <th style="text-align: right;">Total Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaderboard ?? [] as $index => $entry)
                            @php
                                $rank = $index + 1;
                                $rankStyle = '';
                                if ($rank === 1) {
                                    $rankStyle = 'background: #fef9c3; color: #854d0e; font-weight: bold;';
                                } elseif ($rank === 2) {
                                    $rankStyle = 'background: #f1f5f9; color: #475569; font-weight: bold;';
                                } elseif ($rank === 3) {
                                    $rankStyle = 'background: #fff7ed; color: #9a3412; font-weight: bold;';
                                } else {
                                    $rankStyle = 'color: var(--text-muted);';
                                }
                            @endphp
                            <tr>
                                <td style="text-align: center; {{ $rankStyle }}">
                                    @if($rank <= 3)
                                        <div style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 50%; background: inherit;">
                                            {{ $rank }}
                                        </div>
                                    @else
                                        {{ $rank }}
                                    @endif
                                </td>
                                <td>{{ $entry->team->name ?? 'Unknown Team' }}</td>
                                <td>{{ $entry->project_title ?? 'Untitled' }}</td>
                                <td>
                                    <x-badge variant="neutral">{{ $entry->segment->name ?? 'General' }}</x-badge>
                                </td>
                                <td style="text-align: right; font-weight: 600; color: var(--accent);">
                                    {{ number_format($entry->total_score, 1) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 32px; color: var(--text-muted);">
                                    No scores available yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-table>
            </x-card>
        </div>
    @endif
@endsection
