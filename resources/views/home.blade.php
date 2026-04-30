@extends('layouts.public')

@section('title', 'The Hackathon Platform You Actually Own')

@section('content')
<!-- Section 1: HERO -->
<section class="pt-20 pb-[72px] text-center max-w-[720px] mx-auto px-4">
    <div class="inline-flex items-center bg-accent-light text-accent border border-indigo-500/20 rounded-full px-3 py-1 text-xs font-semibold mb-5">
        <x-heroicon-o-bolt class="w-3.5 h-3.5 mr-1.5" />
        Open Source Hackathon Platform
    </div>
    
    <h1 class="text-[40px] font-bold text-text-primary leading-[1.15] tracking-[-0.02em]">
        The Hackathon Platform You Actually Own
    </h1>
    
    <p class="text-[17px] text-text-secondary leading-[1.65] max-w-[560px] mx-auto mt-4">
        Run university hackathons with full control. Team registration, idea submissions, 
        judging, and announcements — all in one self-hosted platform.
    </p>
    
    <div class="mt-8 flex justify-center gap-3">
        <x-button href="{{ route('hackathons.index') }}" variant="primary" size="lg">
            Browse Hackathons
            <x-heroicon-m-arrow-right class="w-5 h-5 ml-2" />
        </x-button>
        <x-button href="https://github.com/prayangshuuu/hostyourhackathon" variant="secondary" size="lg" target="_blank">
            <x-heroicon-o-code-bracket class="w-5 h-5 mr-2" />
            View on GitHub
        </x-button>
    </div>
    
    <div class="mt-10 flex flex-wrap justify-center items-center gap-6">
        <div class="text-[13px] text-text-muted font-medium flex items-center">
            <x-heroicon-o-users class="w-4 h-4 mr-1.5 inline" />
            {{ number_format($stats['participants']) }} participants
        </div>
        <div class="w-1 h-1 rounded-full bg-border"></div>
        <div class="text-[13px] text-text-muted font-medium flex items-center">
            <x-heroicon-o-trophy class="w-4 h-4 mr-1.5 inline" />
            {{ number_format($stats['hackathons']) }} hackathons hosted
        </div>
        <div class="w-1 h-1 rounded-full bg-border"></div>
        <div class="text-[13px] text-text-muted font-medium flex items-center">
            <x-heroicon-o-document-text class="w-4 h-4 mr-1.5 inline" />
            {{ number_format($stats['submissions']) }} submissions
        </div>
    </div>
</section>

<!-- Section 2: STATS BAR -->
<section class="w-full bg-surface border-y border-border py-7">
    <div class="max-w-[900px] mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-border">
            <div class="text-center py-4 md:py-0">
                <div class="text-[28px] font-bold text-text-primary">{{ number_format($stats['hackathons']) }}</div>
                <div class="text-[13px] text-text-muted mt-1">Hackathons Hosted</div>
            </div>
            <div class="text-center py-4 md:py-0">
                <div class="text-[28px] font-bold text-text-primary">{{ number_format($stats['teams']) }}</div>
                <div class="text-[13px] text-text-muted mt-1">Teams Registered</div>
            </div>
            <div class="text-center py-4 md:py-0">
                <div class="text-[28px] font-bold text-text-primary">{{ number_format($stats['submissions']) }}</div>
                <div class="text-[13px] text-text-muted mt-1">Ideas Submitted</div>
            </div>
            <div class="text-center py-4 md:py-0">
                <div class="text-[28px] font-bold text-text-primary">{{ number_format($stats['participants']) }}</div>
                <div class="text-[13px] text-text-muted mt-1">Participants</div>
            </div>
        </div>
    </div>
</section>

<!-- Section 3: ACTIVE HACKATHONS -->
<section class="py-[72px] px-4 max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-[22px] font-semibold text-text-primary">Active Hackathons</h2>
        <a href="{{ route('hackathons.index') }}" class="text-sm font-medium text-accent hover:underline">View all &rarr;</a>
    </div>

    @if($activeHackathons->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($activeHackathons as $hackathon)
                <x-hackathon-card :hackathon="$hackathon" />
            @endforeach
        </div>
    @else
        <x-empty-state 
            icon="calendar" 
            title="No Active Hackathons"
            description="No hackathons are currently running. Check back soon or view past events.">
            <x-slot:action>
                <x-button href="{{ route('hackathons.index') }}" variant="secondary">View Past Hackathons</x-button>
            </x-slot:action>
        </x-empty-state>
    @endif
</section>

<!-- Section 4: FEATURES -->
<section class="py-[72px] px-4 max-w-7xl mx-auto">
    <div class="text-center mb-12">
        <div class="inline-flex items-center bg-accent-light text-accent border border-indigo-500/20 rounded-full px-3 py-1 text-xs font-semibold">
            Everything You Need
        </div>
        <h2 class="text-[28px] font-bold text-text-primary mt-3">Built for organizers, participants, and judges</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-12">
        <!-- Feature 1 -->
        <div class="bg-surface border border-border rounded-lg p-6">
            <div class="w-11 h-11 bg-accent-light rounded-md flex items-center justify-center mb-4 text-accent">
                <x-heroicon-o-users class="w-[22px] h-[22px]" />
            </div>
            <h3 class="text-[15px] font-semibold text-text-primary mb-2">Team Management</h3>
            <p class="text-sm text-text-secondary leading-[1.6]">Invite-based team formation with size limits, solo participation, and leader controls</p>
        </div>
        
        <!-- Feature 2 -->
        <div class="bg-surface border border-border rounded-lg p-6">
            <div class="w-11 h-11 bg-accent-light rounded-md flex items-center justify-center mb-4 text-accent">
                <x-heroicon-o-document-text class="w-[22px] h-[22px]" />
            </div>
            <h3 class="text-[15px] font-semibold text-text-primary mb-2">Idea Submissions</h3>
            <p class="text-sm text-text-secondary leading-[1.6]">Draft and finalize project submissions with file uploads and a live countdown timer</p>
        </div>
        
        <!-- Feature 3 -->
        <div class="bg-surface border border-border rounded-lg p-6">
            <div class="w-11 h-11 bg-accent-light rounded-md flex items-center justify-center mb-4 text-accent">
                <x-heroicon-o-trophy class="w-[22px] h-[22px]" />
            </div>
            <h3 class="text-[15px] font-semibold text-text-primary mb-2">Judging & Scoring</h3>
            <p class="text-sm text-text-secondary leading-[1.6]">Custom scoring rubrics, per-segment judge assignment, and a controlled leaderboard</p>
        </div>
        
        <!-- Feature 4 -->
        <div class="bg-surface border border-border rounded-lg p-6">
            <div class="w-11 h-11 bg-accent-light rounded-md flex items-center justify-center mb-4 text-accent">
                <x-heroicon-o-megaphone class="w-[22px] h-[22px]" />
            </div>
            <h3 class="text-[15px] font-semibold text-text-primary mb-2">Announcements</h3>
            <p class="text-sm text-text-secondary leading-[1.6]">Reach all participants or specific segments with scheduled announcements and email delivery</p>
        </div>
        
        <!-- Feature 5 -->
        <div class="bg-surface border border-border rounded-lg p-6">
            <div class="w-11 h-11 bg-accent-light rounded-md flex items-center justify-center mb-4 text-accent">
                <x-heroicon-o-puzzle-piece class="w-[22px] h-[22px]" />
            </div>
            <h3 class="text-[15px] font-semibold text-text-primary mb-2">Segments & Tracks</h3>
            <p class="text-sm text-text-secondary leading-[1.6]">Organize your hackathon into multiple tracks, each with its own rulebook and judges</p>
        </div>
        
        <!-- Feature 6 -->
        <div class="bg-surface border border-border rounded-lg p-6">
            <div class="w-11 h-11 bg-accent-light rounded-md flex items-center justify-center mb-4 text-accent">
                <x-heroicon-o-shield-check class="w-[22px] h-[22px]" />
            </div>
            <h3 class="text-[15px] font-semibold text-text-primary mb-2">Role-Based Access</h3>
            <p class="text-sm text-text-secondary leading-[1.6]">Five roles — admin, organizer, participant, judge, mentor — each with precise permissions</p>
        </div>
        
        <!-- Feature 7 -->
        <div class="bg-surface border border-border rounded-lg p-6">
            <div class="w-11 h-11 bg-accent-light rounded-md flex items-center justify-center mb-4 text-accent">
                <x-heroicon-o-wrench-screwdriver class="w-[22px] h-[22px]" />
            </div>
            <h3 class="text-[15px] font-semibold text-text-primary mb-2">Self-Hosted</h3>
            <p class="text-sm text-text-secondary leading-[1.6]">Runs on shared hosting with PHP 8.1+, MySQL, and Composer — no Docker, no VPS required</p>
        </div>
        
        <!-- Feature 8 -->
        <div class="bg-surface border border-border rounded-lg p-6">
            <div class="w-11 h-11 bg-accent-light rounded-md flex items-center justify-center mb-4 text-accent">
                <x-heroicon-o-code-bracket class="w-[22px] h-[22px]" />
            </div>
            <h3 class="text-[15px] font-semibold text-text-primary mb-2">REST API</h3>
            <p class="text-sm text-text-secondary leading-[1.6]">Full API with Sanctum authentication and OpenAPI documentation for custom integrations</p>
        </div>
        
        <!-- Feature 9 -->
        <div class="bg-surface border border-border rounded-lg p-6">
            <div class="w-11 h-11 bg-accent-light rounded-md flex items-center justify-center mb-4 text-accent">
                <x-heroicon-o-cog-6-tooth class="w-[22px] h-[22px]" />
            </div>
            <h3 class="text-[15px] font-semibold text-text-primary mb-2">Admin Panel</h3>
            <p class="text-sm text-text-secondary leading-[1.6]">Manage users, settings, feature flags, and SMTP configuration from a single dashboard</p>
        </div>
    </div>
</section>

<!-- Section 5: HOW IT WORKS -->
<section class="py-[72px] bg-surface border-y border-border px-4">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-[22px] font-semibold text-text-primary text-center mb-12">Up and running in minutes</h2>
        
        <div class="max-w-[800px] mx-auto flex flex-col md:flex-row gap-8 items-center md:items-start relative mt-12">
            <!-- Step 1 -->
            <div class="flex-1 text-center relative z-10">
                <div class="w-12 h-12 rounded-full bg-accent-light text-accent text-[20px] font-bold flex items-center justify-center mx-auto mb-4">1</div>
                <h3 class="text-[15px] font-semibold text-text-primary">Install</h3>
                <p class="text-sm text-text-secondary leading-[1.6] max-w-[240px] mx-auto mt-2">Upload files, visit /install, and follow the 5-step wizard. No CLI needed for shared hosting.</p>
            </div>
            
            <x-heroicon-m-arrow-right class="hidden md:block w-5 h-5 text-text-muted mt-6" />
            
            <!-- Step 2 -->
            <div class="flex-1 text-center relative z-10">
                <div class="w-12 h-12 rounded-full bg-accent-light text-accent text-[20px] font-bold flex items-center justify-center mx-auto mb-4">2</div>
                <h3 class="text-[15px] font-semibold text-text-primary">Configure</h3>
                <p class="text-sm text-text-secondary leading-[1.6] max-w-[240px] mx-auto mt-2">Set your hackathon details, add segments, invite organizers, and publish when ready.</p>
            </div>
            
            <x-heroicon-m-arrow-right class="hidden md:block w-5 h-5 text-text-muted mt-6" />
            
            <!-- Step 3 -->
            <div class="flex-1 text-center relative z-10">
                <div class="w-12 h-12 rounded-full bg-accent-light text-accent text-[20px] font-bold flex items-center justify-center mx-auto mb-4">3</div>
                <h3 class="text-[15px] font-semibold text-text-primary">Run</h3>
                <p class="text-sm text-text-secondary leading-[1.6] max-w-[240px] mx-auto mt-2">Participants register, form teams, submit ideas, and judges score — all managed from your dashboard.</p>
            </div>
        </div>
    </div>
</section>

<!-- Section 6: FOR TEAMS -->
<section class="py-[72px] px-4 max-w-7xl mx-auto">
    <h2 class="text-[22px] font-semibold text-text-primary text-center mb-12">Built for every role</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-12">
        <!-- Role 1 -->
        <div class="bg-surface border border-border rounded-lg p-5">
            <x-badge variant="primary" class="mb-3">Organizer</x-badge>
            <h3 class="text-[15px] font-semibold text-text-primary mt-2.5 mb-3">Organizer</h3>
            <ul class="space-y-2">
                <li class="flex items-start text-[13px] text-text-secondary leading-[1.7]">
                    <x-heroicon-s-check-circle class="w-3.5 h-3.5 text-success mr-2 mt-1 shrink-0" />
                    <span>Create and manage hackathons</span>
                </li>
                <li class="flex items-start text-[13px] text-text-secondary leading-[1.7]">
                    <x-heroicon-s-check-circle class="w-3.5 h-3.5 text-success mr-2 mt-1 shrink-0" />
                    <span>Set timelines and segments</span>
                </li>
                <li class="flex items-start text-[13px] text-text-secondary leading-[1.7]">
                    <x-heroicon-s-check-circle class="w-3.5 h-3.5 text-success mr-2 mt-1 shrink-0" />
                    <span>Manage teams and submissions</span>
                </li>
                <li class="flex items-start text-[13px] text-text-secondary leading-[1.7]">
                    <x-heroicon-s-check-circle class="w-3.5 h-3.5 text-success mr-2 mt-1 shrink-0" />
                    <span>Assign judges and view scores</span>
                </li>
            </ul>
        </div>
        
        <!-- Role 2 -->
        <div class="bg-surface border border-border rounded-lg p-5">
            <x-badge variant="info" class="mb-3">Participant</x-badge>
            <h3 class="text-[15px] font-semibold text-text-primary mt-2.5 mb-3">Participant</h3>
            <ul class="space-y-2">
                <li class="flex items-start text-[13px] text-text-secondary leading-[1.7]">
                    <x-heroicon-s-check-circle class="w-3.5 h-3.5 text-success mr-2 mt-1 shrink-0" />
                    <span>Browse and register for hackathons</span>
                </li>
                <li class="flex items-start text-[13px] text-text-secondary leading-[1.7]">
                    <x-heroicon-s-check-circle class="w-3.5 h-3.5 text-success mr-2 mt-1 shrink-0" />
                    <span>Form or join a team</span>
                </li>
                <li class="flex items-start text-[13px] text-text-secondary leading-[1.7]">
                    <x-heroicon-s-check-circle class="w-3.5 h-3.5 text-success mr-2 mt-1 shrink-0" />
                    <span>Submit and finalize your idea</span>
                </li>
                <li class="flex items-start text-[13px] text-text-secondary leading-[1.7]">
                    <x-heroicon-s-check-circle class="w-3.5 h-3.5 text-success mr-2 mt-1 shrink-0" />
                    <span>View results and leaderboard</span>
                </li>
            </ul>
        </div>
        
        <!-- Role 3 -->
        <div class="bg-surface border border-border rounded-lg p-5">
            <x-badge variant="warning" class="mb-3">Judge</x-badge>
            <h3 class="text-[15px] font-semibold text-text-primary mt-2.5 mb-3">Judge</h3>
            <ul class="space-y-2">
                <li class="flex items-start text-[13px] text-text-secondary leading-[1.7]">
                    <x-heroicon-s-check-circle class="w-3.5 h-3.5 text-success mr-2 mt-1 shrink-0" />
                    <span>View assigned submissions</span>
                </li>
                <li class="flex items-start text-[13px] text-text-secondary leading-[1.7]">
                    <x-heroicon-s-check-circle class="w-3.5 h-3.5 text-success mr-2 mt-1 shrink-0" />
                    <span>Score against custom criteria</span>
                </li>
                <li class="flex items-start text-[13px] text-text-secondary leading-[1.7]">
                    <x-heroicon-s-check-circle class="w-3.5 h-3.5 text-success mr-2 mt-1 shrink-0" />
                    <span>Add remarks per criterion</span>
                </li>
                <li class="flex items-start text-[13px] text-text-secondary leading-[1.7]">
                    <x-heroicon-s-check-circle class="w-3.5 h-3.5 text-success mr-2 mt-1 shrink-0" />
                    <span>See live total calculation</span>
                </li>
            </ul>
        </div>
        
        <!-- Role 4 -->
        <div class="bg-surface border border-border rounded-lg p-5">
            <x-badge variant="danger" class="mb-3">Super Admin</x-badge>
            <h3 class="text-[15px] font-semibold text-text-primary mt-2.5 mb-3">Super Admin</h3>
            <ul class="space-y-2">
                <li class="flex items-start text-[13px] text-text-secondary leading-[1.7]">
                    <x-heroicon-s-check-circle class="w-3.5 h-3.5 text-success mr-2 mt-1 shrink-0" />
                    <span>Full platform control</span>
                </li>
                <li class="flex items-start text-[13px] text-text-secondary leading-[1.7]">
                    <x-heroicon-s-check-circle class="w-3.5 h-3.5 text-success mr-2 mt-1 shrink-0" />
                    <span>User and role management</span>
                </li>
                <li class="flex items-start text-[13px] text-text-secondary leading-[1.7]">
                    <x-heroicon-s-check-circle class="w-3.5 h-3.5 text-success mr-2 mt-1 shrink-0" />
                    <span>System settings and feature flags</span>
                </li>
                <li class="flex items-start text-[13px] text-text-secondary leading-[1.7]">
                    <x-heroicon-s-check-circle class="w-3.5 h-3.5 text-success mr-2 mt-1 shrink-0" />
                    <span>Impersonate any user</span>
                </li>
            </ul>
        </div>
    </div>
</section>

<!-- Section 7: OPEN SOURCE CTA -->
<section class="py-[72px] px-4">
    <div class="bg-surface border border-border rounded-xl p-12 max-w-[640px] mx-auto text-center">
        <x-heroicon-o-code-bracket-square class="w-10 h-10 text-accent mx-auto mb-4" />
        <h2 class="text-[22px] font-semibold text-text-primary">Free and Open Source</h2>
        <p class="text-[15px] text-text-secondary mt-2 leading-[1.65]">
            HostYourHackathon is MIT licensed. Fork it, customize it, make it yours. Contributions welcome.
        </p>
        <div class="mt-7 flex flex-col sm:flex-row justify-center gap-3">
            <x-button href="https://github.com/prayangshuuu/hostyourhackathon" variant="secondary" size="lg" target="_blank">
                <x-heroicon-s-star class="w-5 h-5 mr-2 text-[#E3B341]" />
                Star on GitHub
            </x-button>
            <x-button href="{{ route('hackathons.index') }}" variant="primary" size="lg">
                Get Started
                <x-heroicon-m-arrow-right class="w-5 h-5 ml-2" />
            </x-button>
        </div>
    </div>
</section>

@include('partials.footer')
@endsection
