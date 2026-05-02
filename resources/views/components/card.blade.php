@props([
  'title' => null,
  'description' => null,
  'icon' => null,
  'noPadding' => false,
])
<div {{ $attributes->merge(['class' => 'bg-white border border-slate-200 rounded-xl overflow-hidden']) }}>
  @if($title)
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100">
      <div>
        <h3 class="flex items-center gap-2 text-base font-semibold text-slate-900">
          @if($icon)<x-dynamic-component :component="'heroicon-o-'.$icon" class="w-4 h-4 text-slate-400" />@endif
          {{ $title }}
        </h3>
        @if($description)<p class="text-2xs text-slate-500 mt-0.5">{{ $description }}</p>@endif
      </div>
      @isset($actions)<div class="flex items-center gap-2">{{ $actions }}</div>@endisset
    </div>
  @endif
  <div class="{{ $noPadding ? '' : 'p-5' }}">{{ $slot }}</div>
  @isset($footer)
    <div class="flex items-center justify-end gap-2 px-5 py-3.5 border-t border-slate-100 bg-slate-50">{{ $footer }}</div>
  @endisset
</div>
