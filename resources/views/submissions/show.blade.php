@extends('layouts.app')

@section('title', $submission->title)
@section('meta_description', 'Submission details for ' . $submission->title)

@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <span class="separator">/</span>
            <a href="{{ route('teams.index') }}">Teams</a>
            <span class="separator">/</span>
            <a href="{{ route('teams.show', $submission->team) }}">{{ $submission->team->name }}</a>
            <span class="separator">/</span>
            <span>Submission</span>
        </div>
        <div class="page-header-row">
            <div style="display:flex; align-items:center; gap:12px;">
                <h1 class="text-page-title">{{ $submission->title }}</h1>
                @if ($submission->isFinal())
                    <span class="badge badge-submitted">Submitted</span>
                @else
                    <span class="badge badge-draft">Draft</span>
                @endif
            </div>
            @if ($isOrganizer && $submission->isFinal())
                <form method="POST" action="{{ route('submissions.reopen', $submission) }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary" id="btn-reopen">Re-open Submission</button>
                </form>
            @endif
        </div>
    </div>

    <div class="content-grid-8-4">
        {{-- Left Column: Content --}}
        <div class="card">
            {{-- Section 1: Basic Info --}}
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

            {{-- Section 2: Technical --}}
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
                                @if ($file->file_type === 'pdf')
                                    <path d="M4 1h5.586L13 4.414V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1Z" stroke="currentColor" stroke-width="1.2"/>
                                    <path d="M5 9h6M5 11h4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                                @else
                                    <path d="M4 1h5.586L13 4.414V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1Z" stroke="currentColor" stroke-width="1.2"/>
                                    <path d="M5 8l2 3 2-2 2 3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                @endif
                            </svg>
                            <span class="file-row-name">{{ $file->original_name }}</span>
                            <span class="file-row-size">{{ number_format($file->file_size_kb, 0) }} KB</span>
                        </div>
                    @endforeach
                @else
                    <p class="text-helper">No files attached.</p>
                @endif
            </div>
        </div>

        {{-- Right Column: Sidebar --}}
        <div>
            <div class="card" style="position:sticky; top:32px;">
                @if ($submission->isFinal())
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
                    <p class="text-card-title" style="margin-bottom:16px;">Submission Status</p>
                    <p class="text-helper" style="text-align:center;">This submission is still a draft.</p>
                @endif

                {{-- Team Info --}}
                <div style="border-top:1px solid var(--color-border-subtle); padding-top:16px; margin-top:16px;">
                    <p class="text-helper" style="margin-bottom:8px;">Team</p>
                    <a href="{{ route('teams.show', $submission->team) }}" style="font-size:var(--font-size-sm); color:var(--color-accent); text-decoration:none;">
                        {{ $submission->team->name }}
                    </a>
                </div>

                {{-- Hackathon Info --}}
                <div style="border-top:1px solid var(--color-border-subtle); padding-top:16px; margin-top:16px;">
                    <p class="text-helper" style="margin-bottom:8px;">Hackathon</p>
                    <p style="font-size:var(--font-size-sm); color:var(--color-text-primary);">
                        {{ $submission->hackathon->title }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
