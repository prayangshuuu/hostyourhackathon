@props([
    'icon' => 'heroicon-o-inbox',
    'title',
    'description',
])

<div style="padding: 48px 24px; text-align: center; display: flex; flex-direction: column; align-items: center;">
    @svg($icon, 'w-10 h-10')
    <h3 style="margin: 12px 0 0 0; font-size: 15px; line-height: 1.5; font-weight: 600; color: var(--text-primary);">{{ $title }}</h3>
    <p style="margin: 4px 0 0 0; max-width: 320px; font-size: 14px; line-height: 1.6; color: var(--text-muted);">{{ $description }}</p>
    @if (isset($action))
        <div style="margin-top: 16px;">
            {{ $action }}
        </div>
    @endif
</div>
