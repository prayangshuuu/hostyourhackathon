<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Team\CreateTeamRequest;
use App\Models\Hackathon;
use App\Models\Team;
use App\Services\TeamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function __construct(
        protected TeamService $teamService,
    ) {}

    /**
     * List teams the participant belongs to.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Team::class);

        $teams = Team::whereHas('members', fn ($q) => $q->where('user_id', Auth::id()))
            ->with(['hackathon', 'segment', 'members'])
            ->withCount('members')
            ->latest()
            ->get();

        return view('teams.index', compact('teams'));
    }

    /**
     * Show the create-team form for a hackathon.
     */
    public function create(Hackathon $hackathon): View
    {
        $this->authorize('create', Team::class);
        Gate::authorize('teams.createOnHackathon', $hackathon);

        $hackathon->load('segments');

        return view('teams.create', compact('hackathon'));
    }

    /**
     * Store a new team.
     */
    public function store(CreateTeamRequest $request, Hackathon $hackathon): RedirectResponse
    {
        $this->authorize('create', Team::class);
        Gate::authorize('teams.createOnHackathon', $hackathon);

        try {
            $team = $this->teamService->createTeam(
                $hackathon,
                $request->user(),
                $request->validated(),
            );
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('teams.show', $team)
            ->with('success', 'Team created! Share the invite link with your teammates.');
    }

    /**
     * Show team details.
     */
    public function show(Team $team): View
    {
        $this->authorize('view', $team);

        $team->load(['hackathon', 'segment', 'members.user', 'creator']);

        $isLeader = $this->teamService->isLeader($team, Auth::user());
        $isMember = $team->members->contains('user_id', Auth::id());

        return view('teams.show', compact('team', 'isLeader', 'isMember'));
    }

    /**
     * Update team name (leader only).
     */
    public function update(CreateTeamRequest $request, Team $team): RedirectResponse
    {
        $this->authorize('update', $team);

        try {
            $this->teamService->updateName($team, $request->user(), $request->validated('name'));
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Team name updated.');
    }

    /**
     * Disband team (leader only, soft-delete).
     */
    public function destroy(Team $team): RedirectResponse
    {
        $this->authorize('delete', $team);

        try {
            $this->teamService->disbandTeam($team, Auth::user());
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('teams.index')
            ->with('success', 'Team disbanded.');
    }
}
