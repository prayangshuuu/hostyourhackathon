@props(['title', 'description' => null, 'breadcrumbs' => []])
<div class="mb-7 pb-5 border-b border-slate-200">
  @if(count($breadcrumbs))
    <nav class="flex items-center gap-1.5 mb-2">
      @foreach($breadcrumbs as $label => $url)
        @if($url)
          <a href="{{ $url }}" class="text-2xs text-slate-400 hover:text-slate-600 transition-colors">{{ $label }}</a>
          <x-heroicon-o-chevron-right class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" />
        @else
          <span class="text-2xs text-slate-500 font-medium">{{ $label }}</span>
        @endif
      @endforeach
    </nav>
  @endif
  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-xl font-bold text-slate-900 leading-tight">{{ $title }}</h1>
      @if($description)<p class="text-xs text-slate-500 mt-1.5">{{ $description }}</p>@endif
    </div>
    @isset($actions)
      <div class="flex items-center gap-2 flex-shrink-0">{{ $actions }}</div>
    @endisset
  </div>
</div>
