@extends('layouts.app')

@section('title', 'Edit Track — ' . $segment->name)

@section('content')
    <x-page-header 
        :title="'Edit Track: ' . $segment->name" 
        description="Update track configuration, rules, and specialized timeline overrides."
        :breadcrumbs="[
            'Dashboard' => route('dashboard'), 
            $hackathon->title => route('organizer.hackathons.show', $hackathon),
            'Tracks' => null,
            'Edit' => null
        ]"
    />

    <form method="POST" action="{{ route('organizer.segments.update', [$hackathon, $segment]) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-6">
            <div class="space-y-6">
                <x-card title="General Configuration" icon="cog-6-tooth">
                    <div class="space-y-6">
                        <x-input label="Track Name" name="name" :value="old('name', $segment->name)" placeholder="e.g. Fintech Track" required />
                        
                        <x-input type="textarea" label="Summary" name="description" placeholder="Briefly describe what this track is about..." rows="3">{{ old('description', $segment->description) }}</x-input>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <x-input type="number" label="Maximum Teams" name="max_teams" :value="old('max_teams', $segment->max_teams)" placeholder="Unlimited" />
                            <x-input type="number" label="Submissions per Team" name="submission_limit" :value="old('submission_limit', $segment->submission_limit)" placeholder="Unlimited" />
                        </div>

                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl">
                            <x-toggle name="is_active" label="Track is active and visible to participants" :checked="old('is_active', $segment->is_active)" />
                        </div>
                    </div>
                </x-card>

                <x-card title="Rules & Guidelines" icon="document-text">
                    <x-input type="textarea" label="Rules (Markdown supported)" name="rules" placeholder="Track-specific rules..." rows="8">{{ old('rules', $segment->rules) }}</x-input>
                    <p class="text-2xs text-slate-400 mt-3 italic">These rules will be displayed specifically for this track and override general hackathon rules.</p>
                </x-card>

                <x-card title="Timeline Overrides" icon="calendar-days" x-data="{ show: {{ $segment->registration_opens_at || $segment->submission_opens_at ? 'true' : 'false' }} }">
                    <div class="flex items-center justify-between mb-6">
                        <p class="text-xs text-slate-500 max-w-md">Enable overrides if this track has a different schedule than the main hackathon.</p>
                        <x-toggle name="use_overrides" label="" x-model="show" />
                    </div>

                    <div x-show="show" x-collapse class="space-y-6 pt-6 border-t border-slate-100">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <x-input type="datetime-local" label="Registration Opens" name="registration_opens_at" :value="old('registration_opens_at', $segment->registration_opens_at?->format('Y-m-d\TH:i'))" />
                            <x-input type="datetime-local" label="Registration Closes" name="registration_closes_at" :value="old('registration_closes_at', $segment->registration_closes_at?->format('Y-m-d\TH:i'))" />
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <x-input type="datetime-local" label="Submission Opens" name="submission_opens_at" :value="old('submission_opens_at', $segment->submission_opens_at?->format('Y-m-d\TH:i'))" />
                            <x-input type="datetime-local" label="Submission Closes" name="submission_closes_at" :value="old('submission_closes_at', $segment->submission_closes_at?->format('Y-m-d\TH:i'))" />
                        </div>
                        <x-input type="datetime-local" label="Results Announcement" name="results_at" :value="old('results_at', $segment->results_at?->format('Y-m-d\TH:i'))" />
                    </div>
                </x-card>

                <div class="flex items-center justify-end gap-3 pt-4">
                    <x-button :href="route('organizer.hackathons.show', $hackathon)" variant="ghost">Cancel</x-button>
                    <x-button type="submit" variant="primary" size="lg">Save Track Settings</x-button>
                </div>
            </div>

            <div class="space-y-5">
                <x-card title="Visuals & Assets" icon="photo">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-2xs font-bold text-slate-700 uppercase tracking-wider mb-3">Cover Image</label>
                            @if ($segment->cover_image)
                                <img src="{{ Storage::url($segment->cover_image) }}" alt="Cover" class="w-full aspect-video rounded-xl object-cover border border-slate-200 mb-4 shadow-sm">
                            @endif
                            <x-input type="file" name="cover_image" accept="image/*" />
                            <p class="text-2xs text-slate-400 mt-2">Max 2MB. Recommended 1200x600px.</p>
                        </div>

                        <div class="pt-6 border-t border-slate-100">
                            <label class="block text-2xs font-bold text-slate-700 uppercase tracking-wider mb-3">Rulebook PDF</label>
                            @if ($segment->rulebook_path)
                                <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-200 rounded-xl mb-4">
                                    <div class="flex items-center gap-2.5">
                                        <x-heroicon-o-document-text class="w-4 h-4 text-slate-400" />
                                        <span class="text-xs font-medium text-slate-700">Rulebook.pdf</span>
                                    </div>
                                    <a href="{{ Storage::url($segment->rulebook_path) }}" target="_blank" class="text-xs font-bold text-accent-600 hover:underline">View</a>
                                </div>
                            @endif
                            <x-input type="file" name="rulebook" accept=".pdf" />
                            <p class="text-2xs text-slate-400 mt-2">Max 10MB. Only PDF allowed.</p>
                        </div>
                    </div>
                </x-card>

                <x-card title="Current Status" icon="clock">
                    <div class="space-y-4">
                        <div>
                            <p class="text-2xs font-bold text-slate-400 uppercase tracking-widest mb-2">Registration</p>
                            <p class="text-xs font-medium text-slate-900">{{ $segment->effectiveRegistrationOpensAt()?->format('M d, H:i') }} - {{ $segment->effectiveRegistrationClosesAt()?->format('M d, H:i') }}</p>
                            <p class="text-[10px] text-accent-600 font-bold mt-1 uppercase">{{ $segment->registration_opens_at ? 'Track Override' : 'Main Schedule' }}</p>
                        </div>
                        <div class="pt-4 border-t border-slate-50">
                            <p class="text-2xs font-bold text-slate-400 uppercase tracking-widest mb-2">Submission</p>
                            <p class="text-xs font-medium text-slate-900">{{ $segment->effectiveSubmissionOpensAt()?->format('M d, H:i') }} - {{ $segment->effectiveSubmissionClosesAt()?->format('M d, H:i') }}</p>
                            <p class="text-[10px] text-accent-600 font-bold mt-1 uppercase">{{ $segment->submission_opens_at ? 'Track Override' : 'Main Schedule' }}</p>
                        </div>
                        <div class="pt-4 border-t border-slate-50">
                            @php
                                $regOpen = $segment->isRegistrationOpen();
                                $subOpen = $segment->isSubmissionOpen();
                            @endphp
                            @if ($subOpen)
                                <x-badge variant="success">Submission Open</x-badge>
                            @elseif ($regOpen)
                                <x-badge variant="indigo">Registration Open</x-badge>
                            @else
                                <x-badge variant="neutral">Closed</x-badge>
                            @endif
                        </div>
                    </div>
                </x-card>
            </div>
        </div>
    </form>
@endsection
