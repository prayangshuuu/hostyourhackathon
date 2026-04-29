<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hackathon\InviteOrganizerRequest;
use App\Http\Requests\Hackathon\StoreHackathonRequest;
use App\Http\Requests\Hackathon\UpdateHackathonRequest;
use App\Models\Hackathon;
use App\Models\User;
use App\Services\HackathonStatusTransitionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HackathonController extends Controller
{
    public function __construct(
        protected HackathonStatusTransitionService $statusService,
    ) {}

    /**
     * Display a listing of hackathons the user organizes.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Hackathon::class);

        $hackathons = Hackathon::where('created_by', Auth::id())
            ->orWhereHas('organizers', fn ($q) => $q->where('user_id', Auth::id()))
            ->withCount(['teams', 'segments'])
            ->latest()
            ->paginate(15);

        return view('organizer.hackathons.index', compact('hackathons'));
    }

    /**
     * Show the form for creating a new hackathon.
     */
    public function create(): View
    {
        $this->authorize('create', Hackathon::class);

        return view('organizer.hackathons.create');
    }

    /**
     * Store a newly created hackathon.
     */
    public function store(StoreHackathonRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Generate unique slug
        $slug = Str::slug($data['title']);
        $originalSlug = $slug;
        $counter = 1;

        while (Hackathon::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $data['slug'] = $slug;
        $data['created_by'] = Auth::id();

        // Remove file fields before mass-assign
        unset($data['logo'], $data['banner']);

        $hackathon = Hackathon::create($data);

        // Handle file uploads
        $this->handleFileUploads($request, $hackathon);

        return redirect()
            ->route('organizer.hackathons.show', $hackathon)
            ->with('success', 'Hackathon created successfully.');
    }

    /**
     * Display the specified hackathon.
     */
    public function show(Hackathon $hackathon): View
    {
        $this->authorize('view', $hackathon);

        $hackathon->load(['segments', 'organizers', 'creator']);
        $hackathon->loadCount(['teams', 'segments']);

        $nextStatus = $this->statusService->nextStatus($hackathon);

        return view('organizer.hackathons.show', compact('hackathon', 'nextStatus'));
    }

    /**
     * Show the form for editing the specified hackathon.
     */
    public function edit(Hackathon $hackathon): View
    {
        $this->authorize('update', $hackathon);

        return view('organizer.hackathons.edit', compact('hackathon'));
    }

    /**
     * Update the specified hackathon.
     */
    public function update(UpdateHackathonRequest $request, Hackathon $hackathon): RedirectResponse
    {
        $data = $request->validated();

        // Remove file fields before mass-assign
        unset($data['logo'], $data['banner']);

        $hackathon->update($data);

        // Handle file uploads
        $this->handleFileUploads($request, $hackathon);

        return redirect()
            ->route('organizer.hackathons.show', $hackathon)
            ->with('success', 'Hackathon updated successfully.');
    }

    /**
     * Remove the specified hackathon (soft delete).
     */
    public function destroy(Hackathon $hackathon): RedirectResponse
    {
        $this->authorize('delete', $hackathon);

        $hackathon->delete();

        return redirect()
            ->route('organizer.hackathons.index')
            ->with('success', 'Hackathon deleted successfully.');
    }

    /**
     * Invite a co-organizer by email.
     */
    public function inviteOrganizer(InviteOrganizerRequest $request, Hackathon $hackathon): RedirectResponse
    {
        $user = User::where('email', $request->validated('email'))->firstOrFail();

        if ($hackathon->organizers()->where('user_id', $user->id)->exists()) {
            return back()->with('warning', 'This user is already a co-organizer.');
        }

        $hackathon->organizers()->attach($user->id);

        return back()->with('success', "Co-organizer {$user->name} added successfully.");
    }

    /**
     * Remove a co-organizer.
     */
    public function removeOrganizer(Hackathon $hackathon, User $user): RedirectResponse
    {
        $this->authorize('update', $hackathon);

        $hackathon->organizers()->detach($user->id);

        return back()->with('success', 'Co-organizer removed.');
    }

    /**
     * Handle logo and banner file uploads.
     */
    protected function handleFileUploads($request, Hackathon $hackathon): void
    {
        $directory = "hackathons/{$hackathon->id}";

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($hackathon->logo) {
                Storage::disk('public')->delete($hackathon->logo);
            }
            $path = $request->file('logo')->store($directory, 'public');
            $hackathon->update(['logo' => $path]);
        }

        if ($request->hasFile('banner')) {
            // Delete old banner if exists
            if ($hackathon->banner) {
                Storage::disk('public')->delete($hackathon->banner);
            }
            $path = $request->file('banner')->store($directory, 'public');
            $hackathon->update(['banner' => $path]);
        }
    }
}
