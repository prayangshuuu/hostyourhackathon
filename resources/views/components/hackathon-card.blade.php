{{-- Hackathon Card Component --}}
@props(['hackathon', 'inactive' => false])

@php
    $isInactive = filter_var($inactive, FILTER_VALIDATE_BOOLEAN);
    $statusLabel = match ($hackathon->status->value) {
        'ended' => 'Ended',
        'archived' => 'Archived',
        default => ucfirst($hackathon->status->value),
    };
@endphp

<a href="{{ route('hackathons.show', $hackathon) }}" style="
    background: var(--surface);
    border: 1px solid {{ $isInactive ? 'rgba(148,163,184,0.55)' : 'var(--border)' }};
    border-radius: var(--radius-lg);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: border-color 150ms ease;
    text-decoration: none;
    color: inherit;
    opacity: {{ $isInactive ? '0.92' : '1' }};
" onmouseover="this.style.borderColor='{{ $isInactive ? 'rgba(148,163,184,0.85)' : 'var(--accent)' }}'" onmouseout="this.style.borderColor='{{ $isInactive ? 'rgba(148,163,184,0.55)' : 'var(--border)' }}'">
    {{-- Banner --}}
    @if ($hackathon->banner)
        <img src="{{ Storage::url($hackathon->banner) }}" alt="{{ $hackathon->title }}" style="
            width: 100%; height: 120px; object-fit: cover; display: block;
            filter: {{ $isInactive ? 'grayscale(35%) brightness(0.97)' : 'none' }};
        ">
    @else
        <div style="
            width: 100%; height: 120px; background: {{ $isInactive ? 'var(--surface-alt)' : 'var(--accent-light)' }};
            display: flex; align-items: center; justify-content: center; color: var(--accent);
        ">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none">
                <rect x="3" y="3" width="18" height="18" rx="3" stroke="currentColor" stroke-width="1.5"/>
                <path d="M3 16l5-5 4 4 3-3 6 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    @endif

    {{-- Body --}}
    <div style="padding: 16px 20px 20px; flex: 1; display: flex; flex-direction: column;">
        {{-- Logo --}}
        @if ($hackathon->logo)
            <img src="{{ Storage::url($hackathon->logo) }}" alt="" style="
                width: 36px; height: 36px; border-radius: var(--radius-md);
                border: 1px solid var(--border); object-fit: cover;
                margin-top: -34px; position: relative; background: var(--surface); margin-bottom: 10px;
            ">
        @else
            <div style="
                width: 36px; height: 36px; border-radius: var(--radius-md);
                border: 1px solid var(--border); background: var(--surface);
                margin-top: -34px; position: relative; margin-bottom: 10px;
                display: flex; align-items: center; justify-content: center;
                font-size: 14px; font-weight: 600; color: var(--accent);
            ">
                {{ strtoupper(substr($hackathon->title, 0, 1)) }}
            </div>
        @endif

        {{-- Title --}}
        <div style="font-size: 15px; font-weight: 600; color: var(--text-primary);">{{ $hackathon->title }}</div>

        {{-- Tagline --}}
        <div style="
            font-size: 13px; color: var(--text-muted); margin-top: 4px; flex: 1;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        ">{{ $hackathon->tagline ?? Str::limit($hackathon->description, 80) }}</div>

        {{-- Footer --}}
        <div style="margin-top: 16px; display: flex; align-items: center; justify-content: space-between;">
            @php
                $badgeVariant = match($hackathon->status->value) {
                    'published' => 'info',
                    'ongoing' => 'success',
                    'ended' => 'neutral',
                    'draft' => 'neutral',
                    'archived' => 'neutral',
                    default => 'neutral',
                };
            @endphp
            <x-badge :variant="$badgeVariant">{{ $isInactive ? $statusLabel : ucfirst($hackathon->status->value) }}</x-badge>

            @if (! $isInactive && $hackathon->registration_closes_at && $hackathon->registration_closes_at->isFuture())
                <span style="font-size: 12px; color: var(--text-muted);">Reg. closes {{ $hackathon->registration_closes_at->format('M d') }}</span>
            @elseif (! $isInactive && $hackathon->submission_opens_at && $hackathon->submission_opens_at->isFuture())
                <span style="font-size: 12px; color: var(--text-muted);">Submissions open</span>
            @elseif ($isInactive)
                <span style="font-size: 12px; color: var(--text-muted);">View details</span>
            @endif
        </div>
    </div>
</a>
