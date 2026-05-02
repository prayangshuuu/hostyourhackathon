<?php

namespace App\Http\Controllers\Single;

use App\Http\Controllers\Controller;
use App\Http\Requests\Team\CreateTeamRequest;
use App\Services\HackathonModeService;
use App\Services\TeamService;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SingleTeamController extends Controller
{
    public function __construct(
        protected TeamService $teamService,
        protected HackathonModeService $modeService,
    ) {}

    public function create(): View
    {
        $hackathon = $this->modeService->getActiveHackathon();
        abort_if(!$hackathon, 404, 'No active hackathon');

        if (Auth::user()->hasTeamInHackathon($hackathon)) {
            return redirect()->route('single.teams.my');
        }

        $segments = $hackathon->segments()->active()->get();

        return view('single.teams.create', compact('hackathon', 'segments'));
    }

    public function store(CreateTeamRequest $request): RedirectResponse
    {
        $hackathon = $this->modeService->getActiveHackathon();
        abort_if(!$hackathon, 404, 'No active hackathon');

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
            ->route('single.teams.my')
            ->with('success', 'Team created!');
    }

    public function showMy(): View
    {
        $hackathon = $this->modeService->getActiveHackathon();
        abort_if(!$hackathon, 404, 'No active hackathon');

        $team = Auth::user()->teamInHackathon($hackathon);
        
        if (!$team) {
            return redirect()->route('single.teams.create');
        }

        $team->load(['members.user', 'segment', 'submission']);

        return view('single.teams.show', compact('team'));
    }
}
