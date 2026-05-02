@props(['name', 'label', 'description' => null, 'checked' => false, 'value' => '1'])
<div class="flex items-start justify-between gap-4 py-3.5 border-b border-slate-100 last:border-0">
  <div>
    <p class="text-sm font-medium text-slate-900">{{ $label }}</p>
    @if($description)<p class="text-xs text-slate-500 mt-0.5 leading-relaxed">{{ $description }}</p>@endif
  </div>
  <label class="flex-shrink-0 relative cursor-pointer">
    <input type="checkbox" class="toggle-input sr-only" name="{{ $name }}" value="{{ $value }}" {{ $checked ? 'checked' : '' }}>
    <span class="toggle-track"></span>
  </label>
</div>
