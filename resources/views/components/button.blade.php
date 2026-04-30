@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
    'iconOnly' => false,
    'fullWidth' => false,
])

@php
    $sizeClass = match ($size) {
        'sm' => $iconOnly ? 'btn-icon-sm' : 'btn-sm',
        'lg' => $iconOnly ? 'btn-icon-md' : 'btn-lg',
        default => $iconOnly ? 'btn-icon-md' : 'btn-md',
    };

    $variantClass = match ($variant) {
        'secondary' => 'btn-secondary',
        'danger' => 'btn-danger',
        'ghost' => 'btn-ghost',
        default => 'btn-primary',
    };

    $classes = trim('btn ' . $sizeClass . ' ' . $variantClass . ($fullWidth ? ' btn-full' : ''));
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
