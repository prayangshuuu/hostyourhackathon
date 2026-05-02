@extends('layouts.public')

@section('title', $hackathon->title)

@section('content')
    {{-- HERO SECTION --}}
    <div class="bg-white border-b border-slate-200">
        <div class="max-w-[1200px] mx-auto px-6 py-16 md:py-24 grid grid-cols-1 lg:grid-cols-[1fr_400px] gap-12 items-center">
            <div>
                <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 leading-[1.1] tracking-tight">
                    {{ $hackathon->title }}
                </h1>
                <p class="text-lg text-slate-500 mt-6 max-w-2xl leading-relaxed">
                    {{ $hackathon->tagline ?? 'Join the ultimate innovation challenge. Build, compete, and win amazing prizes.' }}
                </p>
                
                <div class="flex flex-wrap gap-3 mt-8">
                    @php
                        $regOpen = $hackathon->isRegistrationOpen();
                        $subOpen = $hackathon->isSubmissionOpen();
                    @endphp
                    
                    <x-badge :variant="$regOpen ? 'indigo' : 'neutral'" class="h-7 px-3">
                        <x-heroicon-o-calendar class="w-3.5 h-3.5" />
                        Registration: {{ $regOpen ? 'Open' : 'Closed' }}
                    </x-badge>
                    
                    <x-badge :variant="$subOpen ? 'success' : 'neutral'" class="h-7 px-3">
                        <x-heroicon-o-clock class="w-3.5 h-3.5" />
                        Submissions: {{ $subOpen ? 'Open' : 'Closed' }}
                    </x-badge>
                </div>

                <div class="flex flex-wrap items-center gap-4 mt-10">
                    @auth
                        @if(auth()->user()->hasTeamInHackathon($hackathon))
                            <x-button :href="route('single.teams.my')" variant="primary" size="lg" icon="users">Go to My Team</x-button>
                        @else
                            <x-button :href="route('single.teams.create')" variant="primary" size="lg" icon="plus">Create or Join Team</x-button>
                        @endif
                    @else
                        @if($regOpen)
                            <x-button :href="route('register')" variant="primary" size="lg">Register for Hackathon</x-button>
                        @endif
                        <x-button :href="route('login')" variant="secondary" size="lg">Sign in</x-button>
                    @endauth
                </div>
            </div>

            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-8">
                <div class="space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Participants</span>
                        <span class="text-2xl font-bold text-slate-900">{{ $hackathon->teams_count ?? $hackathon->teams()->count() }} Teams</span>
                    </div>
                    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Submissions</span>
                        <span class="text-2xl font-bold text-slate-900">{{ $hackathon->submissions_count ?? $hackathon->submissions()->count() }} Projects</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Judges</span>
                        <span class="text-2xl font-bold text-slate-900">{{ $hackathon->judges()->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SEGMENTS SECTION --}}
    <div class="max-w-[1200px] mx-auto px-6 py-16 md:py-24">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-slate-900 tracking-tight">Choose Your Track</h2>
            <p class="text-base text-slate-500 mt-4 max-w-2xl mx-auto">
                Select a track that aligns with your project goals. Each segment has dedicated prizes and rules.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach($segments as $segment)
                <div class="group relative flex flex-col bg-white border border-slate-200 rounded-2xl overflow-hidden hover:border-accent-400 hover:shadow-xl hover:shadow-accent-500/5 transition-all duration-300">
                    <div class="h-1.5 bg-accent-500"></div>
                    <div class="p-8 flex-1">
                        <div class="flex items-start justify-between gap-4 mb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-accent-50 flex items-center justify-center text-accent-600">
                                    <x-heroicon-o-puzzle-piece class="w-6 h-6" />
                                </div>
                                <h3 class="text-xl font-bold text-slate-900">{{ $segment->name }}</h3>
                            </div>
                            <x-badge :variant="$segment->isFull() ? 'danger' : 'success'">
                                {{ $segment->isFull() ? 'Full' : 'Open' }}
                            </x-badge>
                        </div>

                        <p class="text-sm text-slate-500 leading-relaxed line-clamp-3">
                            {{ $segment->description }}
                        </p>

                        @if($segment->prizes->count() > 0)
                            <div class="mt-8 pt-6 border-t border-slate-50">
                                <p class="text-2xs font-bold text-slate-400 uppercase tracking-widest mb-4">Track Prizes</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($segment->prizes->take(3) as $prize)
                                        <x-badge variant="amber" class="h-7 px-3">
                                            <x-heroicon-o-trophy class="w-3.5 h-3.5 text-amber-500" />
                                            {{ $prize->title }}: {{ $prize->amount }}
                                        </x-badge>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <a href="{{ route('single.segments.show', $segment) }}" class="flex items-center justify-between p-5 bg-slate-50 border-t border-slate-100 group-hover:bg-accent-50/50 transition-colors">
                        <div class="flex items-center gap-4 text-xs font-medium text-slate-400">
                            <span>{{ $segment->teams_count }} Teams</span>
                            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                            <span>{{ $segment->submissions_count }} Submissions</span>
                        </div>
                        <span class="text-sm font-bold text-accent-600 group-hover:translate-x-1 transition-transform">
                            View Details →
                        </span>
                    </a>

                    @if($segment->isFull())
                        <div class="absolute inset-0 bg-white/60 backdrop-blur-[1px] flex items-center justify-center z-10 pointer-events-none">
                            <div class="bg-red-50 text-red-700 border border-red-100 rounded-lg px-4 py-2 font-bold text-sm shadow-sm">
                                Track Full
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endsection
