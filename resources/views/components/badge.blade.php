@props(['variant' => 'neutral'])

@php
    $baseStyles = "display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 99px; font-size: 12px; font-weight: 500; white-space: nowrap;";
    
    $variantStyles = match($variant) {
        'success' => "background: var(--success-light); color: var(--success);",
        'warning', 'amber' => "background: var(--warning-light); color: var(--warning);",
        'danger' => "background: var(--danger-light); color: var(--danger);",
        'info', 'indigo' => "background: var(--accent-light); color: var(--accent);",
        'violet' => "background: #f3e8ff; color: #7c3aed;",
        'teal' => "background: #f0fdfa; color: #0f766e;",
        default => "background: var(--surface-alt); color: var(--text-secondary);", // neutral
    };
@endphp

<span style="{{ $baseStyles }} {{ $variantStyles }}" {{ $attributes }}>
    {{ $slot }}
</span>
