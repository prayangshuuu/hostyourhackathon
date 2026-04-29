<?php

namespace App\Http\Controllers\Organizer;

use App\Enums\HackathonStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Hackathon\TransitionHackathonStatusRequest;
use App\Models\Hackathon;
use App\Services\HackathonStatusTransitionService;
use Illuminate\Http\RedirectResponse;

class HackathonStatusController extends Controller
{
    public function __construct(
        protected HackathonStatusTransitionService $transitionService,
    ) {}

    /**
     * Transition hackathon to the next status.
     */
    public function update(TransitionHackathonStatusRequest $request, Hackathon $hackathon): RedirectResponse
    {
        $newStatus = HackathonStatus::from($request->validated('status'));

        try {
            $this->transitionService->transition($hackathon, $newStatus);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Hackathon status changed to \"{$newStatus->value}\".");
    }
}
