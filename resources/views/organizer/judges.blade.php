@extends('layouts.organizer')

@section('title', 'Judge Assignments')
@section('meta_description', 'Manage judge assignments for ' . $hackathon->title)

@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-breadcrumb">
            <a href="{{ route('organizer.hackathons.index') }}">Hackathons</a>
            <span class="separator">/</span>
            <a href="{{ route('organizer.hackathons.show', $hackathon) }}">{{ $hackathon->title }}</a>
            <span class="separator">/</span>
            <span>Judges</span>
        </div>
        <div class="page-header-row">
            <h1 class="text-page-title">Judge Assignments</h1>
        </div>
    </div>

    <div class="content-grid-8-4">
        {{-- Left Column: Assigned Judges Table --}}
        <div class="ds-table-wrapper">
            <table class="ds-table" id="judges-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Segment</th>
                        <th style="width:60px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($hackathon->judges as $judge)
                        <tr>
                            <td>{{ $judge->user->name }}</td>
                            <td style="color:var(--color-text-secondary);">{{ $judge->user->email }}</td>
                            <td>
                                @if ($judge->segment)
                                    <span class="segment-pill">{{ $judge->segment->name }}</span>
                                @else
                                    <span class="text-muted">All segments</span>
                                @endif
                            </td>
                            <td>
                                <form method="POST"
                                      action="{{ route('organizer.hackathons.judges.destroy', [$hackathon, $judge]) }}"
                                      style="margin:0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon" title="Remove judge"
                                            onclick="return confirm('Remove this judge?')"
                                            style="color:var(--color-danger);">
                                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                                            <path d="M2 4h12M5.333 4V2.667a1.333 1.333 0 0 1 1.334-1.334h2.666a1.333 1.333 0 0 1 1.334 1.334V4m2 0v9.333a1.333 1.333 0 0 1-1.334 1.334H4.667a1.333 1.333 0 0 1-1.334-1.334V4h9.334Z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="ds-table-empty">No judges assigned yet.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Right Column: Add Judge Form --}}
        <div class="card" style="position:sticky; top:32px;">
            <p class="text-card-title" style="margin-bottom:16px;">Assign Judge</p>

            <form method="POST" action="{{ route('organizer.hackathons.judges.store', $hackathon) }}" id="form-add-judge">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" name="email" id="email"
                           class="form-input @error('email') is-invalid @enderror"
                           value="{{ old('email') }}"
                           placeholder="judge@example.com" required>
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="segment_id" class="form-label">Segment</label>
                    <select name="segment_id" id="segment_id" class="form-select @error('segment_id') is-invalid @enderror">
                        <option value="">All segments</option>
                        @foreach ($hackathon->segments as $segment)
                            <option value="{{ $segment->id }}" {{ old('segment_id') == $segment->id ? 'selected' : '' }}>
                                {{ $segment->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('segment_id') <p class="form-error">{{ $message }}</p> @enderror
                    <p class="form-helper">Leave blank to assign across all segments.</p>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;" id="btn-assign-judge">Assign</button>
            </form>
        </div>
    </div>
@endsection
