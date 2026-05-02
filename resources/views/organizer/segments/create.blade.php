@extends('layouts.app')

@section('title', 'New Segment — ' . $hackathon->title)

@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <span class="separator">/</span>
            <a href="{{ route('organizer.hackathons.show', $hackathon) }}">{{ Str::limit($hackathon->title, 30) }}</a>
            <span class="separator">/</span>
            <a href="{{ route('organizer.segments.index', $hackathon) }}">Segments</a>
            <span class="separator">/</span>
            <span>New Segment</span>
        </div>
        <div class="page-header-row">
            <div>
                <h1 class="text-page-title">New Segment</h1>
                <p class="page-header-description">Create a new track or category for your hackathon.</p>
            </div>
            <a href="{{ route('organizer.segments.index', $hackathon) }}" class="btn btn-secondary btn-sm">
                Cancel
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('organizer.segments.store', $hackathon) }}" enctype="multipart/form-data">
        @csrf
        <div class="form-layout-2col">
            {{-- Left Column --}}
            <div class="form-col-main">
                <div class="card section-spacing">
                    <div class="card-header">
                        <h2 class="text-card-title">Basic Information</h2>
                    </div>
                    <div class="form-group">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-input @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. Fintech Track, Beginners Only">
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-input @error('description') is-invalid @enderror" style="min-height: 120px;" placeholder="What is this segment about?">{{ old('description') }}</textarea>
                        @error('description') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cover Image (Optional)</label>
                        <input type="file" name="cover_image" class="form-input @error('cover_image') is-invalid @enderror" accept="image/*">
                        <p class="form-help">Max 2MB. Recommended 1200x600px.</p>
                        @error('cover_image') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-checkbox-label">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                            <span>Active</span>
                        </label>
                        <p class="form-help">If inactive, participants won't see this segment.</p>
                    </div>
                </div>

                <div class="card section-spacing">
                    <div class="card-header">
                        <h2 class="text-card-title">Rules & Regulations</h2>
                    </div>
                    <div class="form-group">
                        <label for="rules" class="form-label">Rules (Markdown supported)</label>
                        <textarea name="rules" id="rules" class="form-input @error('rules') is-invalid @enderror" style="min-height: 200px;" placeholder="Specific rules for this track...">{{ old('rules') }}</textarea>
                        <p class="form-help">Segment-specific rules override hackathon rules.</p>
                        @error('rules') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="card section-spacing" x-data="{ open: false }">
                    <div class="card-header" @click="open = !open" style="cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                        <h2 class="text-card-title">Timeline Override (Optional)</h2>
                        <svg :class="open ? 'rotate-180' : ''" class="transition-transform" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor"><path d="M5 7l5 5 5-5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div x-show="open" class="card-body">
                        <p class="text-muted" style="font-size: 13px; margin-bottom: 16px;">Leave blank to use hackathon's default dates.</p>
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label class="form-label">Registration Opens</label>
                                <input type="datetime-local" name="registration_opens_at" class="form-input" value="{{ old('registration_opens_at') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Registration Closes</label>
                                <input type="datetime-local" name="registration_closes_at" class="form-input" value="{{ old('registration_closes_at') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Submission Opens</label>
                                <input type="datetime-local" name="submission_opens_at" class="form-input" value="{{ old('submission_opens_at') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Submission Closes</label>
                                <input type="datetime-local" name="submission_closes_at" class="form-input" value="{{ old('submission_closes_at') }}">
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: 16px;">
                            <label class="form-label">Results Date</label>
                            <input type="datetime-local" name="results_at" class="form-input" value="{{ old('results_at') }}">
                        </div>
                    </div>
                </div>

                <div class="card section-spacing" x-data="{ open: false }">
                    <div class="card-header" @click="open = !open" style="cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                        <h2 class="text-card-title">Limits (Optional)</h2>
                        <svg :class="open ? 'rotate-180' : ''" class="transition-transform" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor"><path d="M5 7l5 5 5-5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div x-show="open" class="card-body">
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label class="form-label">Maximum Teams</label>
                                <input type="number" name="max_teams" class="form-input" value="{{ old('max_teams') }}" placeholder="No limit">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Submissions Per Team</label>
                                <input type="number" name="submission_limit" class="form-input" value="{{ old('submission_limit') }}" placeholder="No limit">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Create Segment</button>
                    <a href="{{ route('organizer.segments.index', $hackathon) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>

            {{-- Right Column --}}
            <div class="form-col-side">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-card-title">Rulebook</h2>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Rulebook PDF (Optional)</label>
                        <div class="upload-area" style="text-align: center; padding: 24px; border: 2px dashed var(--color-border); border-radius: 8px;">
                            <svg style="color: var(--color-text-muted); margin-bottom: 8px;" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><polyline points="9 15 12 12 15 15"/></svg>
                            <input type="file" name="rulebook" id="rulebook" accept=".pdf" style="display: none;" onchange="document.getElementById('rulebook-name').textContent = this.files[0].name">
                            <label for="rulebook" class="btn btn-secondary btn-sm" style="display: block; cursor: pointer;">Select PDF</label>
                            <p id="rulebook-name" style="font-size: 12px; margin-top: 8px; color: var(--color-text-secondary);">No file selected (Max 10MB)</p>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h2 class="text-card-title">Hackathon Dates</h2>
                    </div>
                    <div class="card-body" style="font-size: 13px; color: var(--color-text-secondary);">
                        <div style="margin-bottom: 8px;">
                            <strong>Registration:</strong><br>
                            {{ $hackathon->registration_opens_at?->format('M d, H:i') ?? 'N/A' }} - {{ $hackathon->registration_closes_at?->format('M d, H:i') ?? 'N/A' }}
                        </div>
                        <div style="margin-bottom: 8px;">
                            <strong>Submission:</strong><br>
                            {{ $hackathon->submission_opens_at?->format('M d, H:i') ?? 'N/A' }} - {{ $hackathon->submission_closes_at?->format('M d, H:i') ?? 'N/A' }}
                        </div>
                        <div>
                            <strong>Results:</strong><br>
                            {{ $hackathon->results_at?->format('M d, H:i') ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
