@props(['variant' => 'neutral', 'dot' => false])

@php
    $variantClass = match($variant) {
        'success' => 'background: var(--success-light); color: var(--success);',
        'warning', 'amber' => 'background: var(--warning-light); color: var(--warning);',
        'danger' => 'background: var(--danger-light); color: var(--danger);',
        'info', 'indigo' => 'background: var(--accent-light); color: var(--accent);',
        default => 'background: var(--surface-alt); color: var(--text-secondary);',
    };
@endphp

<span class="badge {{ $dot ? 'badge-dot' : '' }}" style="{{ $variantClass }}" {{ $attributes }}>
    {{ $slot }}
</span>
