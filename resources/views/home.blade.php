@extends('layouts.public')

@section('title', 'The Hackathon Platform You Actually Own')

@section('content')
{{-- HERO --}}
<section style="padding: 80px 24px; text-align: center; max-width: 800px; margin: 0 auto;">
    <div class="badge badge-indigo" style="margin-bottom: 24px; height: 28px; padding: 0 12px;">
        <x-heroicon-o-bolt style="width: 14px; height: 14px; margin-right: 8px;" />
        Open Source Hackathon Platform
    </div>
    
    <h1 style="font-size: 48px; font-weight: 800; color: var(--text-primary); line-height: 1.1; letter-spacing: -0.03em;">
        The Hackathon Platform You Actually Own
    </h1>
    
    <p style="font-size: 18px; color: var(--text-secondary); margin-top: 20px; line-height: 1.65; max-width: 600px; margin-left: auto; margin-right: auto;">
        Run university hackathons with full control. Team registration, idea submissions, 
        judging, and announcements — all in one self-hosted platform.
    </p>
    
    <div style="margin-top: 40px; display: flex; justify-content: center; gap: 16px;">
        @if($isSingleMode && $singleHackathon)
            <x-button href="{{ route('single.segments.index') }}" variant="primary" size="lg" iconRight="arrow-right">
                Explore Hackathon
            </x-button>
        @else
            <x-button href="{{ route('hackathons.index') }}" variant="primary" size="lg" iconRight="arrow-right">
                Browse Hackathons
            </x-button>
        @endif
        <x-button href="https://github.com/prayangshuuu/hostyourhackathon" variant="secondary" size="lg" target="_blank" icon="code-bracket">
            View on GitHub
        </x-button>
    </div>
    
    <div style="margin-top: 48px; display: flex; justify-content: center; align-items: center; gap: 24px;">
        <div style="font-size: 13px; color: var(--text-muted); font-weight: 500; display: flex; align-items: center; gap: 6px;">
            <x-heroicon-o-users style="width: 16px; height: 16px;" />
            {{ number_format($stats['participants']) }} participants
        </div>
        <div style="width: 4px; height: 4px; border-radius: 99px; background: var(--border);"></div>
        <div style="font-size: 13px; color: var(--text-muted); font-weight: 500; display: flex; align-items: center; gap: 6px;">
            <x-heroicon-o-trophy style="width: 16px; height: 16px;" />
            {{ number_format($stats['hackathons']) }} hackathons
        </div>
    </div>
</section>

{{-- STATS BAR --}}
<section style="background: var(--surface); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); padding: 48px 24px;">
    <div style="max-width: 1000px; margin: 0 auto;">
        <div class="grid-4" style="text-align: center;">
            <div>
                <div style="font-size: 32px; font-weight: 800; color: var(--text-primary);">{{ number_format($stats['hackathons']) }}</div>
                <div style="font-size: 13px; color: var(--text-muted); margin-top: 4px; font-weight: 500;">Events Hosted</div>
            </div>
            <div>
                <div style="font-size: 32px; font-weight: 800; color: var(--text-primary);">{{ number_format($stats['teams']) }}</div>
                <div style="font-size: 13px; color: var(--text-muted); margin-top: 4px; font-weight: 500;">Teams Formed</div>
            </div>
            <div>
                <div style="font-size: 32px; font-weight: 800; color: var(--text-primary);">{{ number_format($stats['submissions']) }}</div>
                <div style="font-size: 13px; color: var(--text-muted); margin-top: 4px; font-weight: 500;">Ideas Submitted</div>
            </div>
            <div>
                <div style="font-size: 32px; font-weight: 800; color: var(--text-primary);">{{ number_format($stats['participants']) }}</div>
                <div style="font-size: 13px; color: var(--text-muted); margin-top: 4px; font-weight: 500;">Active Builders</div>
            </div>
        </div>
    </div>
</section>

{{-- ACTIVE EVENTS --}}
@if(!$isSingleMode)
<section style="padding: 80px 24px; max-width: 1200px; margin: 0 auto;">
    <div class="split" style="margin-bottom: 32px;">
        <h2 style="font-size: 24px; font-weight: 700; color: var(--text-primary);">Featured Hackathons</h2>
        <a href="{{ route('hackathons.index') }}" style="font-size: 14px; font-weight: 600; color: var(--accent);">View all &rarr;</a>
    </div>

    @if($activeHackathons->isNotEmpty())
        <div class="grid-3">
            @foreach($activeHackathons as $hackathon)
                <x-hackathon-card :hackathon="$hackathon" />
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <x-heroicon-o-calendar class="empty-state-icon" style="width: 48px; height: 48px;" />
            <h3 class="empty-state-title">No hackathons running right now</h3>
            <p class="empty-state-description">Check back soon or explore past event results.</p>
        </div>
    @endif
</section>
@endif

{{-- FEATURES --}}
<section style="padding: 80px 24px; max-width: 1200px; margin: 0 auto;">
    <div style="text-align: center; margin-bottom: 64px;">
        <div class="badge badge-indigo" style="margin-bottom: 16px;">Everything You Need</div>
        <h2 style="font-size: 32px; font-weight: 800; color: var(--text-primary);">Powerful features for every role</h2>
    </div>

    <div class="grid-3">
        <div class="card" style="padding: 32px;">
            <div class="stat-icon" style="margin-bottom: 20px;"><x-heroicon-o-users style="width: 22px; height: 22px;" /></div>
            <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 12px;">Team Management</h3>
            <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6;">Invite-based formation, size limits, and full control for team leaders.</p>
        </div>
        <div class="card" style="padding: 32px;">
            <div class="stat-icon" style="margin-bottom: 20px;"><x-heroicon-o-document-text style="width: 22px; height: 22px;" /></div>
            <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 12px;">Submissions</h3>
            <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6;">Draft projects, upload assets, and finalize before the live countdown ends.</p>
        </div>
        <div class="card" style="padding: 32px;">
            <div class="stat-icon" style="margin-bottom: 20px;"><x-heroicon-o-trophy style="width: 22px; height: 22px;" /></div>
            <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 12px;">Judging System</h3>
            <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6;">Custom rubrics, specialized tracks, and automated leaderboard generation.</p>
        </div>
    </div>
</section>

{{-- HOW IT WORKS --}}
<section style="background: var(--surface); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); padding: 80px 24px;">
    <div style="max-width: 1000px; margin: 0 auto; text-align: center;">
        <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 48px;">Up and running in minutes</h2>
        <div class="grid-3">
            <div>
                <div style="width: 40px; height: 40px; border-radius: 99px; background: var(--accent); color: white; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-weight: 700;">1</div>
                <h3 style="font-weight: 700; margin-bottom: 10px;">Install</h3>
                <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6;">Upload files to your host and follow the simple 5-step installation wizard.</p>
            </div>
            <div>
                <div style="width: 40px; height: 40px; border-radius: 99px; background: var(--accent); color: white; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-weight: 700;">2</div>
                <h3 style="font-weight: 700; margin-bottom: 10px;">Configure</h3>
                <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6;">Set your event dates, create tracks, and customize the scoring criteria.</p>
            </div>
            <div>
                <div style="width: 40px; height: 40px; border-radius: 99px; background: var(--accent); color: white; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-weight: 700;">3</div>
                <h3 style="font-weight: 700; margin-bottom: 10px;">Launch</h3>
                <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6;">Builders register, teams form, and you manage everything from your dashboard.</p>
            </div>
        </div>
    </div>
</section>

{{-- CALL TO ACTION --}}
<section style="padding: 80px 24px; text-align: center;">
    <div class="card" style="max-width: 700px; margin: 0 auto; padding: 64px 32px; background: var(--surface-alt);">
        <x-heroicon-o-code-bracket-square style="width: 48px; height: 48px; color: var(--accent); margin-bottom: 24px;" />
        <h2 style="font-size: 28px; font-weight: 800; color: var(--text-primary);">Ready to host?</h2>
        <p style="font-size: 16px; color: var(--text-secondary); margin-top: 12px; margin-bottom: 32px; line-height: 1.6;">
            HostYourHackathon is free and open source. Start your own event today.
        </p>
        <div style="display: flex; justify-content: center; gap: 16px;">
            <x-button href="{{ route('register') }}" variant="primary" size="lg">Get Started</x-button>
            <x-button href="https://github.com/prayangshuuu/hostyourhackathon" variant="secondary" size="lg" target="_blank" icon="star">Star on GitHub</x-button>
        </div>
    </div>
</section>

@include('partials.footer')
@endsection
