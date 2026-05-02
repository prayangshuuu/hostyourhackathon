@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
    <x-page-header 
        title="Announcements" 
        :description="'Broadcast updates to participants of ' . $hackathon->title"
        :breadcrumbs="[
            'My Hackathons' => route('organizer.hackathons.index'), 
            $hackathon->title => route('organizer.hackathons.show', $hackathon),
            'Announcements' => null
        ]"
    >
        <x-slot:actions>
            <x-button :href="route('organizer.announcements.create', $hackathon)" variant="primary" icon="plus">New Announcement</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-5 h-[38px] text-2xs font-semibold text-slate-400 text-left uppercase tracking-[0.06em]">Title</th>
                        <th class="px-5 h-[38px] text-2xs font-semibold text-slate-400 text-left uppercase tracking-[0.06em]">Visibility</th>
                        <th class="px-5 h-[38px] text-2xs font-semibold text-slate-400 text-left uppercase tracking-[0.06em]">Status</th>
                        <th class="px-5 h-[38px] text-2xs font-semibold text-slate-400 text-left uppercase tracking-[0.06em]">Timeline</th>
                        <th class="px-5 h-[38px]"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($announcements as $announcement)
                        @php
                            $vis = $announcement->visibility instanceof \App\Enums\AnnouncementVisibility ? $announcement->visibility->value : $announcement->visibility;
                            $st = $announcement->status instanceof \App\Enums\AnnouncementStatus ? $announcement->status->value : $announcement->status;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 h-[48px] text-sm font-semibold text-slate-900">{{ $announcement->title }}</td>
                            <td class="px-5 h-[48px]">
                                @php
                                    $visVariant = match($vis) {
                                        'all' => 'neutral',
                                        'registered' => 'indigo',
                                        'segment' => 'amber',
                                        default => 'neutral',
                                    };
                                    $visText = match($vis) {
                                        'all' => 'All Participants',
                                        'registered' => 'Registered Teams',
                                        'segment' => 'Segment: ' . ($announcement->segment->name ?? 'Unknown'),
                                        default => ucfirst((string) $vis),
                                    };
                                @endphp
                                <x-badge :variant="$visVariant">{{ $visText }}</x-badge>
                            </td>
                            <td class="px-5 h-[48px]">
                                <x-badge :variant="match($st) { 'draft'=>'neutral', 'scheduled'=>'warning', 'published'=>'success', default=>'neutral' }">
                                    {{ ucfirst($st) }}
                                </x-badge>
                            </td>
                            <td class="px-5 h-[48px] text-xs text-slate-500">
                                @if($st === 'draft')
                                    <span class="text-slate-300">Not published</span>
                                @elseif($st === 'scheduled')
                                    {{ $announcement->scheduled_at?->format('M d, h:i A') ?? '—' }}
                                @else
                                    {{ $announcement->published_at ? $announcement->published_at->format('M d, h:i A') : '—' }}
                                @endif
                            </td>
                            <td class="px-5 h-[48px] text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($st !== 'published')
                                        <form method="POST" action="{{ route('organizer.announcements.publish', [$hackathon, $announcement]) }}">
                                            @csrf
                                            <x-button type="submit" size="sm" variant="primary">Publish</x-button>
                                        </form>
                                    @endif
                                    <x-button :href="route('organizer.announcements.edit', [$hackathon, $announcement])" variant="ghost" size="sm" icon="pencil-square"></x-button>
                                    <form method="POST" action="{{ route('organizer.announcements.destroy', [$hackathon, $announcement]) }}" onsubmit="return confirm('Delete this announcement?');">
                                        @csrf @method('DELETE')
                                        <x-button type="submit" variant="ghost" size="sm" class="text-red-600"><x-heroicon-o-trash class="w-4 h-4" /></x-button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-empty-state icon="megaphone" title="No announcements found" description="Create one to keep participants updated." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
