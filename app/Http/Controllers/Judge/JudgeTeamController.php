<?php

namespace App\Http\Controllers\Judge;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Services\BanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JudgeTeamController extends Controller
{
    public function __construct(
        protected BanService $banService,
    ) {}

    public function ban(Request $request, Team $team): RedirectResponse
    {
        $this->authorize('banAsJudge', $team);

        $validated = $request->validate([
            'reason' => 'required|string|max:2000',
        ]);

        $this->banService->banTeam($team, $request->user(), $validated['reason']);

        return back()->with('success', 'Team and all associated members have been suspended.');
    }
}
