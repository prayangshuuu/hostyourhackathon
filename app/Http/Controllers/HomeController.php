<?php

namespace App\Http\Controllers;

use App\Models\Hackathon;
use App\Models\Submission;
use App\Models\Team;
use App\Models\User;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Home page — hero + upcoming hackathons.
     */
    public function index(): View
    {
        $activeHackathons = Hackathon::active()->with(['segments','creator'])->latest()->take(6)->get();
        $stats = [
            'hackathons' => Hackathon::count(),
            'participants' => User::whereHas('teamMemberships')->count(),
            'submissions' => Submission::whereNotNull('submitted_at')->count(),
            'teams' => Team::count(),
        ];
        
        return view('home', compact('activeHackathons', 'stats'));
    }
}
