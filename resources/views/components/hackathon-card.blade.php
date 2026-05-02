@props(['hackathon'])
<a href="{{ route('hackathons.show', $hackathon->slug) }}" class="group block bg-white border border-slate-200 rounded-xl overflow-hidden hover:border-accent-400 transition-colors duration-150">
  <div class="h-[120px] bg-slate-100 overflow-hidden relative">
    @if($hackathon->banner)
      <img src="{{ Storage::url($hackathon->banner) }}" alt="" class="w-full h-full object-cover">
    @else
      <div class="w-full h-full bg-gradient-to-br from-accent-50 to-slate-100 flex items-center justify-center">
        <x-heroicon-o-trophy class="w-10 h-10 text-accent-200" />
      </div>
    @endif
  </div>
  <div class="p-5 pt-4">
    <div class="flex items-start justify-between gap-3 mb-3">
      <div class="w-10 h-10 rounded-lg border border-slate-200 bg-white flex items-center justify-center overflow-hidden flex-shrink-0 -mt-9 relative z-10">
        @if($hackathon->logo)
          <img src="{{ Storage::url($hackathon->logo) }}" alt="" class="w-full h-full object-cover">
        @else
          <span class="text-sm font-bold text-accent-500">{{ strtoupper(substr($hackathon->title,0,1)) }}</span>
        @endif
      </div>
      <x-badge :variant="match($hackathon->status->value) { 'ongoing'=>'success','published'=>'indigo','ended'=>'neutral','archived'=>'neutral',default=>'neutral' }">
        {{ ucfirst($hackathon->status->value) }}
      </x-badge>
    </div>
    <h3 class="text-base font-semibold text-slate-900 leading-snug group-hover:text-accent-600 transition-colors line-clamp-2">{{ $hackathon->title }}</h3>
    @if($hackathon->tagline)
      <p class="text-xs text-slate-500 mt-1.5 line-clamp-2 leading-relaxed">{{ $hackathon->tagline }}</p>
    @endif
    <div class="flex items-center gap-3 mt-4 pt-4 border-t border-slate-100">
      <div class="flex items-center gap-1 text-2xs text-slate-400">
        <x-heroicon-o-users class="w-3.5 h-3.5" />
        {{ $hackathon->teams_count ?? 0 }} teams
      </div>
      <div class="flex items-center gap-1 text-2xs text-slate-400">
        <x-heroicon-o-puzzle-piece class="w-3.5 h-3.5" />
        {{ $hackathon->segments_count ?? 0 }} segments
      </div>
      @if($hackathon->registration_closes_at && $hackathon->isRegistrationOpen())
        <div class="ml-auto flex items-center gap-1 text-2xs text-accent-600 font-medium">
          <x-heroicon-o-clock class="w-3.5 h-3.5" />
          Closes {{ $hackathon->registration_closes_at->diffForHumans() }}
        </div>
      @endif
    </div>
  </div>
</a>
