@extends('layouts.public')

@section('title', $segment->name . ' — ' . $segment->hackathon->title)

@section('content')
    <div style="max-width: 1000px; margin: 0 auto; padding: 32px 24px;">
        {{-- Breadcrumb --}}
        <div style="margin-bottom: 24px;">
            <a href="{{ route('single.segments.index') }}" style="display: inline-flex; align-items: center; gap: 8px; color: var(--text-muted); font-size: 14px; font-weight: 500;">
                <x-heroicon-o-arrow-left style="width: 16px; height: 16px;" />
                Back to All Segments
            </a>
        </div>

        {{-- Header Card --}}
        <div class="card" style="padding: 40px; margin-bottom: 48px;">
            <div class="split" style="align-items: flex-start;">
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 16px;">
                        <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--accent-light); color: var(--accent); display: flex; align-items: center; justify-content: center;">
                            <x-heroicon-o-puzzle-piece style="width: 24px; height: 24px;" />
                        </div>
                        <h1 style="font-size: 32px; font-weight: 800; color: var(--text-primary);">{{ $segment->name }}</h1>
                        <x-badge variant="success" style="margin-left: 8px;">Active</x-badge>
                    </div>
                    <p style="font-size: 16px; color: var(--text-secondary); margin-top: 16px; line-height: 1.6;">
                        {{ $segment->description }}
                    </p>
                </div>
                
                <div class="stack" style="gap: 12px; min-width: 200px;">
                    @if($segment->rulebook_path)
                        <x-button href="{{ Storage::url($segment->rulebook_path) }}" target="_blank" variant="secondary" icon="arrow-down-tray" fullWidth>
                            Download Rulebook
                        </x-button>
                    @endif
                    
                    @auth
                        @if(!auth()->user()->hasTeamInHackathon($segment->hackathon))
                            @if($segment->hackathon->isRegistrationOpen() && !$segment->isFull())
                                <x-button href="{{ route('single.teams.create', ['segment_id' => $segment->id]) }}" variant="primary" icon="plus" fullWidth>
                                    Register for Track
                                </x-button>
                            @endif
                        @endif
                    @else
                        <x-button href="{{ route('register') }}" variant="primary" fullWidth>
                            Register Now
                        </x-button>
                    @endauth
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div x-data="{ tab: 'rules' }">
            <div style="display: flex; gap: 32px; border-bottom: 1px solid var(--border); margin-bottom: 32px;">
                <button @click="tab = 'rules'" :class="tab === 'rules' ? 'tab-active' : 'tab-inactive'">Rules</button>
                <button @click="tab = 'prizes'" :class="tab === 'prizes' ? 'tab-active' : 'tab-inactive'">Prizes</button>
                <button @click="tab = 'faqs'" :class="tab === 'faqs' ? 'tab-active' : 'tab-inactive'">FAQs</button>
                <button @click="tab = 'sponsors'" :class="tab === 'sponsors' ? 'tab-active' : 'tab-inactive'">Sponsors</button>
            </div>

            <style>
                .tab-active { color: var(--accent); font-weight: 600; padding-bottom: 12px; border-bottom: 2px solid var(--accent); margin-bottom: -1px; }
                .tab-inactive { color: var(--text-muted); font-weight: 500; padding-bottom: 12px; transition: color 0.2s; }
                .tab-inactive:hover { color: var(--text-primary); }
            </style>

            {{-- Rules Tab --}}
            <div x-show="tab === 'rules'" class="prose">
                @if($segment->rules)
                    <div class="card" style="padding: 32px;">
                        {!! $segment->rules !!}
                    </div>
                @else
                    <div class="empty-state">
                        <x-heroicon-o-document-text class="empty-state-icon" style="width: 48px; height: 48px;" />
                        <h3 class="empty-state-title">No specific rules set</h3>
                        <p class="empty-state-description">Please refer to the general hackathon rulebook or check back later.</p>
                    </div>
                @endif
            </div>

            {{-- Prizes Tab --}}
            <div x-show="tab === 'prizes'">
                @if($segment->prizes->count() > 0)
                    <div class="grid-2">
                        @foreach($segment->prizes as $prize)
                            <div class="card" style="padding: 24px;">
                                <div class="badge badge-indigo" style="margin-bottom: 12px;">
                                    {{ $prize->rank ? "Rank #{$prize->rank}" : 'Special Prize' }}
                                </div>
                                <h3 style="font-size: 18px; font-weight: 700; color: var(--text-primary);">{{ $prize->title }}</h3>
                                <div style="font-size: 24px; font-weight: 800; color: var(--accent); margin-top: 8px;">{{ $prize->amount }}</div>
                                @if($prize->description)
                                    <p style="font-size: 14px; color: var(--text-secondary); margin-top: 12px; line-height: 1.5;">
                                        {{ $prize->description }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <x-heroicon-o-trophy class="empty-state-icon" style="width: 48px; height: 48px;" />
                        <h3 class="empty-state-title">Prizes to be announced</h3>
                        <p class="empty-state-description">Stay tuned! We are finalizing the rewards for this track.</p>
                    </div>
                @endif
            </div>

            {{-- FAQs Tab --}}
            <div x-show="tab === 'faqs'">
                @php
                    $faqs = $segment->faqs->count() > 0 ? $segment->faqs : $segment->hackathon->faqs;
                @endphp
                @if($faqs->count() > 0)
                    <div class="stack">
                        @foreach($faqs as $faq)
                            <div class="card" style="padding: 20px;">
                                <h4 style="font-weight: 600; color: var(--text-primary); font-size: 16px;">{{ $faq->question }}</h4>
                                <p style="font-size: 14px; color: var(--text-secondary); margin-top: 10px; line-height: 1.6;">
                                    {{ $faq->answer }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <x-heroicon-o-question-mark-circle class="empty-state-icon" style="width: 48px; height: 48px;" />
                        <h3 class="empty-state-title">No FAQs yet</h3>
                        <p class="empty-state-description">Got questions? Feel free to reach out to the organizers.</p>
                    </div>
                @endif
            </div>

            {{-- Sponsors Tab --}}
            <div x-show="tab === 'sponsors'">
                @php
                    $allSponsors = $segment->sponsors->concat($segment->hackathon->sponsors->map(function($s) {
                        $s->isPlatform = true;
                        return $s;
                    }));
                @endphp
                
                @if($allSponsors->count() > 0)
                    <div class="grid-3">
                        @foreach($allSponsors as $sponsor)
                            <div class="card" style="padding: 20px; text-align: center;">
                                @if($sponsor->logo)
                                    <img src="{{ Storage::url($sponsor->logo) }}" alt="{{ $sponsor->name }}" style="height: 48px; margin: 0 auto 16px; object-fit: contain;">
                                @endif
                                <div style="font-weight: 600; color: var(--text-primary);">{{ $sponsor->name }}</div>
                                @if($sponsor->isPlatform)
                                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px; text-transform: uppercase;">Platform Sponsor</div>
                                @endif
                                @if($sponsor->website)
                                    <a href="{{ $sponsor->website }}" target="_blank" style="display: block; font-size: 12px; color: var(--accent); margin-top: 8px;">Visit Website</a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <x-heroicon-o-heart class="empty-state-icon" style="width: 48px; height: 48px;" />
                        <h3 class="empty-state-title">Sponsors to be announced</h3>
                        <p class="empty-state-description">Want to sponsor this track? Contact us for opportunities.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
