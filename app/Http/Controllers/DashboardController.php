<?php

namespace App\Http\Controllers;

use App\Models\Hackathon;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Participant dashboard — eager-loaded, no N+1.
     */
    public function __invoke(): View
    {
        $user = Auth::user();

        // Eager load teams with hackathon + submission in one query
        $teams = Team::whereHas('members', fn ($q) => $q->where('user_id', $user->id))
            ->with([
                'hackathon',
                'submission',
                'segment',
                'members',
            ])
            ->get();

        $hackathons = $teams->pluck('hackathon')->unique('id');

        // Stats
        $activeHackathons = $hackathons->filter(
            fn ($h) => in_array($h->status->value, ['published', 'ongoing'])
        )->count();

        $teamCount = $teams->count();

        $submittedCount = $teams->filter(
            fn ($t) => $t->submission && ! $t->submission->is_draft
        )->count();

        // Upcoming deadlines
        $deadlines = collect();
        foreach ($hackathons as $hackathon) {
            if ($hackathon->registration_closes_at && $hackathon->registration_closes_at->isFuture()) {
                $deadlines->push([
                    'label' => $hackathon->title . ' — Registration closes',
                    'date' => $hackathon->registration_closes_at,
                    'past' => false,
                ]);
            }
            if ($hackathon->submission_closes_at && $hackathon->submission_closes_at->isFuture()) {
                $deadlines->push([
                    'label' => $hackathon->title . ' — Submission deadline',
                    'date' => $hackathon->submission_closes_at,
                    'past' => false,
                ]);
            }
            if ($hackathon->results_at) {
                $deadlines->push([
                    'label' => $hackathon->title . ' — Results',
                    'date' => $hackathon->results_at,
                    'past' => $hackathon->results_at->isPast(),
                ]);
            }
        }
        $deadlines = $deadlines->sortBy('date')->take(6);

        // Recent announcements (last 3 visible)
        $announcements = \App\Models\Announcement::whereIn('hackathon_id', $hackathons->pluck('id'))
            ->whereNotNull('published_at')
            ->where(function ($q) {
                $q->where('scheduled_at', '<=', now())->orWhereNull('scheduled_at');
            })
            ->latest('published_at')
            ->take(3)
            ->get();

        // Unread notification count (for sidebar badge)
        $unreadAnnouncementCount = $user->unreadNotifications()->count();

        return view('dashboard', compact(
            'user', 'teams', 'hackathons', 'activeHackathons', 'teamCount',
            'submittedCount', 'deadlines', 'announcements', 'unreadAnnouncementCount'
        ));
    }
}
