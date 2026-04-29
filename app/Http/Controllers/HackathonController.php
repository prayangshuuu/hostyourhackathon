<?php

namespace App\Http\Controllers;

use App\Enums\HackathonStatus;
use App\Models\Hackathon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HackathonController extends Controller
{
    /**
     * Hackathon listing with server-side filters and pagination.
     */
    public function publicIndex(Request $request): View
    {
        $query = Hackathon::query();

        // Filter by status
        $status = $request->input('status');
        if ($status && in_array($status, ['published', 'ongoing', 'ended'])) {
            $query->where('status', $status);
        } else {
            // Default: show published, ongoing, and ended
            $query->whereIn('status', [
                HackathonStatus::Published,
                HackathonStatus::Ongoing,
                HackathonStatus::Ended,
            ]);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('tagline', 'like', "%{$search}%");
            });
        }

        $hackathons = $query->latest('created_at')->paginate(12)->withQueryString();

        return view('public.hackathons.index', compact('hackathons', 'status'));
    }

    /**
     * Hackathon detail page (tabbed).
     */
    public function publicShow($slug): View
    {
        $hackathon = Hackathon::where('slug', $slug)->firstOrFail();

        if (!in_array($hackathon->status->value, ['published', 'ongoing', 'ended'])) {
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
