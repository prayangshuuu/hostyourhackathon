@extends('layouts.app')

@section('title', 'Teams')

@section('content')
    <x-page-header title="Teams" description="Manage all registered teams across your hackathons." :breadcrumbs="['Dashboard' => route('dashboard'), 'Teams' => null]" />

    <x-card class="mb-6">
        <form method="GET" action="{{ route('organizer.teams.index') }}" class="flex items-end gap-4">
            <div class="flex-1">
                <x-input label="Filter by Hackathon" name="hackathon" type="select" onchange="this.form.submit()">
                    <option value="">All Hackathons</option>
                    @foreach ($hackathons as $h)
                        <option value="{{ $h->id }}" @selected(request('hackathon') == $h->id)>{{ $h->title }}</option>
                    @endforeach
                </x-input>
            </div>
        </form>
    </x-card>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-5 h-[38px] text-2xs font-semibold text-slate-400 text-left uppercase tracking-[0.06em]">Team</th>
                        <th class="px-5 h-[38px] text-2xs font-semibold text-slate-400 text-left uppercase tracking-[0.06em]">Hackathon</th>
                        <th class="px-5 h-[38px] text-2xs font-semibold text-slate-400 text-left uppercase tracking-[0.06em]">Members</th>
                        <th class="px-5 h-[38px]"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($teams as $team)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 h-[48px] text-sm font-semibold text-slate-900">{{ $team->name }}</td>
                            <td class="px-5 h-[48px] text-sm text-slate-600">{{ $team->hackathon->title }}</td>
                            <td class="px-5 h-[48px] text-sm text-slate-600">{{ $team->members_count }}</td>
                            <td class="px-5 h-[48px] text-right">
                                <x-button :href="route('organizer.teams.show', $team)" variant="ghost" size="sm">Manage</x-button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <x-empty-state icon="users" title="No teams found" description="Check back later once teams start registering." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($teams->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50">
                {{ $teams->links() }}
            </div>
        @endif
    </div>
@endsection
