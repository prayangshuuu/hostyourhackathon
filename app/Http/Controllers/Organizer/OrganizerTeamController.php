<?php

namespace App\Http\Controllers\Organizer;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\Hackathon;
use App\Models\Team;
use App\Services\BanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganizerTeamController extends Controller
{
    public function __construct(
        protected BanService $banService,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Team::class);

        $user = $request->user();

        $query = Team::query()
            ->with(['hackathon', 'segment', 'members.user'])
            ->withCount('members');

        if (! $user->hasRole(RoleEnum::SuperAdmin->value)) {
            $query->whereHas('hackathon', function ($q) use ($user) {
                $q->where('created_by', $user->id)
                    ->orWhereHas('organizers', fn ($q2) => $q2->where('users.id', $user->id));
            });
        }

        if ($request->filled('hackathon')) {
            $query->where('hackathon_id', $request->integer('hackathon'));
        }

        $teams = $query->latest()->paginate(20)->withQueryString();

        $hackathonsQuery = Hackathon::query()->orderBy('title');

        if (! $user->hasRole(RoleEnum::SuperAdmin->value)) {
            $hackathonsQuery->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                    ->orWhereHas('organizers', fn ($q2) => $q2->where('users.id', $user->id));
            });
        }

        $hackathons = $hackathonsQuery->get();

        return view('organizer.teams.index', compact('teams', 'hackathons'));
    }

    public function show(Team $team): View
    {
        $this->authorize('view', $team);

        $team->load(['hackathon.segments', 'segment', 'members.user', 'creator', 'bannedBy']);

        return view('organizer.teams.show', compact('team'));
    }

    public function update(Request $request, Team $team): RedirectResponse
    {
        $this->authorize('update', $team);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'segment_id' => 'nullable|exists:segments,id',
        ]);

        if (isset($validated['segment_id']) && $validated['segment_id'] !== null) {
            $exists = $team->hackathon->segments()->where('id', $validated['segment_id'])->exists();
            if (! $exists) {
                return back()->withInput()->with('error', 'That segment does not belong to this hackathon.');
            }
        }

        $team->update([
            'name' => $validated['name'],
            'segment_id' => $validated['segment_id'] ?? null,
        ]);

        return back()->with('success', 'Team updated.');
    }

    public function ban(Request $request, Team $team): RedirectResponse
    {
        $this->authorize('ban', $team);

        $validated = $request->validate([
            'reason' => 'required|string|max:2000',
        ]);

        $this->banService->banTeam($team, $request->user(), $validated['reason']);

        return back()->with('success', 'Team and all associated members have been suspended.');
    }

    public function unban(Request $request, Team $team): RedirectResponse
    {
        $this->authorize('unban', $team);

        $this->banService->unbanTeam($team, $request->user());

        return back()->with('success', 'Team suspension lifted.');
    }
}
