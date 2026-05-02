@extends('layouts.app')

@section('title', $hackathon->title)

@section('content')
    <x-page-header 
        :title="$hackathon->title" 
        :description="$hackathon->tagline"
        :breadcrumbs="['My Hackathons' => route('organizer.hackathons.index'), $hackathon->title => null]"
    >
        <x-slot:actions>
            <x-button :href="route('hackathons.show', $hackathon->slug)" variant="secondary" icon="eye" target="_blank">Public Page</x-button>
            <x-button :href="route('organizer.hackathons.edit', $hackathon)" variant="secondary" icon="pencil-square">Edit</x-button>
            
            <div x-data="{ open: false }" class="relative">
                <x-button @click="open = !open" variant="primary" icon="bolt" iconRight="chevron-down">
                    {{ ucfirst($hackathon->status->value) }}
                </x-button>
                <div x-show="open" @click.away="open = false" class="absolute right-0 top-full mt-2 w-52 bg-white border border-slate-200 rounded-xl overflow-hidden z-50 py-1 shadow-xl">
                    <form method="POST" action="{{ route('organizer.hackathons.status', $hackathon) }}" class="flex flex-col">
                        @csrf
                        @php
                            $statuses = ['published' => 'Publish', 'ongoing' => 'Mark Ongoing', 'ended' => 'End Hackathon', 'archived' => 'Archive', 'draft' => 'Return to Draft'];
                        @endphp
                        @foreach($statuses as $val => $label)
                            @if($hackathon->status->value !== $val)
                                <button name="status" value="{{ $val }}" class="flex items-center px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 {{ $val === 'ended' ? 'text-red-600' : '' }}">
                                    {{ $label }}
                                </button>
                            @endif
                        @endforeach
                    </form>
                </div>
            </div>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-7">
        <x-stat-card icon="users" :value="$teamsCount" label="Registered Teams" />
        <x-stat-card icon="document-text" :value="$submissionsCount" label="Submissions" />
        <x-stat-card icon="star" :value="$judgesCount" label="Judges Assigned" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-6" x-data="{ tab: 'segments' }">
        <div class="space-y-6">
            {{-- Tabs --}}
            <div class="flex items-center gap-8 border-b border-slate-200">
                <button @click="tab = 'segments'" :class="tab === 'segments' ? 'border-accent-500 text-accent-600 font-semibold' : 'border-transparent text-slate-500 font-medium'" class="pb-3 text-sm border-b-2 transition-colors">Segments</button>
                <button @click="tab = 'organizers'" :class="tab === 'organizers' ? 'border-accent-500 text-accent-600 font-semibold' : 'border-transparent text-slate-500 font-medium'" class="pb-3 text-sm border-b-2 transition-colors">Organizers</button>
                <button @click="tab = 'timeline'" :class="tab === 'timeline' ? 'border-accent-500 text-accent-600 font-semibold' : 'border-transparent text-slate-500 font-medium'" class="pb-3 text-sm border-b-2 transition-colors">Timeline</button>
            </div>

            {{-- Segments Tab --}}
            <div x-show="tab === 'segments'" class="space-y-6">
                <x-card title="Hackathon Segments" icon="puzzle-piece" noPadding>
                    <x-slot:actions>
                        <x-button @click="addSegment()" size="sm" variant="primary" icon="plus">Add Segment</x-button>
                    </x-slot:actions>

                    <div class="divide-y divide-slate-100">
                        @foreach ($hackathon->segments as $segment)
                            <div class="flex items-center justify-between p-5 hover:bg-slate-50/50 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="drag-handle text-slate-300"><x-heroicon-o-bars-3 class="w-4 h-4" /></div>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $segment->name }}</p>
                                        <p class="text-2xs text-slate-500 mt-0.5">{{ $segment->teams_count }} teams · {{ $segment->submissions_count }} projects</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <x-button :href="route('organizer.segments.edit', [$hackathon, $segment])" size="sm" variant="ghost">Edit Rules</x-button>
                                    <form method="POST" action="{{ route('organizer.segments.destroy', [$hackathon, $segment]) }}" onsubmit="return confirm('Delete this segment?')">
                                        @csrf @method('DELETE')
                                        <x-button type="submit" size="sm" variant="ghost" class="text-red-600"><x-heroicon-o-trash class="w-4 h-4" /></x-button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                        
                        @if($hackathon->segments->isEmpty())
                            <x-empty-state icon="puzzle-piece" title="No tracks or segments" description="Add tracks like 'AI', 'Fintech', or 'General' to organize submissions." />
                        @endif
                    </div>
                </x-card>
            </div>

            {{-- Organizers Tab --}}
            <div x-show="tab === 'organizers'" class="space-y-6">
                <x-card title="Team & Access" icon="user-group">
                    <form method="POST" action="{{ route('organizer.hackathons.organizers.store', $hackathon) }}" class="flex items-end gap-3 mb-6">
                        @csrf
                        <div class="flex-1"><x-input label="Add Organizer" name="email" placeholder="Email address" required /></div>
                        <div class="mb-5"><x-button type="submit" variant="primary">Add</x-button></div>
                    </form>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3.5 bg-slate-50 border border-slate-200 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-accent-500 text-white flex items-center justify-center text-xs font-bold">{{ strtoupper(substr($hackathon->creator->name, 0, 1)) }}</div>
                                <div>
                                    <p class="text-xs font-semibold text-slate-900">{{ $hackathon->creator->name }}</p>
                                    <p class="text-2xs text-slate-500">{{ $hackathon->creator->email }}</p>
                                </div>
                            </div>
                            <x-badge variant="indigo">Owner</x-badge>
                        </div>
                        @foreach ($hackathon->organizers as $org)
                            <div class="flex items-center justify-between p-3.5 border border-slate-100 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center text-xs font-bold">{{ strtoupper(substr($org->name, 0, 1)) }}</div>
                                    <div>
                                        <p class="text-xs font-semibold text-slate-900">{{ $org->name }}</p>
                                        <p class="text-2xs text-slate-500">{{ $org->email }}</p>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('organizer.hackathons.organizers.destroy', [$hackathon, $org]) }}">
                                    @csrf @method('DELETE')
                                    <x-button type="submit" size="sm" variant="ghost" class="text-red-600">Remove</x-button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </x-card>
            </div>

            {{-- Timeline Tab --}}
            <div x-show="tab === 'timeline'" class="space-y-6">
                <x-card title="Event Schedule" icon="calendar">
                    <div class="space-y-6">
                        @php
                            $dates = [
                                'Registration' => ['open' => $hackathon->registration_opens_at, 'close' => $hackathon->registration_closes_at],
                                'Submission' => ['open' => $hackathon->submission_opens_at, 'close' => $hackathon->submission_closes_at],
                                'Results' => ['date' => $hackathon->results_at],
                            ];
                        @endphp
                        @foreach($dates as $label => $val)
                            <div class="flex gap-4">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400">
                                    <x-heroicon-o-clock class="w-4 h-4" />
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-900 uppercase tracking-tight">{{ $label }}</p>
                                    @if(isset($val['open']))
                                        <p class="text-xs text-slate-500 mt-1">
                                            Opens: <span class="text-slate-700 font-medium">{{ $val['open']?->format('M j, Y — g:i A') ?? 'Not set' }}</span><br>
                                            Closes: <span class="text-slate-700 font-medium">{{ $val['close']?->format('M j, Y — g:i A') ?? 'Not set' }}</span>
                                        </p>
                                    @else
                                        <p class="text-xs text-slate-500 mt-1">
                                            Release: <span class="text-slate-700 font-medium">{{ $val['date']?->format('M j, Y — g:i A') ?? 'Not set' }}</span>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-card>
            </div>
        </div>

        <div class="space-y-5">
            <x-card title="Visibility" icon="globe-alt">
                <div class="space-y-3.5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-500">Current Status</span>
                        <x-badge :variant="$hackathon->status->value === 'ongoing' ? 'success' : 'neutral'">{{ ucfirst($hackathon->status->value) }}</x-badge>
                    </div>
                    <div class="flex items-center justify-between pt-3 border-t border-slate-50">
                        <span class="text-xs text-slate-500">Public Visibility</span>
                        <span class="text-xs font-semibold text-slate-900">{{ $hackathon->status->value === 'draft' ? 'Private' : 'Public' }}</span>
                    </div>
                </div>
            </x-card>

            <x-card title="Danger Zone" icon="exclamation-triangle" class="border-red-100">
                <p class="text-2xs text-slate-500 mb-4 leading-relaxed">Deleting this hackathon will permanently remove all teams, submissions, and scoring data. This cannot be undone.</p>
                <form method="POST" action="{{ route('organizer.hackathons.destroy', $hackathon) }}" onsubmit="return confirm('DELETE PERMANENTLY?')">
                    @csrf @method('DELETE')
                    <x-button type="submit" variant="danger" fullWidth size="lg">Delete Hackathon</x-button>
                </form>
            </x-card>
        </div>
    </div>

    <script>
        function addSegment() {
            const name = prompt('Enter segment name (e.g. AI track, Web Dev):');
            if (!name) return;
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("organizer.segments.store", $hackathon) }}';
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            const nameInp = document.createElement('input');
            nameInp.type = 'hidden';
            nameInp.name = 'name';
            nameInp.value = name;
            form.appendChild(csrf);
            form.appendChild(nameInp);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
@endsection
