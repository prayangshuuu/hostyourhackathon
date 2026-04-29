<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Announcement\StoreAnnouncementRequest;
use App\Models\Announcement;
use App\Models\Hackathon;
use App\Services\AnnouncementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $hackathon->load('segments');

        return view('organizer.announcements.create', compact('hackathon'));
    }

    /**
     * Store a new announcement.
     */
    public function store(StoreAnnouncementRequest $request, Hackathon $hackathon): RedirectResponse
    {
        $announcement = $this->announcementService->create(
            $hackathon,
            $request->validated(),
            Auth::user(),
        );

        // If "publish" button was clicked
        if ($request->has('publish')) {
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
        $hackathon->load('segments');

        return view('organizer.announcements.edit', compact('hackathon', 'announcement'));
    }

    /**
     * Update an announcement.
     */
    public function update(StoreAnnouncementRequest $request, Hackathon $hackathon, Announcement $announcement): RedirectResponse
    {
        $this->announcementService->update($announcement, $request->validated());

        // If "publish" button was clicked and not yet published
        if ($request->has('publish') && $announcement->isDraft()) {
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
        $this->announcementService->delete($announcement);

        return redirect()
            ->route('organizer.hackathons.announcements.index', $hackathon)
            ->with('success', 'Announcement deleted.');
    }
}
