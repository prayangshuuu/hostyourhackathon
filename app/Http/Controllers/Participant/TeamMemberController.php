<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamMember;
use App\Services\TeamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class TeamMemberController extends Controller
{
    public function __construct(
        protected TeamService $teamService,
    ) {}

    /**
     * Remove a member from a team (leave or kick).
     */
    public function destroy(Team $team, TeamMember $member): RedirectResponse
    {
        try {
            $this->teamService->removeMember($team, Auth::user(), $member);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        // If the user removed themselves, go back to index
        if ($member->user_id === Auth::id()) {
            return redirect()
                ->route('teams.index')
                ->with('success', 'You have left the team.');
        }

        return back()->with('success', 'Member removed.');
    }
}
