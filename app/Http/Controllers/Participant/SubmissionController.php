<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Submission\FinalizeSubmissionRequest;
use App\Http\Requests\Submission\SaveSubmissionRequest;
use App\Models\Hackathon;
use App\Models\Submission;
use App\Models\TeamMember;
use App\Services\SubmissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SubmissionController extends Controller
{
    public function __construct(
        protected SubmissionService $submissionService,
    ) {}

    /**
     * Show the create submission form.
     * If the team already has a submission, redirect to edit.
     */
    public function create(Hackathon $hackathon): View|RedirectResponse
    {
        $user = Auth::user();

        // Find user's team for this hackathon
        $team = $this->getUserTeam($hackathon, $user);

        if (! $team) {
            return redirect()->route('teams.index')
                ->with('error', 'You must join a team before submitting.');
        }

        // If submission already exists, redirect to edit
        $existing = Submission::where('team_id', $team->id)
            ->where('hackathon_id', $hackathon->id)
            ->first();

        if ($existing) {
            return redirect()->route('submissions.edit', $existing);
        }

        $isLeader = $this->submissionService->isLeader($team, $user);

        return view('submissions.create', compact('hackathon', 'team', 'isLeader'));
    }

    /**
     * Store a new draft submission.
     */
    public function store(SaveSubmissionRequest $request, Hackathon $hackathon): RedirectResponse
    {
        $user = $request->user();
        $team = $this->getUserTeam($hackathon, $user);

        if (! $team) {
            return redirect()->route('teams.index')
                ->with('error', 'You must join a team before submitting.');
        }

        try {
            $submission = $this->submissionService->saveDraft(
                $team,
                $hackathon,
                $user,
                $request->validated(),
            );
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('submissions.edit', $submission)
            ->with('success', 'Draft saved successfully.');
    }

    /**
     * Show submission details (read-only).
     */
    public function show(Submission $submission): View
    {
        $submission->load(['team.members.user', 'hackathon', 'files']);

        $user = Auth::user();
        $isOrganizer = $submission->hackathon->organizers()
            ->where('user_id', $user->id)
            ->exists()
            || $submission->hackathon->created_by === $user->id
            || $user->hasRole('super_admin');

        return view('submissions.show', compact('submission', 'isOrganizer'));
    }

    /**
     * Show the edit submission form.
     */
    public function edit(Submission $submission): View|RedirectResponse
    {
        $submission->load(['team.members.user', 'hackathon', 'files']);

        $user = Auth::user();
        $team = $submission->team;
        $hackathon = $submission->hackathon;
        $isLeader = $this->isLeader($team, $user);

        return view('submissions.edit', compact('submission', 'hackathon', 'team', 'isLeader'));
    }

    /**
     * Update an existing draft submission.
     */
    public function update(SaveSubmissionRequest $request, Submission $submission): RedirectResponse
    {
        try {
            $this->submissionService->updateDraft(
                $submission,
                $request->user(),
                $request->validated(),
            );
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Draft updated successfully.');
    }

    /**
     * Finalize (submit) the submission.
     */
    public function submit(FinalizeSubmissionRequest $request, Submission $submission): RedirectResponse
    {
        try {
            $this->submissionService->finalize($submission, $request->user());
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('submissions.show', $submission)
            ->with('success', 'Submission finalized successfully!');
    }

    /**
     * Re-open a finalized submission (organizer action).
     */
    public function reopen(Submission $submission): RedirectResponse
    {
        try {
            $this->submissionService->reOpen($submission);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Submission re-opened. The team can now edit it.');
    }

    /**
     * Get the authenticated user's team for a hackathon.
     */
    private function getUserTeam(Hackathon $hackathon, $user)
    {
        $membership = TeamMember::whereHas('team', function ($q) use ($hackathon) {
            $q->where('hackathon_id', $hackathon->id)->whereNull('deleted_at');
        })->where('user_id', $user->id)->first();

        return $membership ? $membership->team : null;
    }

    /**
     * Check if a user is the team leader.
     */
    private function isLeader($team, $user): bool
    {
        return $team->members()
            ->where('user_id', $user->id)
            ->where('role', \App\Enums\TeamRole::Leader)
            ->exists();
    }
}
