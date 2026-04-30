@props(['type' => 'info', 'message'])

@php
    $baseStyles = "position: relative; padding: 12px 40px 12px 16px; border-radius: var(--radius-md); font-size: 14px; border: 1px solid; margin-bottom: 24px; display: flex; align-items: center; gap: 8px; transition: opacity 300ms ease, transform 300ms ease;";
    
    $typeStyles = match($type) {
        'success' => "background: var(--success-light); border-color: rgba(22,163,74,0.2); color: var(--success);",
        'error' => "background: var(--danger-light); border-color: rgba(220,38,38,0.2); color: var(--danger);",
        'warning' => "background: var(--warning-light); border-color: rgba(217,119,6,0.2); color: var(--warning);",
        default => "background: var(--accent-light); border-color: rgba(99,102,241,0.2); color: var(--accent);", // info
    };
    
    $icon = match($type) {
        'success' => 'check-circle',
        'error' => 'x-circle',
        'warning' => 'exclamation-triangle',
        default => 'information-circle',
    };
    
    $alertId = 'alert-' . Str::random(8);
@endphp

<div id="{{ $alertId }}" style="{{ $baseStyles }} {{ $typeStyles }}" role="alert">
    @svg('heroicon-o-' . $icon, 'w-[18px] h-[18px]')
    {{ $message }}
    <button type="button" onclick="document.getElementById('{{ $alertId }}')?.remove()" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); border: none; background: transparent; color: currentColor; display: inline-flex; align-items: center; justify-content: center; width: 20px; height: 20px; cursor: pointer;">
        <x-heroicon-o-x-mark class="w-4 h-4" />
    </button>
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
        }, 5000);
    });
</script>
