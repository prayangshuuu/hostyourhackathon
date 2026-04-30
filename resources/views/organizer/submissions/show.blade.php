@extends('layouts.app')

@section('title', $submission->title)

@section('content')
    <div style="margin-bottom: 16px; font-size: 13px; color: var(--text-muted);">
        <a href="{{ route('organizer.submissions.index') }}">Submissions</a> / {{ Str::limit($submission->title, 50) }}
    </div>

    <h1 class="text-page-title" style="margin-bottom: 8px;">{{ $submission->title }}</h1>
    <p style="color: var(--text-secondary); margin-bottom: 24px;">{{ $submission->team->name }} · {{ $submission->hackathon->title }}</p>

    @if (session('success'))
        <div style="margin-bottom: 24px;"><x-alert type="success" :message="session('success')" /></div>
    @endif
    @if (session('error'))
        <div style="margin-bottom: 24px;"><x-alert type="error" :message="session('error')" /></div>
    @endif

    @if ($submission->disqualified)
        <div class="card" style="padding: 16px; margin-bottom: 24px; border-color: var(--danger);">
            <strong>Disqualified</strong>
            <p style="margin: 8px 0 0;">{{ $submission->disqualified_reason }}</p>
        </div>
    @endif

    <div class="card" style="padding: 24px; margin-bottom: 24px;">
        <p style="margin-bottom: 8px;"><strong>Problem</strong></p>
        <p style="color: var(--text-secondary);">{{ $submission->problem_statement }}</p>
        <p style="margin: 16px 0 8px;"><strong>Description</strong></p>
        <p style="color: var(--text-secondary);">{{ $submission->description }}</p>
    </div>

    <div style="display: flex; flex-wrap: wrap; gap: 12px;">
        @can('reopen', $submission)
            @if ($submission->isFinal())
                <form method="POST" action="{{ route('organizer.submissions.reopen', $submission) }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary">Re-open submission</button>
                </form>
            @endif
        @endcan

        @can('disqualify', $submission)
            @if (! $submission->disqualified)
                <form method="POST" action="{{ route('organizer.submissions.disqualify', $submission) }}" style="display: flex; gap: 8px; align-items: flex-end;">
                    @csrf
                    <div>
                        <label for="reason" class="form-label">Disqualify (reason)</label>
                        <input type="text" name="reason" id="reason" class="form-input" required placeholder="Reason">
                    </div>
                    <button type="submit" class="btn" style="background: var(--danger); color: white; border: none;">Disqualify</button>
                </form>
            @endif
        @endcan
    </div>
@endsection
