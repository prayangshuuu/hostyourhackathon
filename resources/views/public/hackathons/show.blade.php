@extends('layouts.public')

@section('title', $hackathon->title . ' — ' . config('app.name'))
@section('meta_description', $hackathon->tagline ?? Str::limit($hackathon->description, 160))

@section('content')
    {{-- Banner (full width inside container) --}}
    <div style="margin: 0 -24px;">
        @if ($hackathon->banner)
            <img src="{{ Storage::url($hackathon->banner) }}" alt="{{ $hackathon->title }}" style="
                width: 100%; height: 260px; object-fit: cover; display: block;
                background: var(--surface-alt);
            ">
        @else
            <div style="
                width: 100%; height: 260px; background: var(--accent-light);
                display: flex; align-items: center; justify-content: center;
            ">
                <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="1" opacity="0.5">
                    <rect x="3" y="3" width="18" height="18" rx="3"/>
                    <path d="M3 16l5-5 4 4 3-3 6 6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        @endif
    </div>

    {{-- Header Row --}}
    <div style="padding: 24px 0; display: flex; align-items: flex-start; gap: 20px;">
        {{-- Logo --}}
        @if ($hackathon->logo)
            <img src="{{ Storage::url($hackathon->logo) }}" alt="" style="
                width: 64px; height: 64px; border-radius: var(--radius-lg);
                border: 2px solid var(--border); background: var(--surface);
                object-fit: cover; margin-top: -32px; position: relative; flex-shrink: 0;
            ">
        @else
            <div style="
                width: 64px; height: 64px; border-radius: var(--radius-lg);
                border: 2px solid var(--border); background: var(--surface);
                margin-top: -32px; position: relative; flex-shrink: 0;
                display: flex; align-items: center; justify-content: center;
                font-size: 22px; font-weight: 600; color: var(--accent);
            ">{{ strtoupper(substr($hackathon->title, 0, 1)) }}</div>
        @endif

        {{-- Info --}}
        <div style="flex: 1; min-width: 0;">
            <h1 style="font-size: 26px; font-weight: 600; color: var(--text-primary); margin: 0; line-height: 1.3;">{{ $hackathon->title }}</h1>
            @if ($hackathon->tagline)
                <p style="font-size: 15px; color: var(--text-secondary); margin: 4px 0 0 0;">{{ $hackathon->tagline }}</p>
            @endif
        </div>

        {{-- Status + Register --}}
        <div style="display: flex; align-items: center; gap: 12px; flex-shrink: 0;">
            @php
                $badgeVariant = match($hackathon->status->value) {
                    'published' => 'info',
                    'ongoing' => 'success',
                    'ended' => 'neutral',
                    default => 'neutral',
                };
            @endphp
            <x-badge :variant="$badgeVariant">{{ ucfirst($hackathon->status->value) }}</x-badge>

            @guest
                @if ($canRegisterParticipation)
                    <x-button href="{{ route('login') }}" variant="secondary" size="md">Sign in to Register</x-button>
                @elseif ($hackathon->isEndedOrArchived())
                    <span style="
                        padding: 8px 16px; font-size: 14px; font-weight: 500;
                        background: var(--surface-alt); color: var(--text-muted);
                        border: 1px solid var(--border); border-radius: var(--radius-md);
                    ">{{ $hackathon->status->value === 'archived' ? 'Archived' : 'Ended' }}</span>
                @endif
            @else
                @if ($isRegistered)
                    <span style="
                        display: inline-flex; align-items: center; gap: 6px;
                        padding: 8px 16px; font-size: 14px; font-weight: 500;
                        background: var(--success-light); color: var(--success);
                        border: 1px solid rgba(22,163,74,0.2); border-radius: var(--radius-md);
                    ">
                        <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor"><path d="M8 1.333A6.667 6.667 0 1 0 14.667 8 6.674 6.674 0 0 0 8 1.333Zm3.06 4.94-3.667 4a.667.667 0 0 1-.986 0L4.94 8.607a.667.667 0 1 1 .986-.9l.96 1.046 3.174-3.46a.667.667 0 0 1 .986.9l.014.02Z"/></svg>
                        Registered
                    </span>
                @elseif ($canRegisterParticipation && $registrationOpen)
                    <x-button href="{{ route('teams.create', $hackathon) }}" variant="primary" size="md">Register Now</x-button>
                @elseif (!$canRegisterParticipation)
                    <span style="
                        padding: 8px 16px; font-size: 14px; font-weight: 500;
                        background: var(--surface-alt); color: var(--text-muted);
                        border: 1px solid var(--border); border-radius: var(--radius-md);
                    ">Hackathon ended</span>
                @else
                    <span style="
                        padding: 8px 16px; font-size: 14px; font-weight: 500;
                        background: var(--surface-alt); color: var(--text-muted);
                        border: 1px solid var(--border); border-radius: var(--radius-md);
                    ">Registration Closed</span>
                @endif
            @endguest
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div id="tab-bar" style="border-bottom: 1px solid var(--border); display: flex; gap: 0; margin-bottom: 32px;">
        @foreach (['about' => 'About', 'timeline' => 'Timeline', 'segments' => 'Segments', 'rules' => 'Rules', 'prizes' => 'Prizes', 'sponsors' => 'Sponsors', 'faqs' => 'FAQs'] as $key => $label)
            <button
                class="tab-btn {{ $loop->first ? 'active' : '' }}"
                data-tab="{{ $key }}"
                type="button"
                style="
                    padding: 12px 20px; font-size: 14px; font-weight: 500;
                    color: {{ $loop->first ? 'var(--accent)' : 'var(--text-secondary)' }};
                    border: none; background: none; cursor: pointer;
                    border-bottom: 2px solid {{ $loop->first ? 'var(--accent)' : 'transparent' }};
                    transition: color 150ms ease, border-color 150ms ease;
                    font-family: Inter, sans-serif;
                "
            >{{ $label }}</button>
        @endforeach
    </div>

    {{-- ═══════════ Tab: About ═══════════ --}}
    <div class="tab-panel" id="panel-about" style="display: block;">
        <x-card>
            <div style="font-size: 15px; line-height: 1.7; color: var(--text-primary);">
                {!! nl2br(e($hackathon->description)) !!}
            </div>
        </x-card>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
            {{-- Registration Info --}}
            <x-card title="Registration">
                @if ($hackathon->registration_opens_at && $hackathon->registration_closes_at)
                    <div style="font-size: 13px; color: var(--text-secondary); display: flex; flex-direction: column; gap: 8px;">
                        <div><span style="font-weight: 500;">Opens:</span> {{ $hackathon->registration_opens_at->format('M d, Y · h:i A') }}</div>
                        <div><span style="font-weight: 500;">Closes:</span> {{ $hackathon->registration_closes_at->format('M d, Y · h:i A') }}</div>
                        <div style="margin-top: 4px;">
                            @if ($registrationOpen)
                                <x-badge variant="success">Open</x-badge>
                            @elseif ($hackathon->registration_opens_at->isFuture())
                                <x-badge variant="info">Upcoming</x-badge>
                            @else
                                <x-badge variant="neutral">Closed</x-badge>
                            @endif
                        </div>
                    </div>
                @else
                    <p style="font-size: 13px; color: var(--text-muted); margin: 0;">Dates not set yet.</p>
                @endif
            </x-card>

            {{-- Submission Info --}}
            <x-card title="Submission">
                @if ($hackathon->submission_opens_at && $hackathon->submission_closes_at)
                    @php
                        $submissionOpen = now()->between($hackathon->submission_opens_at, $hackathon->submission_closes_at);
                    @endphp
                    <div style="font-size: 13px; color: var(--text-secondary); display: flex; flex-direction: column; gap: 8px;">
                        <div><span style="font-weight: 500;">Opens:</span> {{ $hackathon->submission_opens_at->format('M d, Y · h:i A') }}</div>
                        <div><span style="font-weight: 500;">Closes:</span> {{ $hackathon->submission_closes_at->format('M d, Y · h:i A') }}</div>
                        <div style="margin-top: 4px;">
                            @if ($submissionOpen)
                                <x-badge variant="success">Open</x-badge>
                            @elseif ($hackathon->submission_opens_at->isFuture())
                                <x-badge variant="info">Upcoming</x-badge>
                            @else
                                <x-badge variant="neutral">Closed</x-badge>
                            @endif
                        </div>
                    </div>
                @else
                    <p style="font-size: 13px; color: var(--text-muted); margin: 0;">Dates not set yet.</p>
                @endif
            </x-card>
        </div>
    </div>

    {{-- ═══════════ Tab: Timeline ═══════════ --}}
    <div class="tab-panel" id="panel-timeline" style="display: none;">
        <x-card>
            @php
                $events = collect();
                if ($hackathon->registration_opens_at) $events->push(['label' => 'Registration Opens', 'date' => $hackathon->registration_opens_at]);
                if ($hackathon->registration_closes_at) $events->push(['label' => 'Registration Closes', 'date' => $hackathon->registration_closes_at]);
                if ($hackathon->submission_opens_at) $events->push(['label' => 'Submission Opens', 'date' => $hackathon->submission_opens_at]);
                if ($hackathon->submission_closes_at) $events->push(['label' => 'Submission Closes', 'date' => $hackathon->submission_closes_at]);
                if ($hackathon->results_at) $events->push(['label' => 'Results Announced', 'date' => $hackathon->results_at]);
                $events = $events->sortBy('date')->values();
            @endphp

            @if ($events->count())
                <div style="position: relative; padding-left: 28px;">
                    @foreach ($events as $i => $event)
                        @php
                            $isPast = $event['date']->isPast();
                            $isActive = !$isPast && ($i === 0 || $events[$i - 1]['date']->isPast());
                            $dotColor = $isPast ? 'var(--success)' : ($isActive ? 'var(--accent)' : 'var(--border)');
                            $statusLabel = $isPast ? 'Past' : ($isActive ? 'Active' : 'Upcoming');
                            $statusVariant = $isPast ? 'success' : ($isActive ? 'info' : 'neutral');
                        @endphp
                        <div style="position: relative; padding-bottom: {{ $loop->last ? '0' : '28px' }};">
                            {{-- Dot --}}
                            <div style="
                                position: absolute; left: -28px; top: 2px;
                                width: 12px; height: 12px; border-radius: 50%;
                                background: {{ $dotColor }};
                            "></div>
                            {{-- Connecting line --}}
                            @if (!$loop->last)
                                <div style="
                                    position: absolute; left: -23px; top: 16px;
                                    width: 2px; height: calc(100% - 12px);
                                    background: var(--border);
                                "></div>
                            @endif
                            {{-- Content --}}
                            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                <span style="font-size: 14px; font-weight: 500; color: var(--text-primary);">{{ $event['label'] }}</span>
                                <x-badge :variant="$statusVariant">{{ $statusLabel }}</x-badge>
                            </div>
                            <div style="font-size: 13px; color: var(--text-muted); margin-top: 2px;">
                                {{ $event['date']->format('M d, Y · h:i A') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="font-size: 14px; color: var(--text-muted); margin: 0;">Timeline dates have not been set yet.</p>
            @endif
        </x-card>
    </div>

    {{-- ═══════════ Tab: Segments ═══════════ --}}
    <div class="tab-panel" id="panel-segments" style="display: none;">
        @if ($hackathon->segments->count())
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                @foreach ($hackathon->segments as $segment)
                    <x-card :title="$segment->name">
                        <p style="font-size: 14px; color: var(--text-secondary); margin: 0 0 12px 0; line-height: 1.6;">
                            {{ $segment->description ?? 'No description provided.' }}
                        </p>
                        @if ($segment->rulebook)
                            <x-button href="{{ Storage::url($segment->rulebook) }}" variant="secondary" size="sm">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px;">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7 10 12 15 17 10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                                Download Rulebook
                            </x-button>
                        @endif
                    </x-card>
                @endforeach
            </div>
        @else
            <x-card>
                <p style="font-size: 14px; color: var(--text-muted); margin: 0;">No segments defined yet.</p>
            </x-card>
        @endif
    </div>

    {{-- ═══════════ Tab: Rules ═══════════ --}}
    <div class="tab-panel" id="panel-rules" style="display: none;">
        @if ($hackathon->segments->count())
            @foreach ($hackathon->segments as $segment)
                <div style="margin-bottom: 12px;">
                    <div style="
                        background: var(--surface); border: 1px solid var(--border);
                        border-radius: var(--radius-lg); overflow: hidden;
                    ">
                        <button type="button" class="accordion-trigger" style="
                            width: 100%; padding: 14px 20px; display: flex; align-items: center;
                            justify-content: space-between; border: none; background: none;
                            font-size: 15px; font-weight: 600; color: var(--text-primary);
                            cursor: pointer; font-family: Inter, sans-serif;
                        ">
                            {{ $segment->name }}
                            <svg class="accordion-chevron" width="16" height="16" viewBox="0 0 16 16" fill="none" style="transition: transform 200ms ease; color: var(--text-muted);">
                                <path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div class="accordion-content" style="display: none; padding: 0 20px 16px; font-size: 14px; color: var(--text-secondary); line-height: 1.7;">
                            {{ $segment->description ?? 'Rules will be posted by the organizer.' }}
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <x-card>
                <p style="font-size: 14px; color: var(--text-muted); margin: 0;">Rules and guidelines will be posted by the organizer.</p>
            </x-card>
        @endif
    </div>

    {{-- ═══════════ Tab: Prizes ═══════════ --}}
    <div class="tab-panel" id="panel-prizes" style="display: none;">
        @if ($hackathon->segments->count())
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                @foreach ($hackathon->segments as $segment)
                    <x-card :title="$segment->name">
                        <p style="font-size: 14px; color: var(--text-secondary); margin: 0;">
                            {{ $segment->description ?? 'Prize details coming soon.' }}
                        </p>
                    </x-card>
                @endforeach
            </div>
        @else
            <x-card>
                <p style="font-size: 14px; color: var(--text-muted); margin: 0;">Prize information will be announced soon.</p>
            </x-card>
        @endif
    </div>

    {{-- ═══════════ Tab: Sponsors ═══════════ --}}
    <div class="tab-panel" id="panel-sponsors" style="display: none;">
        @if ($hackathon->sponsors->count())
            @php
                $grouped = $hackathon->sponsors->groupBy(fn ($s) => $s->tier->value);
                $tierConfig = [
                    'title' => ['label' => 'Title Sponsors', 'cols' => 1, 'maxH' => '80px'],
                    'gold' => ['label' => 'Gold Sponsors', 'cols' => 3, 'maxH' => '60px'],
                    'silver' => ['label' => 'Silver Sponsors', 'cols' => 4, 'maxH' => '48px'],
                    'bronze' => ['label' => 'Bronze Sponsors', 'cols' => 5, 'maxH' => '36px'],
                ];
            @endphp
            @foreach ($tierConfig as $tier => $config)
                @if (isset($grouped[$tier]) && $grouped[$tier]->count())
                    <div style="margin-bottom: 32px;">
                        <h3 style="font-size: 14px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px 0;">
                            {{ $config['label'] }}
                        </h3>
                        <div style="display: grid; grid-template-columns: repeat({{ $config['cols'] }}, 1fr); gap: 16px; align-items: center;">
                            @foreach ($grouped[$tier] as $sponsor)
                                <a href="{{ $sponsor->url }}" target="_blank" rel="noopener" title="{{ $sponsor->name }}" style="
                                    display: flex; align-items: center; justify-content: center;
                                    padding: 16px; background: var(--surface); border: 1px solid var(--border);
                                    border-radius: var(--radius-md); text-decoration: none;
                                    transition: filter 150ms ease, border-color 150ms ease;
                                " onmouseover="this.style.borderColor='var(--accent)'; this.querySelector('img,span').style.filter='none';" onmouseout="this.style.borderColor='var(--border)'; this.querySelector('img,span').style.filter='grayscale(1)';">
                                    @if ($sponsor->logo)
                                        <img src="{{ Storage::url($sponsor->logo) }}" alt="{{ $sponsor->name }}" style="
                                            max-height: {{ $config['maxH'] }}; max-width: 100%; object-fit: contain;
                                            filter: grayscale(1); transition: filter 150ms ease;
                                        ">
                                    @else
                                        <span style="font-size: 13px; font-weight: 500; color: var(--text-muted); filter: grayscale(1); transition: filter 150ms ease;">
                                            {{ $sponsor->name }}
                                        </span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        @else
            <x-card>
                <p style="font-size: 14px; color: var(--text-muted); margin: 0;">Sponsors will be announced soon.</p>
            </x-card>
        @endif
    </div>

    {{-- ═══════════ Tab: FAQs ═══════════ --}}
    <div class="tab-panel" id="panel-faqs" style="display: none;">
        @if ($hackathon->faqs->count())
            <div style="display: flex; flex-direction: column; gap: 8px;">
                @foreach ($hackathon->faqs as $faq)
                    <div style="
                        background: var(--surface); border: 1px solid var(--border);
                        border-radius: var(--radius-lg); overflow: hidden;
                    ">
                        <button type="button" class="accordion-trigger" style="
                            width: 100%; padding: 14px 20px; display: flex; align-items: center;
                            justify-content: space-between; border: none; background: none;
                            font-size: 14px; font-weight: 500; color: var(--text-primary);
                            cursor: pointer; font-family: Inter, sans-serif; text-align: left;
                        ">
                            {{ $faq->question }}
                            <svg class="accordion-chevron" width="16" height="16" viewBox="0 0 16 16" fill="none" style="transition: transform 200ms ease; flex-shrink: 0; margin-left: 12px; color: var(--text-muted);">
                                <path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div class="accordion-content" style="display: none; padding: 0 20px 16px; font-size: 14px; color: var(--text-secondary); line-height: 1.7;">
                            {!! nl2br(e($faq->answer)) !!}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <x-card>
                <p style="font-size: 14px; color: var(--text-muted); margin: 0;">No FAQs posted yet.</p>
            </x-card>
        @endif
    </div>

    <div style="height: 80px;"></div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Tab switching
            const tabs = document.querySelectorAll('.tab-btn');
            const panels = document.querySelectorAll('.tab-panel');

            tabs.forEach(function (tab) {
                tab.addEventListener('click', function () {
                    // Reset all tabs
                    tabs.forEach(function (t) {
                        t.style.color = 'var(--text-secondary)';
                        t.style.borderBottomColor = 'transparent';
                        t.classList.remove('active');
                    });
                    // Reset all panels
                    panels.forEach(function (p) {
                        p.style.display = 'none';
                    });
                    // Activate clicked tab
                    this.style.color = 'var(--accent)';
                    this.style.borderBottomColor = 'var(--accent)';
                    this.classList.add('active');
                    // Show matching panel
                    document.getElementById('panel-' + this.dataset.tab).style.display = 'block';
                });
            });

            // Accordion
            document.querySelectorAll('.accordion-trigger').forEach(function (trigger) {
                trigger.addEventListener('click', function () {
                    var content = this.nextElementSibling;
                    var chevron = this.querySelector('.accordion-chevron');
                    if (content.style.display === 'none' || content.style.display === '') {
                        content.style.display = 'block';
                        if (chevron) chevron.style.transform = 'rotate(180deg)';
                    } else {
                        content.style.display = 'none';
                        if (chevron) chevron.style.transform = 'rotate(0)';
                    }
                });
            });
        });
    </script>
    @endpush
@endsection
