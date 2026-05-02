<?php

namespace App\Http\Controllers\Single;

use App\Http\Controllers\Controller;
use App\Services\HackathonModeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SingleSubmissionController extends Controller
{
    public function __construct(
        protected HackathonModeService $modeService,
    ) {}

    public function showMy(): View
    {
        $hackathon = $this->modeService->getActiveHackathon();
        abort_if(!$hackathon, 404, 'No active hackathon');

        $team = Auth::user()->teamInHackathon($hackathon);
        
        if (!$team) {
            return redirect()->route('single.teams.create');
        }

        $submission = $team->submission;

        if (!$submission) {
            return redirect()->route('submissions.create', $hackathon);
        }

        return redirect()->route('submissions.show', $submission);
    }
}
