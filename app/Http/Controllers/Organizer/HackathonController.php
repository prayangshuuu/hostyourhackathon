<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHackathonRequest;
use App\Http\Requests\UpdateHackathonRequest;
use App\Models\Hackathon;
use App\Services\HackathonService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HackathonController extends Controller
{
    public function __construct(private HackathonService $hackathonService)
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->hasRole('super_admin')) {
            $hackathons = Hackathon::withCount(['teams', 'submissions'])
                ->latest()
                ->get();
        } else {
            $hackathons = Hackathon::where('created_by', $user->id)
                ->orWhereHas('organizers', fn ($q) => $q->where('user_id', $user->id))
                ->withCount(['teams', 'submissions'])
                ->latest()
                ->get();
        }

        return view('organizer.hackathons.index', compact('hackathons'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        // Super admin bypasses the active hackathon limit
        if (! $request->user()->hasRole('super_admin') && ! $this->hackathonService->canCreateHackathon($request->user())) {
            return redirect()->route('organizer.hackathons.index')
                ->with('error', 'You already have an active hackathon. Multiple active hackathons are currently disabled.');
        }

        return view('organizer.hackathons.create');
    }

    public function store(StoreHackathonRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            
            $hackathon = $this->hackathonService->store($request->user(), $data);

            if ($request->hasFile('logo')) {
                $hackathon->logo = $request->file('logo')->store('hackathons/' . $hackathon->id, 'public');
            }
            if ($request->hasFile('banner')) {
                $hackathon->banner = $request->file('banner')->store('hackathons/' . $hackathon->id, 'public');
            }
            if ($hackathon->isDirty(['logo', 'banner'])) {
                $hackathon->save();
            }

            if ($request->has('save_draft')) {
                return redirect()->route('organizer.hackathons.edit', $hackathon)
                    ->with('success', 'Hackathon draft saved.');
            }

            return redirect()->route('organizer.hackathons.show', $hackathon)
                ->with('success', 'Hackathon created successfully.');

        } catch (\InvalidArgumentException $e) {
            return redirect()->route('organizer.hackathons.index')
                ->with('error', $e->getMessage());
        }
    }

    public function show(Hackathon $hackathon): View
    {
        $hackathon->load(['segments', 'organizers']);
        
        $judgesCount = $hackathon->judges()->count();
        $teamsCount = $hackathon->teams()->count();
        $submissionsCount = $hackathon->submissions()->count();

        return view('organizer.hackathons.show', compact('hackathon', 'judgesCount', 'teamsCount', 'submissionsCount'));
    }

    public function edit(Hackathon $hackathon): View
    {
        $hackathon->load('segments');
        return view('organizer.hackathons.edit', compact('hackathon'));
    }

    public function update(UpdateHackathonRequest $request, Hackathon $hackathon): RedirectResponse
    {
        $data = $request->validated();

        $this->hackathonService->update($hackathon, $data);

        if ($request->hasFile('logo')) {
            if ($hackathon->logo) {
                Storage::disk('public')->delete($hackathon->logo);
            }
            $hackathon->logo = $request->file('logo')->store('hackathons/' . $hackathon->id, 'public');
        }

        if ($request->hasFile('banner')) {
            if ($hackathon->banner) {
                Storage::disk('public')->delete($hackathon->banner);
            }
            $hackathon->banner = $request->file('banner')->store('hackathons/' . $hackathon->id, 'public');
        }

        if ($hackathon->isDirty(['logo', 'banner'])) {
            $hackathon->save();
        }

        return redirect()->route('organizer.hackathons.show', $hackathon)
            ->with('success', 'Hackathon updated successfully.');
    }

    public function destroy(Hackathon $hackathon): RedirectResponse
    {
        // Check if there are active teams or submissions
        if ($hackathon->teams()->count() > 0) {
            return back()->with('error', 'Cannot delete a hackathon that has registered teams.');
        }

        if ($hackathon->logo) {
            Storage::disk('public')->delete($hackathon->logo);
        }
        if ($hackathon->banner) {
            Storage::disk('public')->delete($hackathon->banner);
        }
        
        Storage::disk('public')->deleteDirectory('hackathons/' . $hackathon->id);

        $hackathon->delete();

        return redirect()->route('organizer.hackathons.index')
            ->with('success', 'Hackathon deleted successfully.');
    }

    public function addOrganizer(Request $request, Hackathon $hackathon): RedirectResponse
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
        
        $user = \App\Models\User::where('email', $request->email)->first();

        if ($user->id === $hackathon->created_by) {
            return back()->with('error', 'This user is the creator of the hackathon.');
        }

        if ($hackathon->organizers()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'This user is already an organizer.');
        }

        $hackathon->organizers()->attach($user->id);

        // Ensure user has organizer role
        if (! $user->hasRole('organizer')) {
            $user->assignRole('organizer');
        }

        return back()->with('success', 'Organizer added successfully.');
    }

    public function removeOrganizer(Hackathon $hackathon, \App\Models\User $user): RedirectResponse
    {
        if ($user->id === $hackathon->created_by) {
            return back()->with('error', 'Cannot remove the creator of the hackathon.');
        }

        $hackathon->organizers()->detach($user->id);

        return back()->with('success', 'Organizer removed successfully.');
    }
}
