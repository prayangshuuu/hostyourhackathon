@extends('layouts.app')

@section('title', 'Edit Announcement')

@section('content')
    <div class="page-header">
        <div class="page-header-breadcrumb">
            <a href="{{ route('organizer.hackathons.index') }}">My Hackathons</a>
            <span class="separator">/</span>
            <a href="{{ route('organizer.hackathons.show', $hackathon) }}">{{ $hackathon->title }}</a>
            <span class="separator">/</span>
            <a href="{{ route('organizer.announcements.index', $hackathon) }}">Announcements</a>
            <span class="separator">/</span>
            <span>Edit</span>
        </div>
        <div class="page-header-row">
            <h1 class="text-page-title">Edit Announcement</h1>
        </div>
    </div>

    <form method="POST" action="{{ route('organizer.announcements.update', [$hackathon, $announcement]) }}">
        @csrf
        @method('PUT')
        <div class="content-grid-8-4">
            {{-- Left: Form --}}
            <x-card>
                <div style="margin-bottom: 20px;">
                    <label class="form-label" for="title">Title <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="title" id="title" class="form-input @error('title') is-invalid @enderror" value="{{ old('title', $announcement->title) }}" required>
                    @error('title')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom: 20px;">
                    <label class="form-label" for="body">Body <span style="color:var(--danger)">*</span></label>
                    <textarea name="body" id="body" class="form-input @error('body') is-invalid @enderror" style="min-height: 240px;" required>{{ old('body', $announcement->body) }}</textarea>
                    <p class="text-helper" style="margin-top: 6px;">Plain text or HTML</p>
                    @error('body')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom: 20px;">
                    <label class="form-label" for="visibility">Visibility</label>
                    <select name="visibility" id="visibility" class="form-input @error('visibility') is-invalid @enderror">
                        <option value="all" {{ old('visibility', $announcement->visibility) == 'all' ? 'selected' : '' }}>All Participants</option>
                        <option value="registered" {{ old('visibility', $announcement->visibility) == 'registered' ? 'selected' : '' }}>Registered Teams</option>
                        <option value="segment" {{ old('visibility', $announcement->visibility) == 'segment' ? 'selected' : '' }}>Specific Segment</option>
                    </select>
                    @error('visibility')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div id="segment-container" style="display: {{ old('visibility', $announcement->visibility) == 'segment' ? 'block' : 'none' }}; margin-bottom: 20px;">
                    <label class="form-label" for="segment_id">Segment</label>
                    <select name="segment_id" id="segment_id" class="form-input @error('segment_id') is-invalid @enderror">
                        <option value="">Select a segment...</option>
                        @foreach($hackathon->segments as $segment)
                            <option value="{{ $segment->id }}" {{ old('segment_id', $announcement->segment_id) == $segment->id ? 'selected' : '' }}>{{ $segment->name }}</option>
                        @endforeach
                    </select>
                    @error('segment_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </x-card>

            {{-- Right: Sidebar --}}
            <div>
                <x-card title="Publishing">
                    <div style="margin-bottom: 16px;">
                        @php
                            $statusVariant = match($announcement->status) {
                                'draft' => 'neutral',
                                'scheduled' => 'warning',
                                'published' => 'success',
                                default => 'neutral',
                            };
                        @endphp
                        <x-badge :variant="$statusVariant">{{ ucfirst($announcement->status) }}</x-badge>
                    </div>
                    
                    <hr class="form-divider" style="margin: 16px 0;">

                    @if($announcement->status !== 'published')
                        <div style="margin-bottom: 24px;">
                            <label class="form-label" for="scheduled_at">Schedule for later (optional)</label>
                            <input type="datetime-local" name="scheduled_at" id="scheduled_at" class="form-input @error('scheduled_at') is-invalid @enderror" value="{{ old('scheduled_at', $announcement->scheduled_at?->format('Y-m-d\TH:i')) }}">
                            <p class="text-helper" style="margin-top: 6px;">Leave blank to publish immediately</p>
                            @error('scheduled_at')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <input type="hidden" name="status_action" id="status_action" value="publish">

                        <button type="submit" class="btn btn-secondary" style="width: 100%; margin-bottom: 12px;" onclick="document.getElementById('status_action').value='draft'">
                            Save as Draft
                        </button>

                        <button type="submit" id="btn-publish" class="btn btn-primary" style="width: 100%;" onclick="document.getElementById('status_action').value='publish'">
                            Publish Now
                        </button>
                    @else
                        <div style="margin-bottom: 24px;">
                            <p class="text-helper">Published on {{ $announcement->published_at->format('M d, Y h:i A') }}</p>
                        </div>
                        <input type="hidden" name="status_action" value="update">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            Update
                        </button>
                    @endif
                </x-card>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const visibilitySelect = document.getElementById('visibility');
            const segmentContainer = document.getElementById('segment-container');
            const scheduledInput = document.getElementById('scheduled_at');
            const publishBtn = document.getElementById('btn-publish');

            visibilitySelect.addEventListener('change', function() {
                if (this.value === 'segment') {
                    segmentContainer.style.display = 'block';
                } else {
                    segmentContainer.style.display = 'none';
                }
            });

            if (scheduledInput && publishBtn) {
                scheduledInput.addEventListener('input', function() {
                    if (this.value) {
                        publishBtn.textContent = 'Schedule';
                    } else {
                        publishBtn.textContent = 'Publish Now';
                    }
                });

                // Trigger initially
                if (scheduledInput.value) {
                    publishBtn.textContent = 'Schedule';
                }
            }
        });
    </script>
    @endpush
@endsection
