<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hackathon;
use App\Models\Submission;
use App\Models\Team;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $totalUsers = User::count();
        $totalHackathons = Hackathon::count();
        $totalSubmissions = Submission::count();
        $totalTeams = Team::count();

        // Hackathons by status for bar chart
        $statusCounts = Hackathon::withTrashed()
            ->selectRaw("status, count(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $maxStatusCount = max($statusCounts ?: [1]);

        // Recent registrations
        $recentUsers = User::with('roles')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'totalHackathons', 'totalSubmissions', 'totalTeams',
            'statusCounts', 'maxStatusCount', 'recentUsers'
        ));
    }
}
