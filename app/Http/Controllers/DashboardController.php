<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Hackathon;
use App\Models\Submission;
use App\Models\Team;
use App\Services\HackathonModeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Participant dashboard.
     */
    public function index(): View
    {
        $user = Auth::user();
        $modeService = app(HackathonModeService::class);
        $singleMode = $modeService->isSingleMode();
        $singleActiveHackathon = $singleMode ? $modeService->getActiveHackathon() : null;

        $hasActiveHackathons = $singleMode
            ? $singleActiveHackathon !== null
            : Hackathon::active()->exists();

        $myPastTeams = Team::whereHas('members', fn ($q) => $q->where('user_id', $user->id))
            ->with(['hackathon', 'segment', 'members'])
            ->withCount('members')
            ->latest()
            ->get();

        $myPastSubmissions = Submission::whereHas('team.members', fn ($q) => $q->where('user_id', $user->id))
            ->with(['hackathon', 'team'])
            ->withSum('scores', 'score')
            ->orderByDesc('submitted_at')
            ->orderByDesc('id')
            ->get();

        if ($hasActiveHackathons) {
            $myTeams = Team::whereHas('members', fn ($q) => $q->where('user_id', $user->id))
                ->whereHas('hackathon', fn ($hq) => $hq->active())
                ->with(['hackathon', 'segment'])
                ->get();

            if ($singleMode && $singleActiveHackathon) {
                $myTeams = $myTeams->where('hackathon_id', $singleActiveHackathon->id)->values();
            }

            $hackathons = $myTeams->pluck('hackathon')->unique('id');

            $teamIds = $myTeams->pluck('id');
            $mySubmissions = Submission::whereIn('team_id', $teamIds)
                ->with(['hackathon', 'team'])
                ->get();

            $announcements = Announcement::whereNotNull('published_at')
                ->where(function ($query) use ($hackathons) {
                    $query->where('visibility', 'public')
                        ->orWhere(function ($q) use ($hackathons) {
                            $q->where('visibility', 'registered_only')
                                ->whereIn('hackathon_id', $hackathons->pluck('id'));
                        });
                })
                ->where(function ($q) {
                    $q->where('scheduled_at', '<=', now())->orWhereNull('scheduled_at');
                })
                ->latest('published_at')
                ->take(5)
                ->get();

            $deadlines = collect();
            foreach ($hackathons as $hackathon) {
                if ($hackathon->registration_closes_at && $hackathon->registration_closes_at->isFuture()) {
                    $deadlines->push([
                        'hackathon' => $hackathon->title,
                        'label' => 'Registration closes',
                        'date' => $hackathon->registration_closes_at,
                        'past' => false,
                    ]);
                }
                if ($hackathon->submission_closes_at && $hackathon->submission_closes_at->isFuture()) {
                    $deadlines->push([
                        'hackathon' => $hackathon->title,
                        'label' => 'Submission deadline',
                        'date' => $hackathon->submission_closes_at,
                        'past' => false,
                    ]);
                }
            }
            $deadlines = $deadlines->sortBy('date')->take(6);
        } else {
            $myTeams = collect();
            $mySubmissions = collect();
            $hackathons = collect();
            $deadlines = collect();

            $pastIds = $myPastTeams->pluck('hackathon_id')->unique()->filter();

            $announcements = Announcement::whereNotNull('published_at')
                ->where(function ($query) use ($pastIds) {
                    $query->where('visibility', 'public')
                        ->orWhere(function ($q) use ($pastIds) {
                            $q->where('visibility', 'registered_only')
                                ->whereIn('hackathon_id', $pastIds);
                        });
                })
                ->where(function ($q) {
                    $q->where('scheduled_at', '<=', now())->orWhereNull('scheduled_at');
                })
                ->latest('published_at')
                ->take(5)
                ->get();
        }

        return view('dashboard', compact(
            'user',
            'myTeams',
            'mySubmissions',
            'announcements',
            'deadlines',
            'hackathons',
            'hasActiveHackathons',
            'myPastTeams',
            'myPastSubmissions',
        ));
    }
}
