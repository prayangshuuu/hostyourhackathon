{{-- Hackathon Card Component --}}
@props(['hackathon'])

<a href="{{ route('hackathons.show', $hackathon) }}" class="hackathon-card">
    @if ($hackathon->banner)
        <img src="{{ Storage::url($hackathon->banner) }}" alt="{{ $hackathon->title }}" class="hackathon-card-banner">
    @else
        <div class="hackathon-card-banner" style="display:flex; align-items:center; justify-content:center; color:var(--color-text-muted);">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="3" stroke="currentColor" stroke-width="1.5"/><path d="M3 16l5-5 4 4 3-3 6 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
    @endif
    <div class="hackathon-card-content">
        @if ($hackathon->logo)
            <img src="{{ Storage::url($hackathon->logo) }}" alt="" class="hackathon-card-logo">
        @else
            <div class="hackathon-card-logo" style="display:flex; align-items:center; justify-content:center; font-size:var(--font-size-sm); font-weight:var(--font-weight-semibold); color:var(--color-accent);">
                {{ strtoupper(substr($hackathon->title, 0, 1)) }}
            </div>
        @endif
        <div class="hackathon-card-title">{{ $hackathon->title }}</div>
        <div class="hackathon-card-tagline">{{ $hackathon->tagline }}</div>
        <div class="hackathon-card-footer">
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
            <span class="badge {{ $statusClass }}">{{ ucfirst($hackathon->status->value) }}</span>
            @if ($hackathon->registration_closes_at && $hackathon->registration_closes_at->isFuture())
                <span class="hackathon-card-deadline">Reg. closes {{ $hackathon->registration_closes_at->format('M d') }}</span>
            @endif
        </div>
    </div>
</a>
