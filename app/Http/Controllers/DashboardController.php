<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Hackathon;
use App\Models\Submission;
use App\Models\Team;
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

        // 1. $myTeams
        $myTeams = Team::whereHas('members', fn ($q) => $q->where('user_id', $user->id))
            ->whereHas('hackathon', function ($q) {
                $q->whereIn('status', ['published', 'ongoing']);
            })
            ->with(['hackathon', 'segment'])
            ->get();

        $hackathons = $myTeams->pluck('hackathon')->unique('id');

        // 2. $mySubmissions
        $teamIds = $myTeams->pluck('id');
        $mySubmissions = Submission::whereIn('team_id', $teamIds)
            ->with(['hackathon', 'team'])
            ->get();

        // 3. $announcements
        // All public + registered hackathon announcements
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

        // 4. $deadlines
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

        return view('dashboard', compact(
            'user', 'myTeams', 'mySubmissions', 'announcements', 'deadlines', 'hackathons'
        ));
    }
}
