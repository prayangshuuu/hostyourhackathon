@extends('layouts.app')

@section('title', 'Scoring Criteria')
@section('meta_description', 'Manage scoring criteria for ' . $hackathon->title)

@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-breadcrumb">
            <a href="{{ route('organizer.hackathons.index') }}">Hackathons</a>
            <span class="separator">/</span>
            <a href="{{ route('organizer.hackathons.show', $hackathon) }}">{{ $hackathon->title }}</a>
            <span class="separator">/</span>
            <span>Scoring Criteria</span>
        </div>
        <div class="page-header-row">
            <h1 class="text-page-title">Scoring Criteria</h1>
        </div>
    </div>

    <div class="card" style="max-width:720px;">
        <div class="card-header">
            <h2 class="text-card-title">Criteria List</h2>
        </div>

        {{-- Existing criteria --}}
        @foreach ($hackathon->scoringCriteria as $criterion)
            <form method="POST"
                  action="{{ route('organizer.hackathons.criteria.update', [$hackathon, $criterion]) }}"
                  class="criteria-row">
                @csrf
                @method('PUT')

                <input type="text" name="name"
                       class="form-input" style="flex:1;"
                       value="{{ $criterion->name }}"
                       placeholder="Criterion name" required>

                <input type="number" name="max_score"
                       class="form-input score-input"
                       value="{{ $criterion->max_score }}"
                       min="1" max="100"
                       placeholder="Max" required>

                <button type="submit" class="btn btn-secondary btn-sm">Save</button>

                {{-- Delete --}}
                <button type="button" class="btn-icon" title="Delete criterion"
                        onclick="if(confirm('Delete this criterion?')) { this.closest('.criteria-row').querySelector('.delete-form').submit(); }">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                        <path d="M2 4h12M5.333 4V2.667a1.333 1.333 0 0 1 1.334-1.334h2.666a1.333 1.333 0 0 1 1.334 1.334V4m2 0v9.333a1.333 1.333 0 0 1-1.334 1.334H4.667a1.333 1.333 0 0 1-1.334-1.334V4h9.334Z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </form>

            {{-- Hidden delete form --}}
            <form method="POST"
                  action="{{ route('organizer.hackathons.criteria.destroy', [$hackathon, $criterion]) }}"
                  class="delete-form" style="display:none;">
                @csrf
                @method('DELETE')
            </form>
        @endforeach

        @if ($hackathon->scoringCriteria->isEmpty())
            <p class="text-helper" style="padding:16px 0;">No criteria defined yet. Add one below.</p>
        @endif

        {{-- Add new criterion --}}
        <form method="POST"
              action="{{ route('organizer.hackathons.criteria.store', $hackathon) }}"
              style="display:flex; align-items:center; gap:12px; padding-top:16px; border-top:1px solid var(--color-border-subtle); margin-top:8px;">
            @csrf

            <input type="text" name="name"
                   class="form-input @error('name') is-invalid @enderror"
                   style="flex:1;"
                   value="{{ old('name') }}"
                   placeholder="New criterion name" required>

            <input type="number" name="max_score"
                   class="form-input score-input @error('max_score') is-invalid @enderror"
                   value="{{ old('max_score', 10) }}"
                   min="1" max="100"
                   placeholder="Max" required>

            <button type="submit" class="btn btn-secondary btn-sm" id="btn-add-criterion">
                <svg width="12" height="12" viewBox="0 0 16 16" fill="none"><path d="M8 3.333v9.334M3.333 8h9.334" stroke="currentColor" stroke-width="1.33" stroke-linecap="round"/></svg>
                Add
            </button>
        </form>

        @if ($errors->any())
            <div style="margin-top:12px;">
                @foreach ($errors->all() as $error)
                    <p class="form-error">{{ $error }}</p>
                @endforeach
            </div>
        @endif
    </div>
@endsection
