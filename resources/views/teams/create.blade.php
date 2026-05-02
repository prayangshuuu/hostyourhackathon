@extends('layouts.app')

@section('title', 'Create Team')

@section('content')
    <x-page-header 
        title="Create Team" 
        :description="'Register a team for ' . $hackathon->title"
        :breadcrumbs="['Dashboard' => route('dashboard'), 'Teams' => route('teams.index'), 'Create' => null]"
    />

    <div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-6">
        <div class="space-y-6">
            <x-card title="Team Details" icon="user-group">
                <form method="POST" action="{{ route('teams.store', $hackathon) }}" class="space-y-6">
                    @csrf

                    <x-input 
                        label="Team Name" 
                        name="name" 
                        :value="old('name')" 
                        :error="$errors->first('name')" 
                        placeholder="e.g. Code Crusaders" 
                        required 
                        autofocus 
                    />

                    @if ($hackathon->hasSegments())
                        <div>
                            <label class="block text-2xs font-semibold text-slate-500 uppercase tracking-wide mb-3">
                                Select Track / Segment <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach ($hackathon->segments()->active()->get() as $segment)
                                    @php $isFull = $segment->isFull(); @endphp
                                    <label class="relative block group cursor-{{ $isFull ? 'not-allowed' : 'pointer' }}">
                                        <input type="radio" name="segment_id" value="{{ $segment->id }}" 
                                               class="peer sr-only"
                                               {{ old('segment_id') == $segment->id ? 'checked' : '' }}
                                               {{ $isFull ? 'disabled' : '' }}>
                                        <div class="h-full p-4 border rounded-xl transition-all duration-150 {{ $isFull ? 'bg-slate-50 border-slate-100 opacity-60' : 'bg-white border-slate-200 group-hover:border-accent-400 peer-checked:border-accent-500 peer-checked:ring-2 peer-checked:ring-accent-500/10 peer-checked:bg-accent-50/30' }}">
                                            <div class="flex items-start justify-between mb-2">
                                                <p class="text-sm font-bold {{ $isFull ? 'text-slate-400' : 'text-slate-900 group-hover:text-accent-600 peer-checked:text-accent-700' }}">{{ $segment->name }}</p>
                                                @if ($isFull)
                                                    <x-badge variant="danger">Full</x-badge>
                                                @endif
                                            </div>
                                            @if ($segment->description)
                                                <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed mb-3">{{ $segment->description }}</p>
                                            @endif
                                            <div class="flex items-center gap-1.5 text-[11px] font-medium text-slate-400 uppercase tracking-tight">
                                                <x-heroicon-o-users class="w-3 h-3" />
                                                {{ $segment->teams_count }} teams joined
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('segment_id') <p class="text-2xs text-red-500 mt-2">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <x-button :href="route('teams.index')" variant="ghost">Cancel</x-button>
                        <x-button type="submit" variant="primary">Create Team</x-button>
                    </div>
                </form>
            </x-card>
        </div>

        <div class="space-y-5">
            <x-card title="Hackathon Rules" icon="information-circle">
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 flex-shrink-0">
                            <x-heroicon-o-users class="w-4 h-4" />
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-900">Team Size</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $hackathon->min_team_size }}–{{ $hackathon->max_team_size }} members</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 flex-shrink-0">
                            <x-heroicon-o-user class="w-4 h-4" />
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-900">Solo Participation</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $hackathon->allow_solo ? 'Allowed' : 'Not allowed' }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-accent-50 border border-accent-100 rounded-xl">
                    <p class="text-2xs text-accent-700 leading-relaxed font-medium">
                        Ensure your team name is appropriate. You can add team members after creating the team.
                    </p>
                </div>
            </x-card>
        </div>
    </div>
@endsection
