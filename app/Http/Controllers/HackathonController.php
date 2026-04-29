<?php

namespace App\Http\Controllers;

use App\Enums\HackathonStatus;
use App\Models\Hackathon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HackathonController extends Controller
{
    /**
     * Hackathon listing with filters.
     */
    public function publicIndex(): View
    {
        $hackathonsData = Cache::remember('public.hackathons.all', 300, function () {
            return Hackathon::whereIn('status', [
                HackathonStatus::Published,
                HackathonStatus::Ongoing,
            ])
                ->latest('registration_opens_at')
                ->get()
                ->map->getAttributes()
                ->all();
        });

        $hackathons = Hackathon::hydrate($hackathonsData);

        return view('public.hackathons.index', compact('hackathons'));
    }

    /**
     * Hackathon detail page (tabbed).
     */
    public function publicShow($slug): View
    {
        $hackathon = Hackathon::where('slug', $slug)->firstOrFail();

        if (!in_array($hackathon->status->value, ['published', 'ongoing'])) {
            if (!Auth::check() || (!Auth::user()->hasRole('super_admin') && $hackathon->created_by !== Auth::id() && !$hackathon->organizers()->where('user_id', Auth::id())->exists())) {
                abort(404);
            }
        }

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
