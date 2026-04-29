@extends('layouts.app')

@section('title', 'Create Hackathon')

@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <span class="separator">/</span>
            <a href="{{ route('organizer.hackathons.index') }}">Hackathons</a>
            <span class="separator">/</span>
            <span>Create</span>
        </div>
        <div class="page-header-row">
            <div>
                <h1 class="text-page-title">Create Hackathon</h1>
                <p class="page-header-description">Set up a new hackathon event.</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('organizer.hackathons.store') }}" enctype="multipart/form-data" id="form-create-hackathon">
        @csrf
        @include('organizer.hackathons._form')

        <div style="display:flex; gap:12px; align-items:center;">
            <button type="submit" class="btn btn-primary" id="btn-submit-hackathon">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 3.333v9.334M3.333 8h9.334" stroke="currentColor" stroke-width="1.33" stroke-linecap="round"/></svg>
                Create Hackathon
            </button>
            <a href="{{ route('organizer.hackathons.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
@endsection
