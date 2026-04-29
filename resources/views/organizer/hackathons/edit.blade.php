@extends('layouts.organizer')

@section('title', 'Edit — ' . $hackathon->title)

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
            <span>Edit</span>
        </div>
        <div class="page-header-row">
            <div>
                <h1 class="text-page-title">Edit Hackathon</h1>
                <p class="page-header-description">Update settings for {{ $hackathon->title }}.</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('organizer.hackathons.update', $hackathon) }}" enctype="multipart/form-data" id="form-edit-hackathon">
        @csrf
        @method('PUT')
        @include('organizer.hackathons._form')

        <div style="display:flex; gap:12px; align-items:center;">
            <button type="submit" class="btn btn-primary" id="btn-update-hackathon">Save Changes</button>
            <a href="{{ route('organizer.hackathons.show', $hackathon) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
@endsection
