<?php

namespace App\Http\Controllers;

use App\Models\Hackathon;
use App\Services\ScoringService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function __construct(
        protected ScoringService $scoringService,
    ) {}

    /**
     * Show the leaderboard for a hackathon.
     */
    public function show(Hackathon $hackathon): View
    {
        $user = Auth::user();

        $isOrganizer = $hackathon->created_by === $user->id
            || $hackathon->organizers()->where('user_id', $user->id)->exists()
            || $user->hasRole('super_admin');

        // If leaderboard is not public and user is not organizer/admin, restrict
        $canView = $hackathon->leaderboard_public || $isOrganizer;

        $submissions = $canView
            ? $this->scoringService->getLeaderboard($hackathon)
            : collect();

        return view('leaderboard.show', compact('hackathon', 'submissions', 'canView', 'isOrganizer'));
    }
}
