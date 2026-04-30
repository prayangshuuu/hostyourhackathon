<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Announcement\StoreAnnouncementRequest;
use App\Models\Announcement;
use App\Models\Hackathon;
use App\Services\AnnouncementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function __construct(
        protected AnnouncementService $announcementService,
    ) {}

    /**
     * List all announcements for a hackathon.
     */
    public function index(Hackathon $hackathon): View
    {
        $this->authorize('update', $hackathon);
        $this->authorize('viewAny', Announcement::class);

        $announcements = $hackathon->announcements()
            ->with('segment')
            ->latest()
            ->get();

        return view('organizer.announcements.index', compact('hackathon', 'announcements'));
    }

    /**
     * Show the form for creating a new announcement.
     */
    public function create(Hackathon $hackathon): View
    {
        $this->authorize('update', $hackathon);
        $this->authorize('create', Announcement::class);

        $hackathon->load('segments');

        return view('organizer.announcements.create', compact('hackathon'));
    }

    /**
     * Store a new announcement.
     */
    public function store(StoreAnnouncementRequest $request, Hackathon $hackathon): RedirectResponse
    {
        $this->authorize('update', $hackathon);
        $this->authorize('create', Announcement::class);

        $announcement = $this->announcementService->create(
            $hackathon,
            $request->validated(),
            Auth::user(),
        );

        // If "publish" button was clicked
        if ($request->has('publish')) {
            Gate::authorize('publish', $announcement);
            $this->announcementService->publish($announcement);

            return redirect()
                ->route('organizer.hackathons.announcements.index', $hackathon)
                ->with('success', 'Announcement published.');
        }

        return redirect()
            ->route('organizer.hackathons.announcements.index', $hackathon)
            ->with('success', 'Announcement saved as draft.');
    }

    /**
     * Show the form for editing an announcement.
     */
    public function edit(Hackathon $hackathon, Announcement $announcement): View
    {
        $this->authorize('update', $hackathon);
        $this->authorize('update', $announcement);

        $hackathon->load('segments');

        return view('organizer.announcements.edit', compact('hackathon', 'announcement'));
    }

    /**
     * Update an announcement.
     */
    public function update(StoreAnnouncementRequest $request, Hackathon $hackathon, Announcement $announcement): RedirectResponse
    {
        $this->authorize('update', $hackathon);
        $this->authorize('update', $announcement);

        $this->announcementService->update($announcement, $request->validated());

        // If "publish" button was clicked and not yet published
        if ($request->has('publish') && $announcement->isDraft()) {
            Gate::authorize('publish', $announcement);
            $this->announcementService->publish($announcement);

            return redirect()
                ->route('organizer.hackathons.announcements.index', $hackathon)
                ->with('success', 'Announcement updated and published.');
        }

        return redirect()
            ->route('organizer.hackathons.announcements.index', $hackathon)
            ->with('success', 'Announcement updated.');
    }

    /**
     * Delete an announcement.
     */
    public function destroy(Hackathon $hackathon, Announcement $announcement): RedirectResponse
    {
        $this->authorize('update', $hackathon);
        $this->authorize('delete', $announcement);

        $this->announcementService->delete($announcement);

        return redirect()
            ->route('organizer.hackathons.announcements.index', $hackathon)
            ->with('success', 'Announcement deleted.');
    }

    /**
     * Publish an announcement from the index action column.
     */
    public function publish(Hackathon $hackathon, Announcement $announcement): RedirectResponse
    {
        $this->authorize('update', $hackathon);
        $this->authorize('publish', $announcement);

        $this->announcementService->publish($announcement);

        return back()->with('success', 'Announcement published.');
    }
}
