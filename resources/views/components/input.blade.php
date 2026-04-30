@props(['label', 'name', 'type' => 'text', 'value' => '', 'error' => null, 'hint' => null, 'required' => false])

<div style="margin-bottom: 20px;">
    @if($label)
        <label for="{{ $name }}" style="font-size: 13px; font-weight: 500; line-height: 1.5; color: var(--text-secondary); margin-bottom: 6px; display: block;">
            {{ $label }} @if($required)<span style="color: var(--danger);"> *</span>@endif
        </label>
    @endif

    <input 
        type="{{ $type }}" 
        id="{{ $name }}" 
        name="{{ $name }}" 
        value="{{ old($name, $value) }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes }}
        class="input"
        style="border-color: {{ $errors->has($name) || $error ? 'var(--danger)' : 'var(--border)' }};"
        onfocus="this.style.borderColor='var(--accent)'; this.style.outline='none'; this.style.boxShadow='0 0 0 3px var(--accent-ring)';"
        onblur="this.style.borderColor='{{ $errors->has($name) || $error ? 'var(--danger)' : 'var(--border)' }}'; this.style.boxShadow='none';"
    >

    @if($errors->has($name) || $error)
        <div style="font-size: 12px; color: var(--danger); margin-top: 4px;">
            {{ $error ?? $errors->first($name) }}
        </div>
    @endif

    @if($hint)
        <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">
            {{ $hint }}
        </div>
    @endif
</div>
