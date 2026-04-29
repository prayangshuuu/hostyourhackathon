@props(['title' => null, 'description' => null])

<div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden;">
    @if($title || isset($actions))
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                @if($title)
                    <h3 style="font-size: 15px; font-weight: 600; color: var(--text-primary); margin: 0;">{{ $title }}</h3>
                @endif
                @if($description)
                    <p style="font-size: 13px; color: var(--text-muted); margin: 2px 0 0 0;">{{ $description }}</p>
                @endif
            </div>
            @if(isset($actions))
                <div>
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    <div style="padding: 20px;">
        {{ $slot }}
    </div>
</div>
