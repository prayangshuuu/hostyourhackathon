@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
    <x-page-header 
        title="Announcements" 
        :description="'Latest updates from ' . $hackathon->title"
        :breadcrumbs="['Dashboard' => route('dashboard'), 'Announcements' => null]"
    />

    <div class="space-y-4">
        @forelse ($announcements as $announcement)
            <x-card>
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h2 class="text-base font-semibold text-slate-900 leading-snug">{{ $announcement->title }}</h2>
                            <x-badge :variant="match($announcement->visibility->value) { 'all'=>'success', 'registered'=>'indigo', 'segment'=>'violet', default=>'neutral' }">
                                {{ ucfirst($announcement->visibility->value) }}
                            </x-badge>
                        </div>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            {{ str()->limit(strip_tags($announcement->body), 200) }}
                        </p>
                        <div class="mt-4">
                            <x-button :href="route('participant.announcements.show', [$hackathon, $announcement])" variant="ghost" size="sm" iconRight="arrow-right">Read more</x-button>
                        </div>
                    </div>
                    <span class="text-xs text-slate-400 font-medium whitespace-nowrap pt-1">
                        {{ $announcement->published_at->format('M d, Y') }}
                    </span>
                </div>
            </x-card>
        @empty
            <x-empty-state icon="megaphone" title="No announcements yet" description="Stay tuned for updates from the hackathon organizers." />
        @endforelse
    </div>
@endsection
