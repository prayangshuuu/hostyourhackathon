@props(['id' => null, 'name' => null, 'title' => null, 'confirmLabel' => 'Confirm', 'confirmVariant' => 'primary', 'size' => 'md', 'show' => false, 'focusable' => false])

@php
    $id = $id ?? $name ?? Str::random(8);
    $maxWidth = match($size) {
        'sm' => '440px',
        default => '560px',
    };
@endphp

<div id="{{ $id }}" 
    x-data="{ show: @js($show) }"
    x-init="$watch('show', value => {
        if (value) {
            document.body.classList.add('overflow-y-hidden');
        } else {
            document.body.classList.remove('overflow-y-hidden');
        }
    })"
    x-on:open-modal.window="$event.detail == '{{ $name ?? $id }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name ?? $id }}' ? show = false : null"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    style="display: {{ $show ? 'block' : 'none' }}; background: rgba(0,0,0,0.4); position: fixed; inset: 0; z-index: 100;" 
    class="modal-backdrop">
    
    <div x-show="show" x-on:click.outside="show = false" style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-xl); padding: 28px; max-width: {{ $maxWidth }}; width: 100%; margin: 20vh auto 0; position: relative;">
        
        @if($title)
            <h2 style="font-size: 16px; font-weight: 600; color: var(--text-primary); margin: 0;">{{ $title }}</h2>
        @endif
        
        <div style="{{ $title ? 'margin: 12px 0 24px;' : 'margin: 0;' }} font-size: 14px; color: var(--text-secondary);">
            {{ $slot }}
        </div>
        
        @if($title)
            <div style="display: flex; justify-content: flex-end; gap: 8px;">
                <x-button type="button" variant="ghost" onclick="closeModal('{{ $id }}')" x-on:click="show = false">Cancel</x-button>
                <x-button type="submit" variant="{{ $confirmVariant }}" form="{{ $id }}-form">{{ $confirmLabel }}</x-button>
            </div>
        @endif

    </div>
</div>

<script>
    if (typeof openModal === 'undefined') {
        window.openModal = function(id) {
            document.getElementById(id).style.display = 'block';
            window.dispatchEvent(new CustomEvent('open-modal', {detail: id}));
        };
        
        window.closeModal = function(id) {
            document.getElementById(id).style.display = 'none';
            window.dispatchEvent(new CustomEvent('close-modal', {detail: id}));
        };
    }
</script>
