@extends('layouts.app')

@section('title', 'Edit Submission')
@section('meta_description', 'Edit your submission for ' . $hackathon->title)

@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <span class="separator">/</span>
            <a href="{{ route('teams.index') }}">Teams</a>
            <span class="separator">/</span>
            <a href="{{ route('teams.show', $team) }}">{{ $team->name }}</a>
            <span class="separator">/</span>
            <span>Submission</span>
        </div>
        <div class="page-header-row">
            <div style="display:flex; align-items:center; gap:12px;">
                <h1 class="text-page-title">Your Submission</h1>
                @if ($submission->isFinal())
                    <span class="badge badge-submitted">Submitted</span>
                @else
                    <span class="badge badge-draft">Draft</span>
                @endif
            </div>
        </div>
    </div>

    @if (! $isLeader && ! $submission->isFinal())
        <div class="alert alert-warning" role="alert">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M14.267 12.467 8.8 2.8a.933.933 0 0 0-1.6 0L1.733 12.467a.867.867 0 0 0 .8 1.2h10.934a.867.867 0 0 0 .8-1.2ZM8 11.333a.667.667 0 1 1 0-1.333.667.667 0 0 1 0 1.333Zm.667-3.333a.667.667 0 0 1-1.334 0V5.333a.667.667 0 0 1 1.334 0V8Z" fill="currentColor"/></svg>
            Only the team leader can edit and submit ideas.
        </div>
    @endif

    <div class="content-grid-8-4">
        {{-- Left Column: Form --}}
        <div class="card">
            <form method="POST" action="{{ route('submissions.update', $submission) }}" id="form-edit-submission">
                @csrf
                @method('PUT')

                @php
                    $readonly = $submission->isFinal() || ! $isLeader;
                @endphp

                {{-- Section 1: Basic Info --}}
                <div class="form-group">
                    <label for="title" class="form-label">Project Title</label>
                    <input type="text" name="title" id="title"
                           class="form-input @error('title') is-invalid @enderror"
                           value="{{ old('title', $submission->title) }}"
                           placeholder="e.g. EcoTrack — Carbon Footprint Monitor"
                           {{ $readonly ? 'readonly' : '' }} required>
                    @error('title') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="problem_statement" class="form-label">Problem Statement</label>
                    <textarea name="problem_statement" id="problem_statement"
                              class="form-textarea @error('problem_statement') is-invalid @enderror"
                              placeholder="What problem are you solving?"
                              {{ $readonly ? 'readonly' : '' }} required>{{ old('problem_statement', $submission->problem_statement) }}</textarea>
                    @error('problem_statement') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description"
                              class="form-textarea @error('description') is-invalid @enderror"
                              placeholder="Describe your solution in detail…"
                              {{ $readonly ? 'readonly' : '' }} required>{{ old('description', $submission->description) }}</textarea>
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
                              {{ $readonly ? 'readonly' : '' }} required>{{ old('tech_stack', $submission->tech_stack) }}</textarea>
                    @error('tech_stack') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="demo_url" class="form-label">Demo URL</label>
                        <input type="url" name="demo_url" id="demo_url"
                               class="form-input @error('demo_url') is-invalid @enderror"
                               value="{{ old('demo_url', $submission->demo_url) }}"
                               placeholder="https://demo.example.com"
                               {{ $readonly ? 'readonly' : '' }}>
                        @error('demo_url') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label for="repo_url" class="form-label">Repository URL</label>
                        <input type="url" name="repo_url" id="repo_url"
                               class="form-input @error('repo_url') is-invalid @enderror"
                               value="{{ old('repo_url', $submission->repo_url) }}"
                               placeholder="https://github.com/team/project"
                               {{ $readonly ? 'readonly' : '' }}>
                        @error('repo_url') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                @if (! $readonly)
                    <div style="display:flex; gap:8px; justify-content:flex-end; padding-top:8px;">
                        <button type="submit" class="btn btn-primary" id="btn-update-draft">Save Draft</button>
                    </div>
                @endif
            </form>

            <hr class="form-divider">

            {{-- Section 3: Files --}}
            <div>
                <p class="text-card-title" style="margin-bottom:12px;">Attachments</p>

                {{-- Upload Zone (only if editable) --}}
                @if ($submission->isEditable() && $isLeader)
                    <form method="POST" action="{{ route('submissions.files.store', $submission) }}" enctype="multipart/form-data" id="form-upload-file">
                        @csrf
                        <label for="file-upload" class="upload-zone" id="upload-drop-zone">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" style="margin:0 auto 8px; display:block; color:var(--color-text-muted);">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4m4-5 5-5 5 5m-5-5v12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <p class="upload-zone-text">Drag files here or click to upload</p>
                            <p class="upload-zone-hint">PDF, PPT, PPTX — max {{ config('hackathon.submission.max_file_size_kb', 10240) / 1024 }} MB</p>
                            <input type="file" name="file" id="file-upload" style="display:none;"
                                   accept=".pdf,.ppt,.pptx">
                        </label>
                    </form>
                @endif

                {{-- Uploaded File List --}}
                @if ($submission->files->count())
                    <div style="margin-top:16px;">
                        @foreach ($submission->files as $file)
                            <div class="file-row">
                                {{-- File type icon --}}
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

                                @if ($submission->isEditable() && $isLeader)
                                    <form method="POST" action="{{ route('submissions.files.destroy', $file) }}" style="margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon" aria-label="Delete file" title="Delete file">
                                            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                                                <path d="M2 4h12M5.333 4V2.667a1.333 1.333 0 0 1 1.334-1.334h2.666a1.333 1.333 0 0 1 1.334 1.334V4m2 0v9.333a1.333 1.333 0 0 1-1.334 1.334H4.667a1.333 1.333 0 0 1-1.334-1.334V4h9.334Z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @elseif ($submission->isFinal())
                    <p class="text-helper">No files attached.</p>
                @endif
            </div>
        </div>

        {{-- Right Column: Sidebar --}}
        <div>
            <div class="card" style="position:sticky; top:32px;">
                @if ($submission->isFinal())
                    {{-- Finalized state --}}
                    <div style="text-align:center; margin-bottom:16px;">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" style="color:var(--color-success); margin-bottom:8px;">
                            <circle cx="16" cy="16" r="14" stroke="currentColor" stroke-width="2"/>
                            <path d="M10 16l4 4 8-8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <p class="text-card-title" style="color:var(--color-success);">Submitted</p>
                        <p class="text-helper" style="margin-top:4px;">
                            {{ $submission->submitted_at->format('M d, Y · h:i A') }}
                        </p>
                    </div>
                @else
                    <p class="text-card-title" style="margin-bottom:16px;">Submission Deadline</p>

                    @if ($hackathon->submission_closes_at)
                        <x-countdown :deadline="$hackathon->submission_closes_at" />
                    @else
                        <p class="text-helper" style="text-align:center;">No deadline set</p>
                    @endif

                    {{-- Window indicator --}}
                    @php
                        $windowOpen = $submission->isWindowOpen();
                    @endphp
                    <div class="window-indicator">
                        <span class="window-dot {{ $windowOpen ? 'window-dot-open' : 'window-dot-closed' }}"></span>
                        <span style="color:var(--color-text-secondary);">
                            {{ $windowOpen ? 'Submission window open' : 'Submission window closed' }}
                        </span>
                    </div>

                    @if ($isLeader && $windowOpen)
                        <div style="display:flex; flex-direction:column; gap:8px; margin-top:16px;">
                            <button type="submit" form="form-edit-submission" class="btn btn-secondary" id="btn-sidebar-save">
                                Save Draft
                            </button>
                            <button type="button" class="btn btn-primary" id="btn-finalize" onclick="document.getElementById('finalize-modal').classList.add('is-open')">
                                Finalize Submission
                            </button>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- Finalize Confirmation Modal --}}
    @if ($submission->isEditable() && $isLeader)
        <div class="modal-overlay" id="finalize-modal">
            <div class="modal-box">
                <h3 class="modal-title">Finalize Submission?</h3>
                <p class="modal-body">
                    Once finalized, your submission cannot be edited unless an organizer re-opens it.
                    Make sure everything is complete before proceeding.
                </p>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('finalize-modal').classList.remove('is-open')">
                        Cancel
                    </button>
                    <form method="POST" action="{{ route('submissions.submit', $submission) }}" style="margin:0;">
                        @csrf
                        <button type="submit" class="btn btn-primary">Yes, Finalize</button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
    <script>
        // File upload auto-submit
        const fileInput = document.getElementById('file-upload');
        if (fileInput) {
            fileInput.addEventListener('change', function () {
                if (this.files.length) {
                    document.getElementById('form-upload-file').submit();
                }
            });
        }

        // Drag and drop
        const dropZone = document.getElementById('upload-drop-zone');
        if (dropZone) {
            ['dragenter', 'dragover'].forEach(evt => {
                dropZone.addEventListener(evt, function (e) {
                    e.preventDefault();
                    dropZone.classList.add('is-dragover');
                });
            });
            ['dragleave', 'drop'].forEach(evt => {
                dropZone.addEventListener(evt, function (e) {
                    e.preventDefault();
                    dropZone.classList.remove('is-dragover');
                });
            });
            dropZone.addEventListener('drop', function (e) {
                const files = e.dataTransfer.files;
                if (files.length) {
                    fileInput.files = files;
                    document.getElementById('form-upload-file').submit();
                }
            });
        }

        // Close modal on overlay click
        const modal = document.getElementById('finalize-modal');
        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    modal.classList.remove('is-open');
                }
            });
        }
    </script>
    @endpush
@endsection
