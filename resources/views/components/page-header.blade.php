@props(['title', 'description' => null, 'breadcrumbs' => []])

<div style="border-bottom: 1px solid var(--border); padding-bottom: 24px; margin-bottom: 32px;">
    @if(!empty($breadcrumbs))
        <div style="font-size: 13px; line-height: 1.5; color: var(--text-muted); margin-bottom: 8px; display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
            @foreach($breadcrumbs as $index => $breadcrumb)
                @if(isset($breadcrumb['url']) && !$loop->last)
                    <a href="{{ $breadcrumb['url'] }}" style="color: var(--text-muted); text-decoration: none;">{{ $breadcrumb['label'] }}</a>
                    <x-heroicon-o-chevron-right class="w-3 h-3" />
                @else
                    <span style="color: var(--text-secondary);">{{ $breadcrumb['label'] ?? $breadcrumb }}</span>
                @endif
            @endforeach
        </div>
    @endif

    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px;">
        <div>
            <h1 style="font-size: 22px; line-height: 1.3; font-weight: 600; color: var(--text-primary); margin: 4px 0 0 0;">{{ $title }}</h1>
            @if($description)
                <p style="font-size: 14px; line-height: 1.6; color: var(--text-secondary); margin: 4px 0 0 0;">{{ $description }}</p>
            @endif
        </div>
        
        @if(isset($actions) && $actions->isNotEmpty())
            <div style="display: flex; gap: 8px; align-items: center;">
                {{ $actions }}
            </div>
        @endif
    </div>
</div>
