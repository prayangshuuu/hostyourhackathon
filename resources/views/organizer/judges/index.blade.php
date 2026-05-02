@extends('layouts.app')

@section('title', 'Judges')

@section('content')
    <x-page-header 
        title="Judges" 
        :description="'Manage judging panel for ' . $hackathon->title"
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
                            <th class="px-5 h-[38px] text-2xs font-semibold text-slate-400 text-left uppercase tracking-[0.06em]">Email</th>
                            <th class="px-5 h-[38px] text-2xs font-semibold text-slate-400 text-left uppercase tracking-[0.06em]">Assigned Segment</th>
                            <th class="px-5 h-[38px]"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($judges as $judge)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 h-[48px] text-sm font-semibold text-slate-900">{{ $judge->name }}</td>
                                <td class="px-5 h-[48px] text-sm text-slate-600">{{ $judge->email }}</td>
                                <td class="px-5 h-[48px]">
                                    @if($judge->pivot->segment_id)
                                        <x-badge variant="indigo">{{ $hackathon->segments->find($judge->pivot->segment_id)?->name ?? 'Unknown Segment' }}</x-badge>
                                    @else
                                        <x-badge variant="neutral">All Segments</x-badge>
                                    @endif
                                </td>
                                <td class="px-5 h-[48px] text-right">
                                    <form action="{{ route('organizer.judges.destroy', [$hackathon->id, $judge->id]) }}" method="POST" onsubmit="return confirm('Remove this judge?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <x-button type="submit" variant="ghost" size="sm" class="text-red-600"><x-heroicon-o-trash class="w-4 h-4" /></x-button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <x-empty-state icon="star" title="No judges assigned yet" description="Assign users as judges to evaluate submissions." />
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
                    <form method="POST" action="{{ route('organizer.judges.store', $hackathon->id) }}" class="space-y-0">
                        @csrf
                        
                        <x-input label="User Email" name="email" type="email" :value="old('email')" :error="$errors->first('email')" required />

                        <x-input label="Assign to Segment" name="segment_id" type="select">
                            <option value="">All Segments</option>
                            @foreach($hackathon->segments as $segment)
                                <option value="{{ $segment->id }}" {{ old('segment_id') == $segment->id ? 'selected' : '' }}>
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
