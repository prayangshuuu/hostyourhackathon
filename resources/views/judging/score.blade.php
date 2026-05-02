@extends('layouts.app')

@section('title', 'Score — ' . $submission->title)

@section('content')
    <x-page-header 
        :title="$submission->title" 
        :description="'Team: ' . $submission->team->name"
        :breadcrumbs="[
            'Dashboard' => route('judge.dashboard'), 
            $submission->team->name => null,
            'Score' => null
        ]"
    />

    <div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-6">
        {{-- Left Column: Submission Detail --}}
        <div class="space-y-6">
            <x-card title="Submission Details" icon="document-text">
                <div class="space-y-6">
                    <div>
                        <p class="text-2xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Problem Statement</p>
                        <p class="text-sm text-slate-800 leading-relaxed">{{ $submission->problem_statement }}</p>
                    </div>

                    <div>
                        <p class="text-2xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Description</p>
                        <p class="text-sm text-slate-800 leading-relaxed">{{ $submission->description }}</p>
                    </div>

                    <div>
                        <p class="text-2xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Tech Stack</p>
                        <div class="flex flex-wrap gap-1.5 mt-1.5">
                            @foreach(explode(',', $submission->tech_stack) as $tech)
                                <x-badge variant="indigo">{{ trim($tech) }}</x-badge>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 pt-2">
                        @if ($submission->demo_url)
                            <x-button :href="$submission->demo_url" variant="secondary" size="sm" icon="play" target="_blank">View Demo</x-button>
                        @endif
                        @if ($submission->repo_url)
                            <x-button :href="$submission->repo_url" variant="secondary" size="sm" icon="code-bracket" target="_blank">Repository</x-button>
                        @endif
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100">
                    <h4 class="text-xs font-semibold text-slate-900 mb-4">Attachments</h4>
                    @if ($submission->files->count())
                        <div class="space-y-2">
                            @foreach ($submission->files as $file)
                                <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-200 rounded-lg">
                                    <div class="flex items-center gap-3">
                                        <x-heroicon-o-document class="w-4 h-4 text-slate-400" />
                                        <span class="text-xs font-medium text-slate-700 truncate max-w-[200px]">{{ $file->original_name }}</span>
                                        <span class="text-2xs text-slate-400">{{ number_format($file->file_size_kb, 0) }} KB</span>
                                    </div>
                                    <x-button :href="Storage::url($file->file_path)" variant="ghost" size="sm" icon="arrow-down-tray" target="_blank"></x-button>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-slate-400 italic">No files attached.</p>
                    @endif
                </div>
            </x-card>

            @can('banAsJudge', $submission->team)
                <x-card title="Moderation" icon="exclamation-triangle" class="border-red-100 bg-red-50/30">
                    <p class="text-xs text-slate-600 mb-4">Ban this team if you detect plagiarism or violation of rules.</p>
                    <form method="POST" action="{{ route('judge.teams.ban', $submission->team) }}" class="space-y-3">
                        @csrf
                        <x-input type="textarea" name="reason" placeholder="Reason for ban..." required />
                        <x-button type="submit" variant="danger" size="sm" onclick="return confirm('Ban this team and suspend all members?')">Ban Team</x-button>
                    </form>
                </x-card>
            @endcan
        </div>

        {{-- Right Column: Scoring Panel --}}
        <div>
            <div class="sticky top-[88px] space-y-6">
                <x-card title="Evaluation" icon="star">
                    <form method="POST" action="{{ route('judge.score.store', $submission) }}" class="space-y-6">
                        @csrf

                        @foreach ($criteria as $criterion)
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <label class="text-2xs font-bold text-slate-700 uppercase tracking-wider">{{ $criterion->name }}</label>
                                    <span class="text-2xs font-semibold text-slate-400">MAX {{ $criterion->max_score }}</span>
                                </div>

                                <x-input 
                                    type="number" 
                                    name="scores[{{ $criterion->id }}][score]"
                                    min="0"
                                    max="{{ $criterion->max_score }}"
                                    value="{{ old('scores.'.$criterion->id.'.score', $existingScores->get($criterion->id)?->score ?? '') }}"
                                    class="score-input"
                                    required
                                    :disabled="!$canScore"
                                />

                                <x-input 
                                    type="textarea" 
                                    name="scores[{{ $criterion->id }}][remarks]"
                                    placeholder="Add remarks..."
                                    rows="2"
                                    class="min-h-[60px]"
                                    :disabled="!$canScore"
                                >{{ old('scores.'.$criterion->id.'.remarks', $existingScores->get($criterion->id)?->remarks ?? '') }}</x-input>
                            </div>
                        @endforeach

                        <div class="pt-4 border-t border-slate-100">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-sm font-semibold text-slate-900">Total Score</span>
                                <span id="total-score" class="text-2xl font-bold text-accent-600">0</span>
                            </div>

                            @if ($canScore)
                                <x-button type="submit" variant="primary" fullWidth size="lg">Save Scores</x-button>
                            @else
                                <div class="p-3 bg-slate-100 border border-slate-200 rounded-lg text-center">
                                    <p class="text-xs text-slate-500 font-medium">Scoring is closed</p>
                                </div>
                            @endif
                        </div>
                    </form>
                </x-card>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const inputs = document.querySelectorAll('.score-input input');
            const totalEl = document.getElementById('total-score');

            function updateTotal() {
                let total = 0;
                inputs.forEach(function (input) {
                    const val = parseInt(input.value, 10);
                    if (!isNaN(val)) total += val;
                });
                totalEl.textContent = total;
            }

            inputs.forEach(function (input) {
                input.addEventListener('input', updateTotal);
            });

            updateTotal();
        });
    </script>
    @endpush
@endsection
