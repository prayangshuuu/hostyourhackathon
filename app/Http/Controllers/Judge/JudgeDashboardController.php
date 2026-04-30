<?php

namespace App\Http\Controllers\Judge;

use App\Http\Controllers\Controller;
use App\Models\Judge;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class JudgeDashboardController extends Controller
{
    /**
     * Show the judging dashboard — lists assigned submissions.
     */
    public function index(): View
    {
        $user = Auth::user();

        // Get all judge assignments for this user
        $judgeAssignments = Judge::where('user_id', $user->id)
            ->with(['hackathon', 'segment'])
            ->get();

        // Collect submissions across all assignments
        $submissions = collect();
        $assignedSegments = collect();

        foreach ($judgeAssignments as $judge) {
            $hackathon = $judge->hackathon;
            $assignedSegments->push($judge->segment);

            $query = Submission::where('hackathon_id', $hackathon->id)
                ->where('is_draft', false)
                ->with(['team.segment', 'hackathon', 'scores' => function ($q) use ($judge) {
                    $q->where('judge_id', $judge->id);
                }]);

            // If judge is assigned to a specific segment, filter by it
            if ($judge->segment_id) {
                $query->whereHas('team', fn ($q) => $q->where('segment_id', $judge->segment_id));
            }

            $items = $query->get()->map(function ($submission) use ($judge, $hackathon) {
                $submission->judge_id = $judge->id;
                $submission->criteria_count = $hackathon->criteria()->count();

                return $submission;
            });

            $submissions = $submissions->merge($items);
        }

        // Deduplicate
        $submissions = $submissions->unique('id');

        // Stats
        $totalAssigned = $submissions->count();
        $scored = $submissions->filter(fn ($s) => $s->scores->count() > 0 && $s->scores->count() >= $s->criteria_count)->count();
        $partial = $submissions->filter(fn ($s) => $s->scores->count() > 0 && $s->scores->count() < $s->criteria_count)->count();
        $remaining = $totalAssigned - $scored - $partial;

        return view('judging.dashboard', compact(
            'submissions', 'assignedSegments', 'totalAssigned', 'scored', 'partial', 'remaining'
        ));
    }
}
