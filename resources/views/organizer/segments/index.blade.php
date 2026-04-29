@extends('layouts.organizer')

@section('title', 'Segments — ' . $hackathon->title)

@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <span class="separator">/</span>
            <a href="{{ route('organizer.hackathons.index') }}">Hackathons</a>
            <span class="separator">/</span>
            <a href="{{ route('organizer.hackathons.show', $hackathon) }}">{{ Str::limit($hackathon->title, 30) }}</a>
            <span class="separator">/</span>
            <span>Segments</span>
        </div>
        <div class="page-header-row">
            <div>
                <h1 class="text-page-title">Segments</h1>
                <p class="page-header-description">Manage segments for {{ $hackathon->title }}.</p>
            </div>
            <a href="{{ route('organizer.hackathons.show', $hackathon) }}" class="btn btn-secondary btn-sm">
                Back to Hackathon
            </a>
        </div>
    </div>

    {{-- Add Segment --}}
    <div class="card section-spacing">
        <div class="card-header">
            <h2 class="text-card-title">Add Segment</h2>
        </div>
        <form method="POST" action="{{ route('organizer.hackathons.segments.store', $hackathon) }}" id="form-add-segment-page">
            @csrf
            <div class="form-grid-2">
                <div class="form-group">
                    <label for="seg_name" class="form-label">Name</label>
                    <input type="text" name="name" id="seg_name"
                           class="form-input @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" placeholder="e.g. AI/ML Track" required>
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label for="seg_desc" class="form-label">Description</label>
                    <input type="text" name="description" id="seg_desc"
                           class="form-input @error('description') is-invalid @enderror"
                           value="{{ old('description') }}" placeholder="Optional description">
                    @error('description') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Add Segment</button>
        </form>
    </div>

    {{-- Segments List --}}
    <div class="card">
        <div class="card-header">
            <h2 class="text-card-title">All Segments ({{ $hackathon->segments->count() }})</h2>
        </div>

        @if ($hackathon->segments->count())
            @foreach ($hackathon->segments as $segment)
                <div x-data="{ editing: false }"
                     style="display:flex; align-items:center; gap:12px; padding:12px 0; {{ !$loop->last ? 'border-bottom:1px solid var(--color-border-subtle);' : '' }}">

                    {{-- View mode --}}
                    <template x-if="!editing">
                        <div style="display:flex; align-items:center; gap:12px; width:100%;">
                            <div style="flex:1;">
                                <div style="font-size:var(--font-size-sm); font-weight:var(--font-weight-medium); color:var(--color-text-primary);">{{ $segment->name }}</div>
                                @if ($segment->description)
                                    <div style="font-size:var(--font-size-xs); color:var(--color-text-muted); margin-top:2px;">{{ $segment->description }}</div>
                                @endif
                            </div>
                            <button @click="editing = true" class="btn-icon" aria-label="Edit {{ $segment->name }}" title="Edit">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M11.333 2A1.886 1.886 0 0 1 14 4.667l-8.667 8.666L2 14l.667-3.333 8.666-8.667Z" stroke="currentColor" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <form method="POST" action="{{ route('organizer.hackathons.segments.destroy', [$hackathon, $segment]) }}"
                                  onsubmit="return confirm('Delete this segment?')" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon" aria-label="Delete {{ $segment->name }}" title="Delete"
                                        style="color:var(--color-danger);">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M2 4h12M5.333 4V2.667a.667.667 0 0 1 .667-.667h4a.667.667 0 0 1 .667.667V4m2 0v9.333a.667.667 0 0 1-.667.667H4a.667.667 0 0 1-.667-.667V4h9.334Z" stroke="currentColor" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </form>
                        </div>
                    </template>

                    {{-- Edit mode --}}
                    <template x-if="editing">
                        <form method="POST" action="{{ route('organizer.hackathons.segments.update', [$hackathon, $segment]) }}"
                              style="display:flex; align-items:center; gap:10px; width:100%;">
                            @csrf @method('PUT')
                            <input type="text" name="name" value="{{ $segment->name }}"
                                   class="form-input" style="flex:1;" required>
                            <input type="text" name="description" value="{{ $segment->description }}"
                                   class="form-input" style="flex:1;" placeholder="Description">
                            <button type="submit" class="btn btn-primary btn-sm">Save</button>
                            <button type="button" @click="editing = false" class="btn btn-secondary btn-sm">Cancel</button>
                        </form>
                    </template>
                </div>
            @endforeach
        @else
            <div class="ds-table-empty">
                <span>No segments. Create one above.</span>
            </div>
        @endif
    </div>
@endsection
