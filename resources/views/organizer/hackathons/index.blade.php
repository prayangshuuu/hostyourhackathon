@extends('layouts.app')

@section('title', 'My Hackathons')

@section('content')
    <x-page-header title="My Hackathons" description="Manage your hosted events and tracks." :breadcrumbs="['Dashboard' => route('dashboard'), 'My Hackathons' => null]">
        <x-slot:actions>
            <x-button :href="route('organizer.hackathons.create')" variant="primary" icon="plus">Create Hackathon</x-button>
        </x-slot:actions>
    </x-page-header>

    @if (!$hasActiveHackathonsInSystem)
        <x-alert type="info">
            <strong>You have no active hackathons.</strong> Published and ongoing hackathons will appear here for quick access.
        </x-alert>
    @endif

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        @if ($hackathons->isEmpty())
            <x-empty-state icon="calendar" title="No hackathons yet" description="Start by creating your first hackathon event.">
                <x-slot:action>
                    <x-button :href="route('organizer.hackathons.create')" variant="primary">Create Now</x-button>
                </x-slot:action>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-5 h-[38px] text-2xs font-semibold text-slate-400 text-left uppercase tracking-[0.06em]">Hackathon</th>
                            <th class="px-5 h-[38px] text-2xs font-semibold text-slate-400 text-left uppercase tracking-[0.06em]">Status</th>
                            <th class="px-5 h-[38px] text-2xs font-semibold text-slate-400 text-left uppercase tracking-[0.06em]">Teams</th>
                            <th class="px-5 h-[38px] text-2xs font-semibold text-slate-400 text-left uppercase tracking-[0.06em]">Projects</th>
                            <th class="px-5 h-[38px]"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($hackathons as $hackathon)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 h-[48px]">
                                    <div class="flex items-center gap-3">
                                        @if ($hackathon->logo)
                                            <img src="{{ Storage::url($hackathon->logo) }}" alt="" class="w-7 h-7 rounded bg-slate-50 border border-slate-100 object-cover">
                                        @else
                                            <div class="w-7 h-7 rounded bg-slate-50 border border-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-400">
                                                {{ strtoupper(substr($hackathon->title, 0, 1)) }}
                                            </div>
                                        @endif
                                        <span class="text-sm font-semibold text-slate-900">{{ $hackathon->title }}</span>
                                    </div>
                                </td>
                                <td class="px-5 h-[48px]">
                                    <x-badge :variant="match($hackathon->status->value) { 'ongoing'=>'success', 'published'=>'indigo', default=>'neutral' }">
                                        {{ ucfirst($hackathon->status->value) }}
                                    </x-badge>
                                </td>
                                <td class="px-5 h-[48px] text-sm text-slate-600">{{ $hackathon->teams_count }}</td>
                                <td class="px-5 h-[48px] text-sm text-slate-600">{{ $hackathon->submissions_count }}</td>
                                <td class="px-5 h-[48px] text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <x-button :href="route('organizer.hackathons.show', $hackathon)" size="sm" variant="ghost">Manage</x-button>
                                        <x-button :href="route('organizer.hackathons.edit', $hackathon)" size="sm" variant="ghost" icon="pencil-square"></x-button>
                                        <form method="POST" action="{{ route('organizer.hackathons.destroy', $hackathon) }}" onsubmit="return confirm('Delete this hackathon?')" class="inline">
                                            @csrf @method('DELETE')
                                            <x-button type="submit" size="sm" variant="ghost" class="text-red-600" icon="trash"></x-button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
