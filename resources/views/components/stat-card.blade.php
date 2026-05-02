@props(['icon', 'value', 'label', 'trend' => null])
<div class="bg-white border border-slate-200 rounded-xl p-5">
  <div class="w-10 h-10 rounded-lg bg-accent-50 flex items-center justify-center text-accent-500">
    <x-dynamic-component :component="'heroicon-o-'.$icon" class="w-5 h-5" />
  </div>
  <p class="text-[30px] font-bold text-slate-900 mt-3.5 leading-none">{{ $value }}</p>
  <p class="text-xs text-slate-500 mt-1.5">{{ $label }}</p>
</div>
