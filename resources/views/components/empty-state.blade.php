@props(['icon' => 'inbox', 'title', 'description' => null])
<div class="flex flex-col items-center justify-center py-14 px-6 text-center">
  <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 mx-auto mb-3.5">
    <x-dynamic-component :component="'heroicon-o-'.$icon" class="w-6 h-6" />
  </div>
  <p class="text-base font-semibold text-slate-800 mt-1">{{ $title }}</p>
  @if($description)<p class="text-xs text-slate-500 mt-1.5 max-w-xs leading-relaxed">{{ $description }}</p>@endif
  @isset($action)<div class="mt-5">{{ $action }}</div>@endisset
</div>
