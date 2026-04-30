@props(['title' => null, 'description' => null, 'variant' => 'default'])

@php
    $variantClass = match ($variant) {
        'flat' => 'card-flat',
        'accent' => 'card-outlined-accent',
        default => '',
    };
@endphp

<div class="card {{ $variantClass }}" style="overflow: hidden;">
    @if($title || isset($actions))
        <div class="card-header">
            <div>
                @if($title)
                    <h3 style="font-size: 15px; font-weight: 600; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 8px;">{{ $title }}</h3>
                @endif
                @if($description)
                    <p style="font-size: 13px; line-height: 1.5; color: var(--text-muted); margin: 2px 0 0 0;">{{ $description }}</p>
                @endif
            </div>
            @if(isset($actions))
                <div style="display: flex; align-items: center; gap: 8px;">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    <div class="card-body">
        {{ $slot }}
    </div>
</div>
