@props(['id', 'title', 'confirmLabel' => 'Confirm', 'confirmVariant' => 'primary', 'size' => 'md'])

@php
    $maxWidth = match($size) {
        'sm' => '440px',
        default => '560px',
    };
@endphp

<div id="{{ $id }}" style="display: none; background: rgba(0,0,0,0.4); position: fixed; inset: 0; z-index: 100;" class="modal-backdrop">
    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-xl); padding: 28px; max-width: {{ $maxWidth }}; width: 100%; margin: 20vh auto 0; position: relative;">
        
        <h2 style="font-size: 16px; font-weight: 600; color: var(--text-primary); margin: 0;">{{ $title }}</h2>
        
        <div style="margin: 12px 0 24px; font-size: 14px; color: var(--text-secondary);">
            {{ $slot }}
        </div>
        
        <div style="display: flex; justify-content: flex-end; gap: 8px;">
            <x-button type="button" variant="ghost" onclick="closeModal('{{ $id }}')">Cancel</x-button>
            <x-button type="submit" variant="{{ $confirmVariant }}" form="{{ $id }}-form">{{ $confirmLabel }}</x-button>
        </div>

    </div>
</div>

<script>
    if (typeof openModal === 'undefined') {
        window.openModal = function(id) {
            document.getElementById(id).style.display = 'block';
        };
        
        window.closeModal = function(id) {
            document.getElementById(id).style.display = 'none';
        };
    }
</script>
