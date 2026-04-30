@extends('layouts.app')

@section('title', 'Submissions')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h1 class="text-page-title">Submissions</h1>
    </div>

    @if (session('success'))
        <div style="margin-bottom: 24px;"><x-alert type="success" :message="session('success')" /></div>
    @endif
    @if (session('error'))
        <div style="margin-bottom: 24px;"><x-alert type="error" :message="session('error')" /></div>
    @endif

    <form method="GET" action="{{ route('organizer.submissions.index') }}" class="card" style="padding: 16px; margin-bottom: 16px;">
        <label for="hackathon" class="form-label">Hackathon</label>
        <select name="hackathon" id="hackathon" class="form-input" onchange="this.form.submit()">
            <option value="">All</option>
            @foreach ($hackathons as $h)
                <option value="{{ $h->id }}" @selected(request('hackathon') == $h->id)>{{ $h->title }}</option>
            @endforeach
        </select>
    </form>

    <div class="card" style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border);">
                    <th style="text-align: left; padding: 12px; font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Title</th>
                    <th style="text-align: left; padding: 12px; font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Team</th>
                    <th style="text-align: left; padding: 12px; font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Hackathon</th>
                    <th style="text-align: right; padding: 12px; font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($submissions as $submission)
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 12px;">{{ Str::limit($submission->title, 40) }}</td>
                        <td style="padding: 12px; color: var(--text-secondary);">{{ $submission->team->name }}</td>
                        <td style="padding: 12px; color: var(--text-secondary);">{{ $submission->hackathon->title }}</td>
                        <td style="padding: 12px; text-align: right;">
                            <a href="{{ route('organizer.submissions.show', $submission) }}" style="color: var(--accent); font-weight: 500; text-decoration: none;">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="padding: 32px; text-align: center; color: var(--text-secondary);">No submissions found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding: 16px;">{{ $submissions->links() }}</div>
    </div>
@endsection
