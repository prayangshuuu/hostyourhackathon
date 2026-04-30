<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Models\Hackathon;
use App\Models\Judge;
use App\Models\User;
use App\Services\ScoringService;
use App\Services\SettingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function __construct(
        protected ScoringService $scoringService,
    ) {}

    /**
     * Show the leaderboard for a hackathon.
     *
     * leaderboard_public=false: requires auth — super admin, hackathon organizers, or judges.
     */
    public function show(Hackathon $hackathon): View
    {
        if (! app(SettingService::class)->get('enable_leaderboard', true)) {
            abort(403, 'Leaderboard is currently disabled.');
        }

        $user = Auth::user();

        $canViewData = $this->leaderboardPayloadVisibleTo($user, $hackathon);

        $leaderboardEntries = collect();
        if ($canViewData) {
            $leaderboardEntries = $this->scoringService->getLeaderboard($hackathon)->map(function ($submission) {
                return (object) [
                    'team' => $submission->team,
                    'segment' => $submission->team?->segment,
                    'project_title' => $submission->title,
                    'total_score' => (float) ($submission->scores_sum_score ?? 0),
                ];
            });
        }

        return view('leaderboard.show', [
            'hackathon' => $hackathon,
            'leaderboard' => $leaderboardEntries,
            'canView' => $canViewData,
        ]);
    }

    protected function leaderboardPayloadVisibleTo(?User $user, Hackathon $hackathon): bool
    {
        if ($hackathon->leaderboard_public) {
            return true;
        }

        if (! $user) {
            return false;
        }

        if ($user->hasRole(RoleEnum::SuperAdmin->value)) {
            return true;
        }

        if ($hackathon->isOwnedByUser($user)) {
            return true;
        }

        return Judge::query()
            ->where('hackathon_id', $hackathon->id)
            ->where('user_id', $user->id)
            ->exists();
    }
}
