@extends('layouts.judge')

@section('title', 'Score — ' . $submission->title)
@section('meta_description', 'Score submission: ' . $submission->title)

@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-breadcrumb">
            <a href="{{ route('judging.dashboard') }}">Dashboard</a>
            <span class="separator">/</span>
            <span>{{ $submission->team->name }}</span>
            <span class="separator">/</span>
            <span>Score</span>
        </div>
        <div class="page-header-row">
            <h1 class="text-page-title">{{ $submission->title }}</h1>
        </div>
    </div>

    <div class="content-grid-8-4">
        {{-- Left Column: Submission Detail (read-only) --}}
        <div class="card">
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

            <div class="definition-item">
                <div class="definition-label">Tech Stack</div>
                <div class="definition-value">{{ $submission->tech_stack }}</div>
            </div>

            {{-- Links as clickable chips --}}
            <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:20px;">
                @if ($submission->demo_url)
                    <a href="{{ $submission->demo_url }}" target="_blank" rel="noopener" class="link-chip">
                        <svg width="12" height="12" viewBox="0 0 16 16" fill="none"><path d="M6.667 8.667a3.333 3.333 0 0 0 5.026.36l2-2a3.334 3.334 0 0 0-4.713-4.714L7.667 3.627" stroke="currentColor" stroke-width="1.33" stroke-linecap="round"/><path d="M9.333 7.333a3.333 3.333 0 0 0-5.026-.36l-2 2a3.334 3.334 0 0 0 4.713 4.714l1.313-1.314" stroke="currentColor" stroke-width="1.33" stroke-linecap="round"/></svg>
                        Demo
                    </a>
                @endif
                @if ($submission->repo_url)
                    <a href="{{ $submission->repo_url }}" target="_blank" rel="noopener" class="link-chip">
                        <svg width="12" height="12" viewBox="0 0 16 16" fill="none"><path d="M6 14s-1-.5-1-2 .5-2.5 0-3c-.5-.5-2-1-2-3s1.5-2.5 2-2.5c.5 0 1.5.5 2 .5h2c.5 0 1.5-.5 2-.5s2 .5 2 2.5-1.5 2.5-2 3c-.5.5 0 1 0 3s-1 2-1 2" stroke="currentColor" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Repository
                    </a>
                @endif
            </div>

            <hr class="form-divider">

            {{-- Files --}}
            <div>
                <p class="text-card-title" style="margin-bottom:12px;">Attachments</p>
                @if ($submission->files->count())
                    @foreach ($submission->files as $file)
                        <div class="file-row">
                            <svg class="file-row-icon" viewBox="0 0 16 16" fill="none">
                                @if ($file->file_type === 'pdf')
                                    <path d="M4 1h5.586L13 4.414V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1Z" stroke="currentColor" stroke-width="1.2"/>
                                    <path d="M5 9h6M5 11h4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                                @else
                                    <path d="M4 1h5.586L13 4.414V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1Z" stroke="currentColor" stroke-width="1.2"/>
                                    <path d="M5 8l2 3 2-2 2 3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                @endif
                            </svg>
                            <span class="file-row-name">{{ $file->original_name }}</span>
                            <span class="file-row-size">{{ number_format($file->file_size / 1024, 0) }} KB</span>
                        </div>
                    @endforeach
                @else
                    <p class="text-helper">No files attached.</p>
                @endif
            </div>
        </div>

        {{-- Right Column: Scoring Panel --}}
        <div>
            <div class="card" style="position:sticky; top:32px;">
                <p class="text-card-title" style="margin-bottom:16px;">Scoring</p>

                <form method="POST" action="{{ route('judging.score.store', $submission) }}" id="form-score">
                    @csrf

                    @foreach ($criteria as $criterion)
                        <div class="criterion-block">
                            <div class="criterion-header">
                                <span class="criterion-name">{{ $criterion->name }}</span>
                                <span class="criterion-max">/ {{ $criterion->max_score }}</span>
                            </div>

                            <input type="number"
                                   name="scores[{{ $criterion->id }}][score]"
                                   class="form-input score-input @error("scores.{$criterion->id}.score") is-invalid @enderror"
                                   min="0"
                                   max="{{ $criterion->max_score }}"
                                   value="{{ old("scores.{$criterion->id}.score", $existingScores->get($criterion->id)?->score ?? '') }}"
                                   data-max="{{ $criterion->max_score }}"
                                   placeholder="0"
                                   {{ $canScore ? '' : 'readonly' }}
                                   required>
                            @error("scores.{$criterion->id}.score")
                                <p class="form-error">{{ $message }}</p>
                            @enderror

                            <textarea name="scores[{{ $criterion->id }}][remarks]"
                                      class="form-textarea"
                                      style="min-height:60px; margin-top:8px;"
                                      placeholder="Remarks (optional)"
                                      {{ $canScore ? '' : 'readonly' }}>{{ old("scores.{$criterion->id}.remarks", $existingScores->get($criterion->id)?->remarks ?? '') }}</textarea>
                        </div>
                    @endforeach

                    {{-- Total Score Preview --}}
                    <div class="scoring-total">
                        <span class="scoring-total-label">Total Score</span>
                        <span class="scoring-total-value" id="total-score">0</span>
                    </div>

                    @if ($canScore)
                        <button type="submit" class="btn btn-primary" style="width:100%; margin-top:16px;" id="btn-submit-scores">
                            Save Scores
                        </button>
                    @else
                        <p class="text-helper" style="text-align:center; margin-top:16px;">
                            Scoring is closed — results have been published.
                        </p>
                    @endif
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const inputs = document.querySelectorAll('.score-input');
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
