@extends('layouts.app')

@section('title', $segment->name . ' — ' . $hackathon->title)

@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <span class="separator">/</span>
            <a href="{{ route('organizer.hackathons.show', $hackathon) }}">{{ Str::limit($hackathon->title, 30) }}</a>
            <span class="separator">/</span>
            <a href="{{ route('organizer.segments.index', $hackathon) }}">Segments</a>
            <span class="separator">/</span>
            <span>{{ $segment->name }}</span>
        </div>
        <div class="page-header-row">
            <div style="display: flex; align-items: center; gap: 12px;">
                <h1 class="text-page-title">{{ $segment->name }}</h1>
                @if ($segment->is_active)
                    <span class="badge badge-success">Active</span>
                @else
                    <span class="badge badge-neutral">Inactive</span>
                @endif
                @php
                    $regOpen = $segment->isRegistrationOpen();
                    $subOpen = $segment->isSubmissionOpen();
                @endphp
                @if ($subOpen)
                    <span class="badge badge-success">Submission Open</span>
                @elseif ($regOpen)
                    <span class="badge badge-primary">Registration Open</span>
                @else
                    <span class="badge badge-neutral">Closed</span>
                @endif
            </div>
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('organizer.segments.edit', [$hackathon, $segment]) }}" class="btn btn-secondary btn-sm">
                    Edit Segment
                </a>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div x-data="{ tab: 'overview' }" class="tabs-container">
        <div class="tabs-header">
            <button @click="tab = 'overview'" :class="tab === 'overview' ? 'active' : ''" class="tab-btn">Overview</button>
            <button @click="tab = 'teams'" :class="tab === 'teams' ? 'active' : ''" class="tab-btn">Teams ({{ $segment->teams->count() }})</button>
            <button @click="tab = 'submissions'" :class="tab === 'submissions' ? 'active' : ''" class="tab-btn">Submissions ({{ $segment->submissions->whereNotNull('submitted_at')->count() }})</button>
            <button @click="tab = 'judges'" :class="tab === 'judges' ? 'active' : ''" class="tab-btn">Judges ({{ $segment->judges->count() }})</button>
            <button @click="tab = 'criteria'" :class="tab === 'criteria' ? 'active' : ''" class="tab-btn">Criteria</button>
            <button @click="tab = 'faqs'" :class="tab === 'faqs' ? 'active' : ''" class="tab-btn">FAQs</button>
            <button @click="tab = 'sponsors'" :class="tab === 'sponsors' ? 'active' : ''" class="tab-btn">Sponsors</button>
        </div>

        {{-- Overview Tab --}}
        <div x-show="tab === 'overview'" class="tab-content">
            <div class="form-layout-2col">
                <div class="form-col-main">
                    @if ($segment->description)
                        <div class="card section-spacing">
                            <div class="card-header"><h3 class="text-card-title">Description</h3></div>
                            <div class="card-body">{{ $segment->description }}</div>
                        </div>
                    @endif

                    @if ($segment->rules)
                        <div class="card section-spacing">
                            <div class="card-header"><h3 class="text-card-title">Rules</h3></div>
                            <div class="card-body prose">{!! nl2br(e($segment->rules)) !!}</div>
                        </div>
                    @endif

                    <div class="card section-spacing">
                        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                            <h3 class="text-card-title">Prizes</h3>
                            <button @click="$dispatch('open-modal', 'add-prize')" class="btn btn-secondary btn-xs">+ Add Prize</button>
                        </div>
                        <div class="ds-table-container">
                            <table class="ds-table">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Title</th>
                                        <th>Amount</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($segment->prizeRecords as $prize)
                                        <tr>
                                            <td><span style="font-weight: 500;">{{ $prize->rank }}</span></td>
                                            <td>{{ $prize->title }}</td>
                                            <td>{{ $prize->amount }}</td>
                                            <td class="text-right">
                                                <form method="POST" action="{{ route('organizer.segments.prizes.destroy', [$hackathon, $segment, $prize]) }}">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn-icon text-danger"><svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor"><path d="M2 4h12M5 4V2h6v2M4 4v10h8V4"/></svg></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="ds-table-empty">No prizes added yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="form-col-side">
                    @if ($segment->cover_image)
                        <div class="card section-spacing">
                            <img src="{{ Storage::url($segment->cover_image) }}" style="width: 100%; border-radius: 4px;">
                        </div>
                    @endif
                    <div class="card section-spacing">
                        <div class="card-header"><h3 class="text-card-title">Stats</h3></div>
                        <div class="card-body" style="font-size: 13px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span class="text-muted">Teams</span>
                                <span style="font-weight: 500;">{{ $segment->teams->count() }} / {{ $segment->max_teams ?? '∞' }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span class="text-muted">Submissions</span>
                                <span style="font-weight: 500;">{{ $segment->submissions->whereNotNull('submitted_at')->count() }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span class="text-muted">Judges</span>
                                <span style="font-weight: 500;">{{ $segment->judges->count() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header"><h3 class="text-card-title">Dates</h3></div>
                        <div class="card-body" style="font-size: 13px; color: var(--color-text-secondary);">
                            <div style="margin-bottom: 8px;"><strong>Registration:</strong><br>{{ $segment->effectiveRegistrationOpensAt()?->format('M d, H:i') }} - {{ $segment->effectiveRegistrationClosesAt()?->format('M d, H:i') }}</div>
                            <div style="margin-bottom: 8px;"><strong>Submission:</strong><br>{{ $segment->effectiveSubmissionOpensAt()?->format('M d, H:i') }} - {{ $segment->effectiveSubmissionClosesAt()?->format('M d, H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Teams Tab --}}
        <div x-show="tab === 'teams'" class="tab-content">
            <div class="card">
                <div class="ds-table-container">
                    <table class="ds-table">
                        <thead>
                            <tr>
                                <th>Team Name</th>
                                <th>Leader</th>
                                <th>Members</th>
                                <th>Status</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($segment->teams as $team)
                                <tr class="{{ $team->is_banned ? 'bg-danger-light' : '' }}" style="{{ $team->is_banned ? 'opacity: 0.7;' : '' }}">
                                    <td><div style="font-weight: 500;">{{ $team->name }}</div></td>
                                    <td>{{ $team->creator->name }}</td>
                                    <td>{{ $team->members->count() }}</td>
                                    <td>
                                        @if ($team->is_banned)
                                            <span class="badge badge-danger">Banned</span>
                                        @else
                                            <span class="badge badge-success">Active</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <div style="display: flex; justify-content: flex-end; gap: 8px;">
                                            <a href="{{ route('organizer.teams.show', $team) }}" class="btn-icon"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor"><path d="M1 8s3-7 7-7 7 7 7 7-3 7-7 7-7-7-7-7z"/><circle cx="8" cy="8" r="3"/></svg></a>
                                            @if (!$team->is_banned)
                                                <form method="POST" action="{{ route('organizer.teams.ban', $team) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-secondary btn-xs text-danger">Ban</button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('organizer.teams.unban', $team) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-secondary btn-xs">Unban</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="ds-table-empty">No teams in this segment yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Submissions Tab --}}
        <div x-show="tab === 'submissions'" class="tab-content">
            <div class="card">
                <div class="ds-table-container">
                    <table class="ds-table">
                        <thead>
                            <tr>
                                <th>Project</th>
                                <th>Team</th>
                                <th>Status</th>
                                <th>Submitted At</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($segment->submissions as $submission)
                                <tr>
                                    <td><div style="font-weight: 500;">{{ $submission->title }}</div></td>
                                    <td>{{ $submission->team->name }}</td>
                                    <td>
                                        @if ($submission->disqualified)
                                            <span class="badge badge-danger">Disqualified</span>
                                        @elseif ($submission->is_draft)
                                            <span class="badge badge-neutral">Draft</span>
                                        @else
                                            <span class="badge badge-success">Finalized</span>
                                        @endif
                                    </td>
                                    <td>{{ $submission->submitted_at?->diffForHumans() ?? '-' }}</td>
                                    <td class="text-right">
                                        <div style="display: flex; justify-content: flex-end; gap: 8px;">
                                            <a href="{{ route('organizer.submissions.show', $submission) }}" class="btn-icon"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor"><path d="M1 8s3-7 7-7 7 7 7 7-3 7-7 7-7-7-7-7z"/><circle cx="8" cy="8" r="3"/></svg></a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="ds-table-empty">No submissions yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Judges Tab --}}
        <div x-show="tab === 'judges'" class="tab-content">
            <div class="card section-spacing">
                <div class="card-header"><h3 class="text-card-title">Assign Judge</h3></div>
                <form method="POST" action="{{ route('organizer.segments.judges.store', [$hackathon, $segment]) }}" style="display: flex; gap: 8px;">
                    @csrf
                    <input type="email" name="email" class="form-input" placeholder="Judge's email address" required style="flex: 1;">
                    <button type="submit" class="btn btn-primary btn-sm">Assign to Segment</button>
                </form>
            </div>
            <div class="card">
                <div class="ds-table-container">
                    <table class="ds-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($segment->judges as $judge)
                                <tr>
                                    <td>{{ $judge->user->name }}</td>
                                    <td>{{ $judge->user->email }}</td>
                                    <td class="text-right">
                                        <form method="POST" action="{{ route('organizer.segments.judges.destroy', [$hackathon, $segment, $judge]) }}">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-icon text-danger"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor"><path d="M2 4h12M5 4V2h6v2M4 4v10h8V4"/></svg></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="ds-table-empty">No judges assigned specifically to this segment.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Criteria Tab --}}
        <div x-show="tab === 'criteria'" class="tab-content">
            <div class="card section-spacing">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 class="text-card-title">Segment-Specific Criteria</h3>
                    <button @click="$dispatch('open-modal', 'add-criterion')" class="btn btn-secondary btn-xs">+ Add Criterion</button>
                </div>
                <div class="ds-table-container">
                    <table class="ds-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Max Score</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($segment->criteria as $criterion)
                                <tr>
                                    <td>
                                        <div style="font-weight: 500;">{{ $criterion->name }}</div>
                                        <div style="font-size: 11px; color: var(--color-text-muted);">{{ $criterion->description }}</div>
                                    </td>
                                    <td>{{ $criterion->max_score }}</td>
                                    <td class="text-right">
                                        <form method="POST" action="{{ route('organizer.segments.criteria.destroy', [$hackathon, $segment, $criterion]) }}">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-icon text-danger"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor"><path d="M2 4h12M5 4V2h6v2M4 4v10h8V4"/></svg></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="ds-table-empty">No segment-specific criteria. Global criteria still apply.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header"><h3 class="text-card-title">Global Criteria (Applies to all segments)</h3></div>
                <div class="ds-table-container">
                    <table class="ds-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Max Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($hackathon->criteria as $criterion)
                                <tr>
                                    <td>{{ $criterion->name }}</td>
                                    <td>{{ $criterion->max_score }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- FAQs Tab --}}
        <div x-show="tab === 'faqs'" class="tab-content">
            <div class="card section-spacing">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 class="text-card-title">Segment FAQs</h3>
                    <button @click="$dispatch('open-modal', 'add-faq')" class="btn btn-secondary btn-xs">+ Add FAQ</button>
                </div>
                <div class="card-body">
                    @forelse ($segment->faqs as $faq)
                        <div style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid var(--color-border-subtle);">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                <div style="font-weight: 600; color: var(--color-text-primary);">{{ $faq->question }}</div>
                                <form method="POST" action="{{ route('organizer.segments.faqs.destroy', [$hackathon, $segment, $faq]) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-icon text-danger"><svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor"><path d="M2 4h12M5 4V2h6v2M4 4v10h8V4"/></svg></button>
                                </form>
                            </div>
                            <div style="color: var(--color-text-secondary); font-size: 14px;">{{ $faq->answer }}</div>
                        </div>
                    @empty
                        <div class="ds-table-empty">No FAQs for this segment.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sponsors Tab --}}
        <div x-show="tab === 'sponsors'" class="tab-content">
            <div class="card section-spacing">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 class="text-card-title">Segment Sponsors</h3>
                    <button @click="$dispatch('open-modal', 'add-sponsor')" class="btn btn-secondary btn-xs">+ Add Sponsor</button>
                </div>
                <div class="ds-table-container">
                    <table class="ds-table">
                        <thead>
                            <tr>
                                <th>Logo</th>
                                <th>Name</th>
                                <th>Tier</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($segment->sponsors as $sponsor)
                                <tr>
                                    <td><img src="{{ Storage::url($sponsor->logo) }}" height="24" style="max-width: 100px; object-fit: contain;"></td>
                                    <td>{{ $sponsor->name }}</td>
                                    <td><span class="badge badge-neutral">{{ ucfirst($sponsor->tier->value) }}</span></td>
                                    <td class="text-right">
                                        <form method="POST" action="{{ route('organizer.segments.sponsors.destroy', [$hackathon, $segment, $sponsor]) }}">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-icon text-danger"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor"><path d="M2 4h12M5 4V2h6v2M4 4v10h8V4"/></svg></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="ds-table-empty">No sponsors for this segment.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    {{-- Add Prize Modal --}}
    <x-modal name="add-prize" title="Add Prize">
        <form method="POST" action="{{ route('organizer.segments.prizes.store', [$hackathon, $segment]) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Rank (e.g. 1st Place, Best Design)</label>
                <input type="text" name="rank" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Prize Title</label>
                <input type="text" name="title" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Amount / Reward</label>
                <input type="text" name="amount" class="form-input" placeholder="e.g. $500, Macbook Pro">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add Prize</button>
            </div>
        </form>
    </x-modal>

    {{-- Add Criterion Modal --}}
    <x-modal name="add-criterion" title="Add Criterion">
        <form method="POST" action="{{ route('organizer.segments.criteria.store', [$hackathon, $segment]) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Criterion Name</label>
                <input type="text" name="name" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <input type="text" name="description" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Max Score</label>
                <input type="number" name="max_score" class="form-input" value="10" min="1" max="100" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add Criterion</button>
            </div>
        </form>
    </x-modal>

    {{-- Add FAQ Modal --}}
    <x-modal name="add-faq" title="Add FAQ">
        <form method="POST" action="{{ route('organizer.segments.faqs.store', [$hackathon, $segment]) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Question</label>
                <input type="text" name="question" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Answer</label>
                <textarea name="answer" class="form-input" style="min-height: 100px;" required></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add FAQ</button>
            </div>
        </form>
    </x-modal>

    {{-- Add Sponsor Modal --}}
    <x-modal name="add-sponsor" title="Add Sponsor">
        <form method="POST" action="{{ route('organizer.segments.sponsors.store', [$hackathon, $segment]) }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Sponsor Name</label>
                <input type="text" name="name" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Logo</label>
                <input type="file" name="logo" class="form-input" required accept="image/*">
            </div>
            <div class="form-group">
                <label class="form-label">Website URL</label>
                <input type="url" name="url" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Tier</label>
                <select name="tier" class="form-input">
                    <option value="title">Title</option>
                    <option value="gold">Gold</option>
                    <option value="silver">Silver</option>
                    <option value="bronze" selected>Bronze</option>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add Sponsor</button>
            </div>
        </form>
    </x-modal>

@endsection
