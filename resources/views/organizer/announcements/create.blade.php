@extends('layouts.app')

@section('title', 'New Announcement')
@section('meta_description', 'Create a new announcement for ' . $hackathon->title)

@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-breadcrumb">
            <a href="{{ route('organizer.hackathons.index') }}">Hackathons</a>
            <span class="separator">/</span>
            <a href="{{ route('organizer.hackathons.show', $hackathon) }}">{{ $hackathon->title }}</a>
            <span class="separator">/</span>
            <a href="{{ route('organizer.hackathons.announcements.index', $hackathon) }}">Announcements</a>
            <span class="separator">/</span>
            <span>New</span>
        </div>
        <div class="page-header-row">
            <h1 class="text-page-title">New Announcement</h1>
        </div>
    </div>

    <form method="POST" action="{{ route('organizer.hackathons.announcements.store', $hackathon) }}" id="form-announcement">
        @csrf

        <div class="content-grid-8-4">
            {{-- Left Column: Form --}}
            <div class="card">
                <div class="form-group">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" name="title" id="title"
                           class="form-input @error('title') is-invalid @enderror"
                           value="{{ old('title') }}"
                           placeholder="Announcement title" required>
                    @error('title') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="body" class="form-label">Body</label>
                    <textarea name="body" id="body"
                              class="form-textarea @error('body') is-invalid @enderror"
                              style="min-height:200px;"
                              placeholder="Write your announcement…"
                              required>{{ old('body') }}</textarea>
                    @error('body') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="visibility" class="form-label">Visibility</label>
                        <select name="visibility" id="visibility" class="form-select @error('visibility') is-invalid @enderror" required>
                            <option value="all" {{ old('visibility') === 'all' ? 'selected' : '' }}>All</option>
                            <option value="registered" {{ old('visibility') === 'registered' ? 'selected' : '' }}>Registered Participants</option>
                            <option value="segment" {{ old('visibility') === 'segment' ? 'selected' : '' }}>Specific Segment</option>
                        </select>
                        @error('visibility') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group" id="segment-group" style="{{ old('visibility') === 'segment' ? '' : 'display:none;' }}">
                        <label for="segment_id" class="form-label">Segment</label>
                        <select name="segment_id" id="segment_id" class="form-select @error('segment_id') is-invalid @enderror">
                            <option value="">Select a segment</option>
                            @foreach ($hackathon->segments as $segment)
                                <option value="{{ $segment->id }}" {{ old('segment_id') == $segment->id ? 'selected' : '' }}>
                                    {{ $segment->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('segment_id') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="scheduled_at" class="form-label">Schedule For (optional)</label>
                    <input type="datetime-local" name="scheduled_at" id="scheduled_at"
                           class="form-input @error('scheduled_at') is-invalid @enderror"
                           value="{{ old('scheduled_at') }}">
                    @error('scheduled_at') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Right Column: Publishing Sidebar --}}
            <div>
                <div class="card" style="position:sticky; top:32px;">
                    <p class="text-card-title" style="margin-bottom:16px;">Publishing</p>

                    <div style="margin-bottom:16px;">
                        <span class="badge badge-partial">Draft</span>
                    </div>

                    <div style="display:flex; flex-direction:column; gap:8px;">
                        <button type="submit" name="publish" value="1" class="btn btn-primary" style="width:100%;" id="btn-publish">
                            Publish
                        </button>
                        <button type="submit" class="btn btn-secondary" style="width:100%;" id="btn-save-draft">
                            Save Draft
                        </button>
                    </div>

                    <p class="text-helper" style="margin-top:12px;" id="schedule-note" style="display:none;">
                        If a scheduled date is set, the announcement will be delivered at that time.
                    </p>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const visibilitySelect = document.getElementById('visibility');
            const segmentGroup = document.getElementById('segment-group');

            visibilitySelect.addEventListener('change', function () {
                segmentGroup.style.display = this.value === 'segment' ? '' : 'none';
            });
        });
    </script>
    @endpush
@endsection
