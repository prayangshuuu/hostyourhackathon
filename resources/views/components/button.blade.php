@props(['variant' => 'primary', 'size' => 'md', 'type' => 'button', 'href' => null])

@php
    $baseStyles = "display: inline-flex; align-items: center; justify-content: center; font-family: Inter, sans-serif; cursor: pointer; transition: background 150ms ease; text-decoration: none; border-radius: var(--radius-md); font-weight: 500;";
    
    // Size variants
    $sizeStyles = match($size) {
        'sm' => "padding: 6px 12px; font-size: 13px;",
        default => "padding: 9px 16px; font-size: 14px;",
    };

    // Color variants
    $variantStyles = match($variant) {
        'secondary' => "background: var(--surface); color: var(--text-primary); border: 1px solid var(--border);",
        'danger' => "background: var(--danger-light); color: var(--danger); border: 1px solid rgba(220,38,38,0.2);",
        'ghost' => "background: transparent; color: var(--text-secondary); border: none;",
        default => "background: var(--accent); color: white; border: 1px solid var(--accent);", // primary
    };
    
    // Add hover effects using inline styles via onmouseover/onmouseout attributes since we can't do pseudo-classes inline
    $hoverColor = match($variant) {
        'secondary' => 'var(--surface-alt)',
        'danger' => 'rgba(220,38,38,0.1)',
        'ghost' => 'var(--surface-alt)',
        default => 'var(--accent-hover)', // primary
    };
    $defaultBg = match($variant) {
        'secondary' => 'var(--surface)',
        'danger' => 'var(--danger-light)',
        'ghost' => 'transparent',
        default => 'var(--accent)', // primary
    };
    
    $style = "{$baseStyles} {$sizeStyles} {$variantStyles}";
    $onmouseover = "this.style.background='{$hoverColor}'";
    $onmouseout = "this.style.background='{$defaultBg}'";
@endphp

@if($href)
    <a href="{{ $href }}" style="{{ $style }}" onmouseover="{{ $onmouseover }}" onmouseout="{{ $onmouseout }}" {{ $attributes }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" style="{{ $style }}" onmouseover="{{ $onmouseover }}" onmouseout="{{ $onmouseout }}" onfocus="this.style.outline='2px solid var(--accent-ring)'; this.style.outlineOffset='2px';" onblur="this.style.outline='none';" {{ $attributes }}>
        {{ $slot }}
    </button>
@endif
