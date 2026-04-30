<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Hackathon;
use App\Models\Judge;
use App\Models\User;
use App\Services\ScoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JudgeAssignmentController extends Controller
{
    public function __construct(
        protected ScoringService $scoringService,
    ) {}

    /**
     * Show judge management view for a hackathon.
     */
    public function index(Hackathon $hackathon): View
    {
        $this->authorize('update', $hackathon);

        $hackathon->load(['judges.user', 'judges.segment', 'segments']);

        return view('organizer.judges', compact('hackathon'));
    }

    /**
     * Assign a judge to the hackathon.
     */
    public function store(Request $request, Hackathon $hackathon): RedirectResponse
    {
        $this->authorize('update', $hackathon);

        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'segment_id' => ['nullable', 'integer', 'exists:segments,id'],
        ]);

        $user = User::where('email', $data['email'])->firstOrFail();

        try {
            $this->scoringService->assignJudge($hackathon, $user, $data['segment_id'] ?? null);

            // Ensure user has the judge role
            if (! $user->hasRole('judge')) {
                $user->assignRole('judge');
            }
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return back()->with('success', "Judge {$user->name} assigned.");
    }

    /**
     * Remove a judge assignment.
     */
    public function destroy(Hackathon $hackathon, Judge $judge): RedirectResponse
    {
        $this->authorize('update', $hackathon);

        $this->scoringService->removeJudge($judge);

        return back()->with('success', 'Judge removed.');
    }
}
