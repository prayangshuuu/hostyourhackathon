<?php

namespace App\Http\Controllers\Single;

use App\Http\Controllers\Controller;
use App\Models\Hackathon;
use App\Services\HackathonModeService;
use App\Services\ScoringService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SingleLeaderboardController extends Controller
{
    public function __construct(
        protected ScoringService $scoringService,
        protected HackathonModeService $modeService,
    ) {}

    public function show(Request $request): View
    {
        $hackathon = $this->modeService->getActiveHackathon();
        abort_if(!$hackathon, 404, 'No active hackathon');

        if (! app(SettingService::class)->get('enable_leaderboard', true)) {
            abort(403, 'Leaderboard is currently disabled.');
        }

        $user = Auth::user();
        
        // Use existing logic for visibility check if possible, or just check results_at
        $canViewData = $hackathon->results_at && now()->greaterThanOrEqualTo($hackathon->results_at);
        if ($user && ($user->hasRole('super_admin') || $hackathon->isOwnedByUser($user))) {
            $canViewData = true;
        }

        $segmentId = $request->get('segment_id');
        $segment = $segmentId ? $hackathon->segments()->find($segmentId) : null;

        $leaderboardEntries = collect();
        if ($canViewData) {
            if ($segment) {
                $leaderboardEntries = $this->scoringService->getLeaderboard($segment);
            } else {
                $leaderboardEntries = $this->scoringService->getHackathonLeaderboard($hackathon);
            }

            $leaderboardEntries = $leaderboardEntries->map(function ($submission) {
                return (object) [
                    'team' => $submission->team,
                    'segment' => $submission->team?->segment,
                    'project_title' => $submission->title,
                    'total_score' => (float) ($submission->scores_sum_score ?? 0),
                ];
            });
        }

        $segments = $hackathon->segments()->active()->orderBy('order')->get();

        return view('leaderboard.show', [
            'hackathon' => $hackathon,
            'leaderboard' => $leaderboardEntries,
            'canView' => $canViewData,
            'segments' => $segments,
            'currentSegment' => $segment,
        ]);
    }
}
