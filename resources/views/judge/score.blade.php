@extends('layouts.app')

@section('title', 'Score Submission: ' . $submission->title)

@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-breadcrumb">
            <a href="{{ route('judge.dashboard') }}">Judging</a>
            <span class="separator">/</span>
            <span>{{ $submission->hackathon->title }}</span>
            <span class="separator">/</span>
            <span>{{ $submission->title }}</span>
        </div>
        <div class="page-header-row">
            <h1 class="text-page-title">{{ $submission->title }}</h1>
        </div>
    </div>

    <x-alert />

    <div class="content-grid-7-5">
        {{-- Left: Submission Detail --}}
        <x-card title="Submission Details">
            {{-- Section 1: Project Overview --}}
            <div class="definition-item">
                <div class="definition-label">Project Title</div>
                <div class="definition-value">{{ $submission->title }}</div>
            </div>

            <div class="definition-item">
                <div class="definition-label">Problem Statement</div>
                <div class="definition-value">{{ $submission->problem_statement }}</div>
            </div>

            <div class="definition-item">
                <div class="definition-label">Description</div>
                <div class="definition-value">{{ $submission->description }}</div>
            </div>

            <hr class="form-divider">

            {{-- Section 2: Technical Details --}}
            <div class="definition-item">
                <div class="definition-label">Tech Stack</div>
                <div class="definition-value">{{ $submission->tech_stack }}</div>
            </div>

            <div class="form-grid-2">
                <div class="definition-item">
                    <div class="definition-label">Demo URL</div>
                    <div class="definition-value">
                        @if ($submission->demo_url)
                            <a href="{{ $submission->demo_url }}" target="_blank" rel="noopener">{{ $submission->demo_url }}</a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>
                </div>

                <div class="definition-item">
                    <div class="definition-label">Repository URL</div>
                    <div class="definition-value">
                        @if ($submission->repo_url)
                            <a href="{{ $submission->repo_url }}" target="_blank" rel="noopener">{{ $submission->repo_url }}</a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>
                </div>
            </div>

            <hr class="form-divider">

            {{-- Section 3: Files --}}
            <div>
                <p class="text-card-title" style="margin-bottom:12px;">Attachments</p>
                @if ($submission->files->count())
                    @foreach ($submission->files as $file)
                        <div class="file-row">
                            <svg class="file-row-icon" viewBox="0 0 16 16" fill="none">
                                <path d="M4 1h5.586L13 4.414V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1Z" stroke="currentColor" stroke-width="1.2"/>
                            </svg>
                            <a href="{{ Storage::url($file->file_path) }}" target="_blank" class="file-row-name">{{ $file->original_name }}</a>
                            <span class="file-row-size">{{ number_format($file->file_size_kb, 0) }} KB</span>
                        </div>
                    @endforeach
                @else
                    <p class="text-helper">No files attached.</p>
                @endif
            </div>
        </x-card>

        {{-- Right: Scoring Panel --}}
        <x-card title="Score This Submission">
            @php
                $isScored = isset($score) && $score->submitted_at;
                $action = $isScored ? route('judge.score.update', $submission->id) : route('judge.score.store', $submission->id);
                $method = $isScored ? 'PUT' : 'POST';
                $totalMaxScore = collect($criteria)->sum('max_score');
                $currentTotal = $isScored ? collect($score->scores)->sum('score') : 0;
            @endphp
            <form method="POST" action="{{ $action }}">
                @csrf
                @method($method)

                <div id="criteria-list">
                    @foreach($criteria as $index => $criterion)
                        @php
                            $existingScore = $isScored ? (collect($score->scores)->firstWhere('criterion_id', $criterion->id)['score'] ?? 0) : old("scores.{$criterion->id}.score", 0);
                            $existingRemarks = $isScored ? (collect($score->scores)->firstWhere('criterion_id', $criterion->id)['remarks'] ?? '') : old("scores.{$criterion->id}.remarks", '');
                        @endphp
                        <div class="criterion-block" style="margin-bottom: 20px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <span style="font-size: 14px; font-weight: 500; color: var(--text-primary);">{{ $criterion->name }}</span>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <input type="number" name="scores[{{ $criterion->id }}][score]" class="score-input form-input" value="{{ $existingScore }}" min="0" max="{{ $criterion->max_score }}" style="width: 80px; text-align: right;" required>
                                    <span style="font-size: 12px; color: var(--text-muted);">/ {{ $criterion->max_score }} pts</span>
                                </div>
                            </div>
                            <textarea name="scores[{{ $criterion->id }}][remarks]" class="form-input" style="min-height: 60px; font-size: 13px; margin-top: 8px; width: 100%;" placeholder="Optional remarks...">{{ $existingRemarks }}</textarea>
                            
                            @if(!$loop->last)
                                <div style="border-bottom: 1px solid var(--border-subtle); margin-top: 20px;"></div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Total score preview --}}
                <div style="position: sticky; bottom: 0; background: var(--surface); padding-top: 16px; margin-top: 16px; border-top: 1px solid var(--border);">
                    <div style="text-align: right; font-size: 16px; font-weight: 600; color: var(--accent); margin-bottom: 16px;">
                        Total: <span id="live-total">{{ $currentTotal }}</span> / {{ $totalMaxScore }}
                    </div>

                    @if($isScored)
                        <div style="font-size: 12px; color: var(--text-muted); text-align: center; margin-bottom: 8px;">
                            Submitted at: {{ $score->submitted_at->format('M d, Y h:i A') }}
                        </div>
                    @endif

                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        {{ $isScored ? 'Update Scores' : 'Submit Scores' }}
                    </button>
                </div>
            </form>
        </x-card>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scoreInputs = document.querySelectorAll('.score-input');
            const liveTotal = document.getElementById('live-total');

            function updateTotal() {
                let sum = 0;
                scoreInputs.forEach(input => {
                    sum += parseFloat(input.value) || 0;
                });
                liveTotal.textContent = sum;
            }

            scoreInputs.forEach(input => {
                input.addEventListener('input', updateTotal);
            });
        });
    </script>
    @endpush
@endsection
