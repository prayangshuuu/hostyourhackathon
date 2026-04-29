<?php

namespace App\Http\Controllers;

use App\Enums\HackathonStatus;
use App\Models\Hackathon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class PublicController extends Controller
{
    /**
     * Home page — hero + upcoming hackathons.
     */
    public function home(): View
    {
        $hackathons = Cache::remember('public.hackathons.upcoming', 300, function () {
            return Hackathon::whereIn('status', [
                HackathonStatus::Published,
                HackathonStatus::Ongoing,
            ])
                ->latest('registration_opens_at')
                ->take(6)
                ->get();
        });

        return view('public.home', compact('hackathons'));
    }

    /**
     * Hackathon listing with filters.
     */
    public function index(): View
    {
        $hackathons = Cache::remember('public.hackathons.all', 300, function () {
            return Hackathon::whereIn('status', [
                HackathonStatus::Published,
                HackathonStatus::Ongoing,
                HackathonStatus::Ended,
            ])
                ->latest('registration_opens_at')
                ->get();
        });

        return view('public.hackathons.index', compact('hackathons'));
    }

    /**
     * Hackathon detail page (tabbed).
     */
    public function show(Hackathon $hackathon): View
    {
        $hackathon->load([
            'segments',
            'sponsors',
            'faqs' => fn ($q) => $q->orderBy('order'),
            'teams',
        ]);

        $isRegistered = false;
        $registrationOpen = $hackathon->registration_opens_at
            && $hackathon->registration_closes_at
            && now()->between($hackathon->registration_opens_at, $hackathon->registration_closes_at);

        if (Auth::check()) {
            $isRegistered = $hackathon->teams()
                ->whereHas('members', fn ($q) => $q->where('user_id', Auth::id()))
                ->exists();
        }

        return view('public.hackathons.show', compact('hackathon', 'isRegistered', 'registrationOpen'));
    }
}
