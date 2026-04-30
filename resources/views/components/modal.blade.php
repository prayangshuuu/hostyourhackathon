@props(['id' => null, 'name' => null, 'title' => null, 'confirmLabel' => 'Confirm', 'confirmVariant' => 'primary', 'size' => 'md', 'show' => false, 'focusable' => false])

@php
    $id = $id ?? $name ?? Str::random(8);
    $maxWidth = '440px';
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
    style="display: {{ $show ? 'block' : 'none' }}; background: rgba(0,0,0,0.35); position: fixed; inset: 0; z-index: 100;" 
    class="modal-backdrop">
    
    <div x-show="show" x-on:click.outside="show = false" style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-xl); padding: 0; max-width: {{ $maxWidth }}; width: calc(100% - 24px); margin: 15vh auto 0; position: relative;">
        
        @if($title)
            <div style="padding: 20px 24px; border-bottom: 1px solid var(--border); position: relative;">
                <h2 style="font-size: 16px; line-height: 1.4; font-weight: 600; color: var(--text-primary); margin: 0;">{{ $title }}</h2>
                <button type="button" x-on:click="show = false" style="position: absolute; top: 16px; right: 16px; width: 30px; height: 30px; border: none; background: transparent; color: var(--text-secondary); border-radius: var(--radius-md); display: inline-flex; align-items: center; justify-content: center; cursor: pointer;">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                </button>
            </div>
        @endif
        
        <div style="padding: 20px 24px; font-size: 14px; line-height: 1.6; color: var(--text-secondary);">
            {{ $slot }}
        </div>
        
        @if($title)
            <div style="padding: 16px 24px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 8px;">
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
