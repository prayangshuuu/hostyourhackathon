@extends('layouts.app')

@section('title', 'Judging Dashboard')

@section('content')
    <x-page-header title="Judging Dashboard" description="Evaluate submissions and track your scoring progress." :breadcrumbs="['Dashboard' => route('dashboard'), 'Judging' => null]">
        <x-slot:actions>
            <div class="flex items-center gap-1.5">
                @foreach ($assignedSegments->filter() as $segment)
                    <x-badge variant="indigo">{{ $segment->name }}</x-badge>
                @endforeach
                @if ($assignedSegments->filter()->isEmpty())
                    <x-badge variant="neutral">All Segments</x-badge>
                @endif
            </div>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-7">
        <x-stat-card icon="document-duplicate" :value="$totalAssigned" label="Total Assigned" />
        <x-stat-card icon="check-badge" :value="$scored" label="Scored" />
        <x-stat-card icon="clock" :value="$remaining + $partial" label="Remaining" />
    </div>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-5 h-[38px] text-2xs font-semibold text-slate-400 text-left uppercase tracking-[0.06em]">Team</th>
                        <th class="px-5 h-[38px] text-2xs font-semibold text-slate-400 text-left uppercase tracking-[0.06em]">Track</th>
                        <th class="px-5 h-[38px] text-2xs font-semibold text-slate-400 text-left uppercase tracking-[0.06em]">Project</th>
                        <th class="px-5 h-[38px] text-2xs font-semibold text-slate-400 text-left uppercase tracking-[0.06em]">Progress</th>
                        <th class="px-5 h-[38px]"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($submissions as $submission)
                        @php
                            $scoreCount = $submission->scores->count();
                            $criteriaCount = $submission->criteria_count;

                            if ($scoreCount === 0) {
                                $variant = 'neutral';
                                $statusLabel = 'Pending';
                            } elseif ($scoreCount >= $criteriaCount) {
                                $variant = 'success';
                                $statusLabel = 'Scored';
                            } else {
                                $variant = 'indigo';
                                $statusLabel = 'Partial';
                            }
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 h-[48px] text-sm font-semibold text-slate-900">{{ $submission->team->name }}</td>
                            <td class="px-5 h-[48px]">
                                @if ($submission->team->segment)
                                    <x-badge variant="indigo">{{ $submission->team->segment->name }}</x-badge>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="px-5 h-[48px] text-sm text-slate-600">{{ Str::limit($submission->title, 40) }}</td>
                            <td class="px-5 h-[48px]">
                                <x-badge :variant="$variant">{{ $statusLabel }}</x-badge>
                            </td>
                            <td class="px-5 h-[48px] text-right">
                                <x-button :href="route('judge.score.create', $submission)" :variant="$scoreCount >= $criteriaCount ? 'secondary' : 'primary'" size="sm">
                                    {{ $scoreCount >= $criteriaCount ? 'Edit Score' : 'Evaluate' }}
                                </x-button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-empty-state icon="document-text" title="No submissions assigned" description="Assignments will appear here once segments are defined and judges are linked." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
