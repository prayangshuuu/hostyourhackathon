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
     * Hackathon listing: active hackathons first, then past (ended/archived).
     */
    public function publicIndex(Request $request): View
    {
        $query = Hackathon::query()->whereIn('status', [
            HackathonStatus::Published,
            HackathonStatus::Ongoing,
            HackathonStatus::Ended,
            HackathonStatus::Archived,
        ]);

        $statusFilter = $request->input('status');
        if ($statusFilter && in_array($statusFilter, ['published', 'ongoing', 'ended', 'archived'], true)) {
            $query->where('status', $statusFilter);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('tagline', 'like', "%{$search}%");
            });
        }

        $activeHackathons = (clone $query)->active()
            ->latest('created_at')
            ->get();

        $pastHackathons = (clone $query)->whereIn('status', [
            HackathonStatus::Ended,
            HackathonStatus::Archived,
        ])
            ->latest('created_at')
            ->get();

        return view('public.hackathons.index', [
            'activeHackathons' => $activeHackathons,
            'pastHackathons' => $pastHackathons,
            'hasAnyHackathons' => $activeHackathons->isNotEmpty() || $pastHackathons->isNotEmpty(),
            'status' => $statusFilter,
        ]);
    }

    /**
     * Hackathon detail page (tabbed).
     */
    public function publicShow(string $slug): View
    {
        $hackathon = Hackathon::where('slug', $slug)->firstOrFail();

        if (! in_array($hackathon->status->value, ['published', 'ongoing', 'ended', 'archived'], true)) {
            if (! Auth::check() || (! Auth::user()->hasRole('super_admin') && $hackathon->created_by !== Auth::id() && ! $hackathon->organizers()->where('user_id', Auth::id())->exists())) {
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
        $registrationOpen = $hackathon->isRegistrationOpen();

        $canRegisterParticipation = $hackathon->status === HackathonStatus::Published
            || $hackathon->status === HackathonStatus::Ongoing;

        if (Auth::check()) {
            $isRegistered = $hackathon->teams()
                ->whereHas('members', fn ($q) => $q->where('user_id', Auth::id()))
                ->exists();
        }

        return view('public.hackathons.show', compact(
            'hackathon',
            'isRegistered',
            'registrationOpen',
            'canRegisterParticipation',
        ));
    }
}
