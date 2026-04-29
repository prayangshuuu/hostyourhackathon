@extends('layouts.public')

@section('title', $hackathon->title . ' — ' . config('app.name'))
@section('meta_description', $hackathon->tagline ?? str()->limit($hackathon->description, 160))

@section('content')
    {{-- Banner --}}
    <div class="detail-banner-wrapper">
        @if ($hackathon->banner)
            <img src="{{ Storage::url($hackathon->banner) }}" alt="{{ $hackathon->title }}" class="detail-banner">
        @else
            <div class="detail-banner" style="height:200px; display:flex; align-items:center; justify-content:center; color:var(--color-text-muted);">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="3" stroke="currentColor" stroke-width="1.5"/><path d="M3 16l5-5 4 4 3-3 6 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
        @endif
        <div class="detail-banner-gradient"></div>
    </div>

    {{-- Header --}}
    <div class="detail-header-row">
        <div class="detail-header">
            @if ($hackathon->logo)
                <img src="{{ Storage::url($hackathon->logo) }}" alt="" class="detail-logo">
            @else
                <div class="detail-logo" style="display:flex; align-items:center; justify-content:center; font-size:var(--font-size-lg); font-weight:var(--font-weight-semibold); color:var(--color-accent);">
                    {{ strtoupper(substr($hackathon->title, 0, 1)) }}
                </div>
            @endif
            <div>
                <h1 class="text-page-title" style="margin:0;">{{ $hackathon->title }}</h1>
                @php
                    $statusClass = match($hackathon->status->value) {
                        'draft' => 'badge-partial',
                        'published' => 'badge-scored',
                        'ongoing' => 'badge-pending',
                        'ended' => 'badge-partial',
                        'archived' => 'badge-partial',
                        default => 'badge-partial',
                    };
                @endphp
                <span class="badge {{ $statusClass }}" style="margin-top:4px;">{{ ucfirst($hackathon->status->value) }}</span>
            </div>
        </div>

        {{-- Register Button --}}
        @guest
            <a href="{{ route('login') }}" class="btn btn-secondary">Sign in to Register</a>
        @else
            @if ($isRegistered)
                <span class="btn-registered">Registered ✓</span>
            @elseif ($registrationOpen)
                <a href="{{ route('teams.create', $hackathon) }}" class="btn btn-primary">Register Now</a>
            @else
                <button class="btn btn-secondary" disabled>Registration Closed</button>
            @endif
        @endguest
    </div>

    {{-- Tab Navigation --}}
    <div class="tab-bar" id="tab-bar">
        <button class="tab-item active" data-tab="about">About</button>
        <button class="tab-item" data-tab="timeline">Timeline</button>
        <button class="tab-item" data-tab="rules">Rules</button>
        <button class="tab-item" data-tab="prizes">Prizes</button>
        <button class="tab-item" data-tab="sponsors">Sponsors</button>
        <button class="tab-item" data-tab="faqs">FAQs</button>
    </div>

    {{-- Tab Panels --}}
    <div class="tab-panel active" id="panel-about">
        <div class="card" style="padding:32px;">
            <div style="font-size:var(--font-size-sm); color:var(--color-text-primary); line-height:var(--line-height-relaxed);">
                {!! nl2br(e($hackathon->description)) !!}
            </div>

            @if ($hackathon->segments->count())
                <div style="margin-top:24px;">
                    <p class="text-card-title" style="margin-bottom:12px;">Segments</p>
                    <div style="display:flex; flex-wrap:wrap; gap:8px;">
                        @foreach ($hackathon->segments as $segment)
                            <span class="badge badge-visibility-registered" style="border-radius:99px; padding:3px 10px;">{{ $segment->name }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="tab-panel" id="panel-timeline">
        <div class="card" style="padding:32px;">
            <div class="timeline">
                @php
                    $events = collect();
                    if ($hackathon->registration_opens_at) $events->push(['label' => 'Registration Opens', 'date' => $hackathon->registration_opens_at]);
                    if ($hackathon->registration_closes_at) $events->push(['label' => 'Registration Closes', 'date' => $hackathon->registration_closes_at]);
                    if ($hackathon->submission_opens_at) $events->push(['label' => 'Submission Opens', 'date' => $hackathon->submission_opens_at]);
                    if ($hackathon->submission_closes_at) $events->push(['label' => 'Submission Closes', 'date' => $hackathon->submission_closes_at]);
                    if ($hackathon->results_at) $events->push(['label' => 'Results Announced', 'date' => $hackathon->results_at]);
                    $events = $events->sortBy('date');
                @endphp
                @foreach ($events as $event)
                    <div class="timeline-item {{ $event['date']->isPast() ? 'timeline-item-past' : '' }}">
                        <div class="timeline-dot {{ $event['date']->isPast() ? 'timeline-dot-past' : '' }}"></div>
                        <div class="timeline-label">{{ $event['label'] }}</div>
                        <div class="timeline-date">{{ $event['date']->format('M d, Y · h:i A') }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="tab-panel" id="panel-rules">
        <div class="card" style="padding:32px;">
            <div style="font-size:var(--font-size-sm); color:var(--color-text-primary); line-height:var(--line-height-relaxed);">
                <p style="color:var(--color-text-muted);">Rules and guidelines for this hackathon will be posted by the organizer.</p>
            </div>
        </div>
    </div>

    <div class="tab-panel" id="panel-prizes">
        <div class="card" style="padding:32px;">
            @if ($hackathon->segments->count())
                <div class="grid-3">
                    @foreach ($hackathon->segments as $segment)
                        <div class="card" style="padding:20px; border:1px solid var(--color-border);">
                            <div class="text-card-title">{{ $segment->name }}</div>
                            <p style="font-size:var(--font-size-sm); color:var(--color-text-muted); margin-top:8px;">
                                {{ $segment->description ?? 'Prize details coming soon.' }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="color:var(--color-text-muted); font-size:var(--font-size-sm);">Prize information will be announced soon.</p>
            @endif
        </div>
    </div>

    <div class="tab-panel" id="panel-sponsors">
        @php
            $grouped = $hackathon->sponsors->groupBy(fn ($s) => $s->tier->value);
            $tiers = ['title' => 'Title Sponsors', 'gold' => 'Gold Sponsors', 'silver' => 'Silver Sponsors', 'bronze' => 'Bronze Sponsors'];
        @endphp
        @foreach ($tiers as $tier => $title)
            @if (isset($grouped[$tier]) && $grouped[$tier]->count())
                <div class="sponsor-section">
                    <div class="sponsor-section-title">{{ $title }}</div>
                    <div class="sponsor-grid-{{ $tier }}">
                        @foreach ($grouped[$tier] as $sponsor)
                            <a href="{{ $sponsor->url }}" target="_blank" class="sponsor-logo-card" title="{{ $sponsor->name }}">
                                @if ($sponsor->logo)
                                    <img src="{{ Storage::url($sponsor->logo) }}" alt="{{ $sponsor->name }}">
                                @else
                                    <span style="font-size:var(--font-size-sm); font-weight:var(--font-weight-medium); color:var(--color-text-muted);">{{ $sponsor->name }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
        @if ($hackathon->sponsors->isEmpty())
            <div class="card" style="padding:32px;">
                <p style="color:var(--color-text-muted); font-size:var(--font-size-sm);">Sponsors will be announced soon.</p>
            </div>
        @endif
    </div>

    <div class="tab-panel" id="panel-faqs">
        @if ($hackathon->faqs->count())
            @foreach ($hackathon->faqs as $faq)
                <div class="accordion-item">
                    <button class="accordion-header" type="button">
                        {{ $faq->question }}
                        <svg class="accordion-chevron" viewBox="0 0 16 16" fill="none">
                            <path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <div class="accordion-body">
                        {!! nl2br(e($faq->answer)) !!}
                    </div>
                </div>
            @endforeach
        @else
            <div class="card" style="padding:32px;">
                <p style="color:var(--color-text-muted); font-size:var(--font-size-sm);">No FAQs yet.</p>
            </div>
        @endif
    </div>

    <div style="height:80px;"></div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Tab switching
            const tabItems = document.querySelectorAll('.tab-item');
            const tabPanels = document.querySelectorAll('.tab-panel');

            tabItems.forEach(function (tab) {
                tab.addEventListener('click', function () {
                    tabItems.forEach(t => t.classList.remove('active'));
                    tabPanels.forEach(p => p.classList.remove('active'));
                    this.classList.add('active');
                    document.getElementById('panel-' + this.dataset.tab).classList.add('active');
                });
            });

            // Accordion
            document.querySelectorAll('.accordion-header').forEach(function (header) {
                header.addEventListener('click', function () {
                    this.closest('.accordion-item').classList.toggle('is-open');
                });
            });
        });
    </script>
    @endpush
@endsection
