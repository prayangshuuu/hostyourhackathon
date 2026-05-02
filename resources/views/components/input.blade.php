@props([
  'label' => null,
  'name' => '',
  'type' => 'text',
  'error' => null,
  'hint' => null,
  'required' => false,
])
<div class="mb-5">
  @if($label)
    <label for="{{ $name }}" class="block text-2xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">
      {{ $label }}@if($required)<span class="text-red-500 ml-0.5">*</span>@endif
    </label>
  @endif
  @if($type === 'textarea')
    <textarea
      id="{{ $name }}"
      name="{{ $name }}"
      class="form-textarea block w-full px-3 py-2.5 text-sm text-slate-900 bg-white border {{ $error ? 'border-red-400 focus:border-red-400 focus:ring-red-500/10' : 'border-slate-200 focus:border-accent-500 focus:ring-accent-500/15' }} rounded-md placeholder:text-slate-400 focus:outline-none focus:ring-3 transition-colors duration-100 resize-y min-h-[96px]"
      {{ $attributes }}>{{ $slot }}</textarea>
  @elseif($type === 'select')
    <select
      id="{{ $name }}"
      name="{{ $name }}"
      class="form-select block w-full h-[36px] pl-3 pr-8 text-sm text-slate-900 bg-white border {{ $error ? 'border-red-400 focus:border-red-400 focus:ring-red-500/10' : 'border-slate-200 focus:border-accent-500 focus:ring-accent-500/15' }} rounded-md focus:outline-none focus:ring-3 transition-colors duration-100"
      {{ $attributes }}>{{ $slot }}</select>
  @else
    <input
      type="{{ $type }}"
      id="{{ $name }}"
      name="{{ $name }}"
      class="form-input block w-full h-[36px] px-3 text-sm text-slate-900 bg-white border {{ $error ? 'border-red-400 focus:border-red-400 focus:ring-red-500/10' : 'border-slate-200 focus:border-accent-500 focus:ring-accent-500/15' }} rounded-md placeholder:text-slate-400 focus:outline-none focus:ring-3 transition-colors duration-100"
      {{ $attributes }}>
  @endif
  @if($error)
    <p class="text-2xs text-red-500 mt-1">{{ $error }}</p>
  @elseif($hint)
    <p class="text-2xs text-slate-400 mt-1">{{ $hint }}</p>
  @endif
</div>
