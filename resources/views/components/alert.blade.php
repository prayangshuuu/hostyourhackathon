@props(['type' => 'info', 'message'])

@php
    $baseStyles = "padding: 12px 16px; border-radius: var(--radius-md); font-size: 14px; border: 1px solid; margin-bottom: 24px; display: flex; align-items: center; gap: 10px; transition: opacity 300ms ease, transform 300ms ease;";
    
    $typeStyles = match($type) {
        'success' => "background: var(--success-light); border-color: rgba(22,163,74,0.2); color: var(--success);",
        'error' => "background: var(--danger-light); border-color: rgba(220,38,38,0.2); color: var(--danger);",
        'warning' => "background: var(--warning-light); border-color: rgba(217,119,6,0.2); color: var(--warning);",
        default => "background: var(--accent-light); border-color: rgba(99,102,241,0.2); color: var(--accent);", // info
    };
    
    $icon = match($type) {
        'success' => '<path d="M8 1.333A6.667 6.667 0 1 0 14.667 8 6.674 6.674 0 0 0 8 1.333Zm3.06 4.94-3.667 4a.667.667 0 0 1-.986 0L4.94 8.607a.667.667 0 1 1 .986-.9l.96 1.046 3.174-3.46a.667.667 0 0 1 .986.9l.014.02Z" fill="currentColor"/>',
        'error' => '<path d="M8 1.333A6.667 6.667 0 1 0 14.667 8 6.674 6.674 0 0 0 8 1.333Zm2.473 8.527a.667.667 0 0 1-.946.946L8 9.28l-1.527 1.527a.667.667 0 0 1-.946-.946L7.054 8.333 5.527 6.807a.667.667 0 0 1 .946-.947L8 7.387l1.527-1.527a.667.667 0 0 1 .946.947L8.946 8.333l1.527 1.527Z" fill="currentColor"/>',
        'warning' => '<path d="M14.267 12.467 8.8 2.8a.933.933 0 0 0-1.6 0L1.733 12.467a.867.867 0 0 0 .8 1.2h10.934a.867.867 0 0 0 .8-1.2ZM8 11.333a.667.667 0 1 1 0-1.333.667.667 0 0 1 0 1.333Zm.667-3.333a.667.667 0 0 1-1.334 0V5.333a.667.667 0 0 1 1.334 0V8Z" fill="currentColor"/>',
        default => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>', // info
    };
    
    $alertId = 'alert-' . Str::random(8);
@endphp

<div id="{{ $alertId }}" style="{{ $baseStyles }} {{ $typeStyles }}" role="alert">
    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
        {!! $icon !!}
    </svg>
    {{ $message }}
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            const el = document.getElementById('{{ $alertId }}');
            if (el) {
                el.style.opacity = '0';
                el.style.transform = 'translateY(-8px)';
                setTimeout(() => el.remove(), 300);
            }
        }, 4000);
    });
</script>
