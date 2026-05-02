@extends('layouts.app')
@section('title', 'Dashboard')

@slot('slot')
<x-page-header title="Dashboard" description="Welcome back, {{ auth()->user()->name }}" />

@if(!$hasActiveHackathons)
  <div class="flex items-start gap-3 bg-white border border-l-4 border-l-accent-400 border-slate-200 rounded-xl p-5 mb-7">
    <x-heroicon-o-calendar-days class="w-5 h-5 text-accent-400 flex-shrink-0 mt-0.5" />
    <div>
      <p class="text-sm font-semibold text-slate-800">No Active Hackathons</p>
      <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">Registration is temporarily unavailable. Your past submissions and results are still accessible below.</p>
    </div>
  </div>
@endif

{{-- STATS ROW --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-7">
  <x-stat-card icon="trophy" value="{{ $hackathonCount }}" label="Active Hackathons" />
  <x-stat-card icon="users" value="{{ $teamStatus }}" label="My Team" />
  <x-stat-card icon="document-text" value="{{ $submissionStatus }}" label="Submission" />
</div>

{{-- MAIN CONTENT --}}
<div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-6">
  {{-- Left --}}
  <div class="space-y-6">
    <x-card title="My Hackathons" icon="trophy">
      @if($myHackathons->isEmpty())
        <x-empty-state icon="trophy" title="No hackathons yet" description="Browse available hackathons and create or join a team to get started.">
          <x-slot:action>
            <x-button href="{{ route($isSingleMode ? 'single.segments.index' : 'hackathons.index') }}" icon="arrow-right">
              {{ $isSingleMode ? 'Browse Segments' : 'Browse Hackathons' }}
            </x-button>
          </x-slot:action>
        </x-empty-state>
      @else
        <div class="-m-5">
          <div class="overflow-x-auto">
            <table class="w-full border-collapse">
              <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                  <th class="px-5 h-[38px] text-2xs font-semibold text-slate-400 text-left uppercase tracking-[0.06em]">Hackathon</th>
                  <th class="px-5 h-[38px] text-2xs font-semibold text-slate-400 text-left uppercase tracking-[0.06em]">Status</th>
                  <th class="px-5 h-[38px] text-2xs font-semibold text-slate-400 text-left uppercase tracking-[0.06em]">Team</th>
                  <th class="px-5 h-[38px] text-2xs font-semibold text-slate-400 text-left uppercase tracking-[0.06em]">Submission</th>
                  <th class="px-5 h-[38px]"></th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                @foreach($myHackathons as $item)
                  <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="px-5 h-[48px] text-sm font-medium text-slate-900">{{ $item->hackathon->title }}</td>
                    <td class="px-5 h-[48px]"><x-badge :variant="match($item->hackathon->status->value){'ongoing'=>'success','published'=>'indigo',default=>'neutral'}">{{ ucfirst($item->hackathon->status->value) }}</x-badge></td>
                    <td class="px-5 h-[48px] text-xs text-slate-600">{{ $item->name ?? '—' }}</td>
                    <td class="px-5 h-[48px]"><x-badge :variant="$item->submission_status === 'Submitted' ? 'success' : 'neutral'">{{ $item->submission_status ?? 'Not started' }}</x-badge></td>
                    <td class="px-5 h-[48px] text-right"><a href="{{ route('hackathons.show', $item->hackathon->slug) }}" class="text-2xs text-accent-600 font-semibold hover:underline">View →</a></td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      @endif
    </x-card>

    {{-- Recent Announcements --}}
    <x-card title="Recent Announcements" icon="megaphone">
      @if($announcements->isEmpty())
        <x-empty-state icon="megaphone" title="No announcements" description="Announcements from your hackathons will appear here." />
      @else
        <div class="space-y-3">
          @foreach($announcements as $ann)
            <a href="{{ route('announcements.index') }}" class="block bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-lg px-4 py-3.5 transition-colors">
              <div class="flex items-start justify-between gap-3">
                <p class="text-xs font-semibold text-slate-900 leading-snug">{{ $ann->title }}</p>
                <span class="text-2xs text-slate-400 whitespace-nowrap flex-shrink-0">{{ $ann->published_at?->diffForHumans() }}</span>
              </div>
              <p class="text-2xs text-slate-500 mt-1.5 line-clamp-2 leading-relaxed">{{ Str::limit(strip_tags($ann->body), 100) }}</p>
            </a>
          @endforeach
        </div>
      @endif
    </x-card>
  </div>

  {{-- Right sidebar --}}
  <div class="space-y-5">
    {{-- Upcoming Deadlines --}}
    <x-card title="Upcoming Deadlines" icon="clock">
      @if($deadlines->isEmpty())
        <x-empty-state icon="clock" title="No upcoming deadlines" />
      @else
        <div class="space-y-0 -mx-5">
          @foreach($deadlines as $d)
            <div class="flex items-center gap-3 px-5 py-3 border-b border-slate-100 last:border-0">
              <div class="w-2 h-2 rounded-full {{ $d['passed'] ? 'bg-slate-300' : 'bg-accent-500' }} flex-shrink-0"></div>
              <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-slate-800 truncate">{{ $d['label'] }}</p>
                <p class="text-2xs text-slate-400 mt-0.5">{{ $d['hackathon'] }}</p>
              </div>
              <span class="text-2xs {{ $d['passed'] ? 'text-slate-400' : 'text-accent-600 font-medium' }} whitespace-nowrap">{{ $d['date'] }}</span>
            </div>
          @endforeach
        </div>
      @endif
    </x-card>

    {{-- Quick Links --}}
    <x-card title="Quick Actions" icon="bolt">
      <div class="space-y-2">
        @if($hasActiveHackathons)
          <x-button href="{{ route($isSingleMode ? 'single.segments.index' : 'hackathons.index') }}" variant="secondary" fullWidth icon="magnifying-glass">
            {{ $isSingleMode ? 'Browse Segments' : 'Browse Hackathons' }}
          </x-button>
        @endif
        <x-button href="{{ route('profile.show') }}" variant="ghost" fullWidth icon="user-circle">My Profile</x-button>
      </div>
    </x-card>
  </div>
</div>
@endslot
