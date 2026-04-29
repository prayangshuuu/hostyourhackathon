<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Team\JoinTeamRequest;
use App\Models\Team;
use App\Services\TeamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TeamInviteController extends Controller
{
    public function __construct(
        protected TeamService $teamService,
    ) {}

    /**
     * Show the join-team confirmation page.
     */
    public function show(string $inviteCode): View
    {
        $team = Team::where('invite_code', $inviteCode)
            ->with(['hackathon', 'segment', 'members'])
            ->withCount('members')
            ->firstOrFail();

        return view('teams.join', compact('team'));
    }

    /**
     * Accept the invite and join the team.
     */
    public function store(JoinTeamRequest $request, string $inviteCode): RedirectResponse
    {
        $team = Team::where('invite_code', $inviteCode)->firstOrFail();

        try {
            $this->teamService->joinTeam($team, $request->user());
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('teams.show', $team)
            ->with('success', "You've joined {$team->name}!");
    }
}
