@extends('layouts.public')

@section('title', 'Leaderboard — ' . $hackathon->title)

@section('content')
    <div class="text-center py-12">
        <h1 class="text-3xl font-bold text-slate-900">Leaderboard</h1>
        <p class="text-slate-500 mt-2">{{ $hackathon->title }}</p>
    </div>

    @if (! $canView)
        <div class="max-w-xl mx-auto text-center">
            <x-card>
                <div class="py-12 px-6">
                    <x-heroicon-o-calendar class="w-12 h-12 text-slate-300 mx-auto mb-4" />
                    <h2 class="text-xl font-semibold text-slate-900 mb-2">Results are not yet public</h2>
                    <p class="text-slate-500">
                        Results will be announced on {{ $hackathon->results_at ? $hackathon->results_at->format('M d, Y h:i A') : 'a later date' }}.
                    </p>
                </div>
            </x-card>
        </div>
    @else
        <div class="max-w-5xl mx-auto pb-12">
            @if ($segments->count() > 0)
                <div class="mb-8 flex justify-center gap-2 flex-wrap">
                    <a href="{{ route('leaderboard.show', $hackathon) }}" 
                       class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors {{ !$currentSegment ? 'bg-accent-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                        Overall
                    </a>
                    @foreach ($segments as $s)
                        <a href="{{ route('leaderboard.show', [$hackathon, 'segment_id' => $s->id]) }}" 
                           class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors {{ $currentSegment?->id === $s->id ? 'bg-accent-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                            {{ $s->name }}
                        </a>
                    @endforeach
                </div>
            @endif

            <x-card no-padding>
                <x-table>
                    <thead>
                        <tr>
                            <th class="w-20 text-center">Rank</th>
                            <th>Team</th>
                            <th>Project</th>
                            <th>Segment</th>
                            <th class="text-right">Total Score</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($leaderboard as $index => $entry)
                            @php
                                $rank = $index + 1;
                                $rankClasses = match($rank) {
                                    1 => 'bg-amber-50 text-amber-700 font-bold',
                                    2 => 'bg-slate-50 text-slate-600 font-bold',
                                    3 => 'bg-orange-50 text-orange-700 font-bold',
                                    default => 'text-slate-400 font-medium'
                                };
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="text-center py-4">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full {{ $rankClasses }}">
                                        {{ $rank }}
                                    </span>
                                </td>
                                <td class="font-medium text-slate-900">{{ $entry->team->name ?? 'Unknown Team' }}</td>
                                <td class="text-slate-600">{{ $entry->project_title ?? 'Untitled' }}</td>
                                <td>
                                    <x-badge variant="neutral">{{ $entry->segment->name ?? 'General' }}</x-badge>
                                </td>
                                <td class="text-right font-bold text-accent-600 px-6">
                                    {{ number_format($entry->total_score, 1) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-12 text-slate-400">
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
