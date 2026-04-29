<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Hackathon;
use App\Services\AnnouncementService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ParticipantAnnouncementController extends Controller
{
    public function __construct(
        protected AnnouncementService $announcementService,
    ) {}

    /**
     * List visible announcements for a participant.
     */
    public function index(Hackathon $hackathon): View
    {
        $announcements = $this->announcementService->getVisibleForParticipant(
            $hackathon,
            Auth::user(),
        );

        return view('participant.announcements.index', compact('hackathon', 'announcements'));
    }

    /**
     * Show a single announcement.
     */
    public function show(Hackathon $hackathon, Announcement $announcement): View
    {
        return view('participant.announcements.show', compact('hackathon', 'announcement'));
    }
}
