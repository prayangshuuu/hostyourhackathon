@extends('layouts.participant')

@section('title', 'Create Submission')
@section('meta_description', 'Submit your idea for ' . $hackathon->title)

@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <span class="separator">/</span>
            <a href="{{ route('teams.index') }}">Teams</a>
            <span class="separator">/</span>
            <span>{{ $hackathon->title }}</span>
            <span class="separator">/</span>
            <span>Submission</span>
        </div>
        <div class="page-header-row">
            <div style="display:flex; align-items:center; gap:12px;">
                <h1 class="text-page-title">Your Submission</h1>
                <span class="badge badge-draft">Draft</span>
            </div>
        </div>
    </div>

    @if (! $isLeader)
        <div class="alert alert-warning" role="alert">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M14.267 12.467 8.8 2.8a.933.933 0 0 0-1.6 0L1.733 12.467a.867.867 0 0 0 .8 1.2h10.934a.867.867 0 0 0 .8-1.2ZM8 11.333a.667.667 0 1 1 0-1.333.667.667 0 0 1 0 1.333Zm.667-3.333a.667.667 0 0 1-1.334 0V5.333a.667.667 0 0 1 1.334 0V8Z" fill="currentColor"/></svg>
            Only the team leader can create and submit ideas.
        </div>
    @endif

    <div class="content-grid-8-4">
        {{-- Left Column: Form --}}
        <div class="card">
            <form method="POST" action="{{ route('submissions.store', $hackathon) }}" id="form-create-submission">
                @csrf

                {{-- Section 1: Basic Info --}}
                <div class="form-group">
                    <label for="title" class="form-label">Project Title</label>
                    <input type="text" name="title" id="title"
                           class="form-input @error('title') is-invalid @enderror"
                           value="{{ old('title') }}"
                           placeholder="e.g. EcoTrack — Carbon Footprint Monitor"
                           {{ $isLeader ? '' : 'disabled' }} required>
                    @error('title') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="problem_statement" class="form-label">Problem Statement</label>
                    <textarea name="problem_statement" id="problem_statement"
                              class="form-textarea @error('problem_statement') is-invalid @enderror"
                              placeholder="What problem are you solving?"
                              {{ $isLeader ? '' : 'disabled' }} required>{{ old('problem_statement') }}</textarea>
                    @error('problem_statement') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description"
                              class="form-textarea @error('description') is-invalid @enderror"
                              placeholder="Describe your solution in detail…"
                              {{ $isLeader ? '' : 'disabled' }} required>{{ old('description') }}</textarea>
                    @error('description') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <hr class="form-divider">

                {{-- Section 2: Technical --}}
                <div class="form-group">
                    <label for="tech_stack" class="form-label">Tech Stack</label>
                    <textarea name="tech_stack" id="tech_stack"
                              class="form-textarea @error('tech_stack') is-invalid @enderror"
                              placeholder="e.g. React, Node.js, PostgreSQL, Docker"
                              style="min-height:80px;"
                              {{ $isLeader ? '' : 'disabled' }} required>{{ old('tech_stack') }}</textarea>
                    @error('tech_stack') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="demo_url" class="form-label">Demo URL</label>
                        <input type="url" name="demo_url" id="demo_url"
                               class="form-input @error('demo_url') is-invalid @enderror"
                               value="{{ old('demo_url') }}"
                               placeholder="https://demo.example.com"
                               {{ $isLeader ? '' : 'disabled' }}>
                        @error('demo_url') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label for="repo_url" class="form-label">Repository URL</label>
                        <input type="url" name="repo_url" id="repo_url"
                               class="form-input @error('repo_url') is-invalid @enderror"
                               value="{{ old('repo_url') }}"
                               placeholder="https://github.com/team/project"
                               {{ $isLeader ? '' : 'disabled' }}>
                        @error('repo_url') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <hr class="form-divider">

                {{-- Section 3: Files (upload after draft is saved) --}}
                <div>
                    <p class="text-card-title" style="margin-bottom:12px;">Attachments</p>
                    <div class="upload-zone" style="opacity:0.5; pointer-events:none;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" style="margin:0 auto 8px; display:block; color:var(--color-text-muted);">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4m4-5 5-5 5 5m-5-5v12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <p class="upload-zone-text">Save as draft first to upload files</p>
                        <p class="upload-zone-hint">PDF, PPT, PPTX — max {{ config('hackathon.submission.max_file_size_kb', 10240) / 1024 }} MB</p>
                    </div>
                </div>

                @if ($isLeader)
                    <div style="display:flex; gap:8px; justify-content:flex-end; padding-top:24px;">
                        <button type="submit" class="btn btn-primary" id="btn-save-draft">Save Draft</button>
                    </div>
                @endif
            </form>
        </div>

        {{-- Right Column: Sidebar --}}
        <div>
            <div class="card" style="position:sticky; top:32px;">
                <p class="text-card-title" style="margin-bottom:16px;">Submission Deadline</p>

                @if ($hackathon->submission_closes_at)
                    <x-countdown :deadline="$hackathon->submission_closes_at" />
                @else
                    <p class="text-helper" style="text-align:center;">No deadline set</p>
                @endif

                {{-- Window indicator --}}
                @php
                    $windowOpen = true;
                    $now = now();
                    if ($hackathon->submission_opens_at && $now->lt($hackathon->submission_opens_at)) $windowOpen = false;
                    if ($hackathon->submission_closes_at && $now->gt($hackathon->submission_closes_at)) $windowOpen = false;
                @endphp
                <div class="window-indicator">
                    <span class="window-dot {{ $windowOpen ? 'window-dot-open' : 'window-dot-closed' }}"></span>
                    <span style="color:var(--color-text-secondary);">
                        {{ $windowOpen ? 'Submission window open' : 'Submission window closed' }}
                    </span>
                </div>

                @if ($isLeader && $windowOpen)
                    <div style="display:flex; flex-direction:column; gap:8px; margin-top:16px;">
                        <button type="submit" form="form-create-submission" class="btn btn-secondary" id="btn-sidebar-save">
                            Save Draft
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
