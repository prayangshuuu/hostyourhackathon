<?php

namespace App\Http\Controllers;

use App\Enums\HackathonStatus;
use App\Models\Hackathon;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Home page — hero + upcoming hackathons.
     */
    public function index(): View
    {
        $hackathonsData = Cache::remember('public.hackathons.upcoming', 300, function () {
            return Hackathon::whereIn('status', [
                HackathonStatus::Published,
                HackathonStatus::Ongoing,
            ])
                ->latest('registration_opens_at')
                ->take(6)
                ->get()
                ->map->getAttributes()
                ->all();
        });

        $hackathons = Hackathon::hydrate($hackathonsData);

        return view('public.home', compact('hackathons'));
    }
}
