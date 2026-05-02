@extends('layouts.app')

@section('title', 'Judges')

@section('content')
    <x-page-header 
        title="Judge Assignments" 
        :description="'Manage the judging panel for ' . $hackathon->title"
        :breadcrumbs="[
            'My Hackathons' => route('organizer.hackathons.index'), 
            $hackathon->title => route('organizer.hackathons.show', $hackathon),
            'Judges' => null
        ]"
    />

    <div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-6">
        {{-- Left: Assigned Judges --}}
        <x-card title="Assigned Judges" icon="star" noPadding>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-5 h-[38px] text-2xs font-semibold text-slate-400 text-left uppercase tracking-[0.06em]">Judge</th>
                            <th class="px-5 h-[38px] text-2xs font-semibold text-slate-400 text-left uppercase tracking-[0.06em]">Track</th>
                            <th class="px-5 h-[38px]"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($hackathon->judges as $assignment)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 h-[48px]">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $assignment->user->name }}</p>
                                        <p class="text-2xs text-slate-500">{{ $assignment->user->email }}</p>
                                    </div>
                                </td>
                                <td class="px-5 h-[48px]">
                                    @if($assignment->segment)
                                        <x-badge variant="indigo">{{ $assignment->segment->name }}</x-badge>
                                    @else
                                        <x-badge variant="neutral">All Tracks</x-badge>
                                    @endif
                                </td>
                                <td class="px-5 h-[48px] text-right">
                                    <form action="{{ route('organizer.hackathons.judges.destroy', [$hackathon, $assignment]) }}" method="POST" onsubmit="return confirm('Remove this judge?');" class="inline">
                                        @csrf @method('DELETE')
                                        <x-button type="submit" variant="ghost" size="sm" class="text-red-600"><x-heroicon-o-trash class="w-4 h-4" /></x-button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
                                    <x-empty-state icon="star" title="No judges assigned" description="Assign users as judges to evaluate submissions." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        {{-- Right: Add Judge --}}
        <div>
            <div class="sticky top-[88px]">
                <x-card title="Assign Judge" icon="user-plus">
                    <form method="POST" action="{{ route('organizer.hackathons.judges.store', $hackathon) }}" class="space-y-0">
                        @csrf
                        
                        <x-input label="User Email" name="email" type="email" :value="old('email')" :error="$errors->first('email')" required />

                        <x-input label="Assign to Track (Optional)" name="segment_id" type="select">
                            <option value="">All Tracks</option>
                            @foreach($hackathon->segments as $segment)
                                <option value="{{ $segment->id }}" @selected(old('segment_id') == $segment->id)>
                                    {{ $segment->name }}
                                </option>
                            @endforeach
                        </x-input>

                        <div class="pt-2">
                            <x-button type="submit" variant="primary" fullWidth size="lg">Assign Judge</x-button>
                        </div>
                    </form>
                </x-card>
            </div>
        </div>
    </div>
@endsection
