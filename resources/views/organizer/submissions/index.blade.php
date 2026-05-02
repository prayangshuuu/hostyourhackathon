@extends('layouts.app')

@section('title', 'Submissions')

@section('content')
    <x-page-header title="Submissions" description="Review projects and track submission statuses." :breadcrumbs="['Dashboard' => route('dashboard'), 'Submissions' => null]" />

    <x-card class="mb-6">
        <form method="GET" action="{{ route('organizer.submissions.index') }}">
            <x-input label="Filter by Hackathon" name="hackathon" type="select" onchange="this.form.submit()">
                <option value="">All Hackathons</option>
                @foreach ($hackathons as $h)
                    <option value="{{ $h->id }}" @selected(request('hackathon') == $h->id)>{{ $h->title }}</option>
                @endforeach
            </x-input>
        </form>
    </x-card>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-5 h-[38px] text-2xs font-semibold text-slate-400 text-left uppercase tracking-[0.06em]">Project Title</th>
                        <th class="px-5 h-[38px] text-2xs font-semibold text-slate-400 text-left uppercase tracking-[0.06em]">Team</th>
                        <th class="px-5 h-[38px] text-2xs font-semibold text-slate-400 text-left uppercase tracking-[0.06em]">Hackathon</th>
                        <th class="px-5 h-[38px]"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($submissions as $submission)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 h-[48px] text-sm font-semibold text-slate-900">{{ Str::limit($submission->title, 50) }}</td>
                            <td class="px-5 h-[48px] text-sm text-slate-600">{{ $submission->team->name }}</td>
                            <td class="px-5 h-[48px] text-sm text-slate-600">{{ $submission->hackathon->title }}</td>
                            <td class="px-5 h-[48px] text-right">
                                <x-button :href="route('organizer.submissions.show', $submission)" variant="ghost" size="sm">View Details</x-button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <x-empty-state icon="document-text" title="No submissions yet" description="Projects will appear here once teams start submitting." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($submissions->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50">
                {{ $submissions->links() }}
            </div>
        @endif
    </div>
@endsection
