@props([
    'name',
    'label',
    'description' => null,
    'checked' => false,
])

<label for="{{ $name }}" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; cursor: pointer;">
    <span style="display: block;">
        <span style="display: block; font-size: 14px; line-height: 1.6; font-weight: 500; color: var(--text-primary);">{{ $label }}</span>
        @if ($description)
            <span style="display: block; margin-top: 2px; font-size: 13px; line-height: 1.5; color: var(--text-muted);">{{ $description }}</span>
        @endif
    </span>
    <span style="position: relative; width: 44px; height: 24px; flex-shrink: 0;">
        <input
            id="{{ $name }}"
            type="checkbox"
            name="{{ $name }}"
            value="1"
            {{ $checked ? 'checked' : '' }}
            {{ $attributes->except(['name', 'label', 'description', 'checked']) }}
            style="position: absolute; inset: 0; opacity: 0; margin: 0; cursor: pointer;"
            onchange="
                const track = this.nextElementSibling;
                const thumb = track.querySelector('[data-thumb]');
                track.style.background = this.checked ? 'var(--accent)' : 'var(--border)';
                thumb.style.transform = this.checked ? 'translateX(22px)' : 'translateX(2px)';
            "
        >
        <span style="position: absolute; inset: 0; border-radius: 99px; background: {{ $checked ? 'var(--accent)' : 'var(--border)' }}; transition: background 150ms ease;">
            <span data-thumb style="position: absolute; top: 2px; left: 0; width: 20px; height: 20px; border-radius: 99px; background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,0.15); transform: {{ $checked ? 'translateX(22px)' : 'translateX(2px)' }}; transition: transform 150ms ease;"></span>
        </span>
    </span>
</label>
