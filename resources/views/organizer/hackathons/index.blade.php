@extends('layouts.organizer')

@section('title', 'Hackathons')
@section('meta_description', 'Manage all your hackathons')

@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <span class="separator">/</span>
            <span>Hackathons</span>
        </div>
        <div class="page-header-row">
            <div>
                <h1 class="text-page-title">Hackathons</h1>
                <p class="page-header-description">Create and manage your hackathon events.</p>
            </div>
            <a href="{{ route('organizer.hackathons.create') }}" class="btn btn-primary" id="btn-create-hackathon">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 3.333v9.334M3.333 8h9.334" stroke="currentColor" stroke-width="1.33" stroke-linecap="round"/></svg>
                Create Hackathon
            </a>
        </div>
    </div>

    {{-- Hackathons Table --}}
    @if ($hackathons->count())
        <div class="ds-table-wrapper">
            <table class="ds-table" id="hackathons-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Teams</th>
                        <th>Segments</th>
                        <th>Created</th>
                        <th style="text-align:right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($hackathons as $hackathon)
                        <tr>
                            <td>
                                <a href="{{ route('organizer.hackathons.show', $hackathon) }}"
                                   style="color:var(--color-text-primary); text-decoration:none; font-weight:var(--font-weight-medium);">
                                    {{ $hackathon->title }}
                                </a>
                                @if ($hackathon->tagline)
                                    <div style="font-size:var(--font-size-xs); color:var(--color-text-muted); margin-top:2px;">
                                        {{ Str::limit($hackathon->tagline, 50) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $hackathon->status->value }}">
                                    {{ ucfirst($hackathon->status->value) }}
                                </span>
                            </td>
                            <td>{{ $hackathon->teams_count }}</td>
                            <td>{{ $hackathon->segments_count }}</td>
                            <td style="color:var(--color-text-secondary);">
                                {{ $hackathon->created_at->format('M d, Y') }}
                            </td>
                            <td>
                                <div class="inline-actions" style="justify-content:flex-end;">
                                    <a href="{{ route('organizer.hackathons.show', $hackathon) }}"
                                       class="btn-icon" aria-label="View {{ $hackathon->title }}" title="View">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M1.333 8s2.334-4.667 6.667-4.667S14.667 8 14.667 8s-2.334 4.667-6.667 4.667S1.333 8 1.333 8Z" stroke="currentColor" stroke-width="1.33" stroke-linecap="round"/><circle cx="8" cy="8" r="2" stroke="currentColor" stroke-width="1.33"/></svg>
                                    </a>
                                    <a href="{{ route('organizer.hackathons.edit', $hackathon) }}"
                                       class="btn-icon" aria-label="Edit {{ $hackathon->title }}" title="Edit">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M11.333 2A1.886 1.886 0 0 1 14 4.667l-8.667 8.666L2 14l.667-3.333 8.666-8.667Z" stroke="currentColor" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('organizer.hackathons.destroy', $hackathon) }}"
                                          onsubmit="return confirm('Are you sure you want to delete this hackathon?')"
                                          style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-icon" aria-label="Delete {{ $hackathon->title }}" title="Delete"
                                                style="color:var(--color-danger);">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M2 4h12M5.333 4V2.667a.667.667 0 0 1 .667-.667h4a.667.667 0 0 1 .667.667V4m2 0v9.333a.667.667 0 0 1-.667.667H4a.667.667 0 0 1-.667-.667V4h9.334Z" stroke="currentColor" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div style="margin-top:24px;">
            {{ $hackathons->links() }}
        </div>
    @else
        <div class="card">
            <div class="ds-table-empty">
                <div style="text-align:center;">
                    <p style="margin-bottom:12px;">No hackathons yet. Create your first one to get started.</p>
                    <a href="{{ route('organizer.hackathons.create') }}" class="btn btn-primary btn-sm">
                        Create Hackathon
                    </a>
                </div>
            </div>
        </div>
    @endif
@endsection
