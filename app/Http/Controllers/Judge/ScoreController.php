<?php

namespace App\Http\Controllers\Judge;

use App\Http\Controllers\Controller;
use App\Models\Hackathon;
use App\Models\Judge;
use App\Models\Submission;
use App\Rules\MaxScoreRule;
use App\Services\ScoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ScoreController extends Controller
{
    public function __construct(
        protected ScoringService $scoringService,
    ) {}

    /**
     * Show the judging dashboard — lists assigned submissions.
     */
    public function dashboard(): View
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
                $submission->criteria_count = $hackathon->scoringCriteria()->count();
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

    /**
     * Show the scoring form for a specific submission.
     */
    public function show(Submission $submission): View
    {
        $user = Auth::user();
        $submission->load(['team.segment', 'hackathon.scoringCriteria', 'files']);

        // Find the judge assignment
        $judge = Judge::where('user_id', $user->id)
            ->where('hackathon_id', $submission->hackathon_id)
            ->firstOrFail();

        // Get existing scores
        $existingScores = $judge->scores()
            ->where('submission_id', $submission->id)
            ->get()
            ->keyBy('criteria_id');

        $criteria = $submission->hackathon->scoringCriteria;
        $canScore = ! ($submission->hackathon->results_at && now()->gte($submission->hackathon->results_at));

        return view('judging.score', compact('submission', 'judge', 'criteria', 'existingScores', 'canScore'));
    }

    /**
     * Store or update scores.
     */
    public function store(Request $request, Submission $submission): RedirectResponse
    {
        $user = Auth::user();

        $judge = Judge::where('user_id', $user->id)
            ->where('hackathon_id', $submission->hackathon_id)
            ->firstOrFail();

        // Build validation rules dynamically per criterion
        $criteria = $submission->hackathon->scoringCriteria;
        $rules = [];

        foreach ($criteria as $criterion) {
            $rules["scores.{$criterion->id}.score"] = ['required', 'integer', new MaxScoreRule($criterion->id)];
            $rules["scores.{$criterion->id}.remarks"] = ['nullable', 'string', 'max:1000'];
        }

        $validated = $request->validate($rules);

        try {
            $this->scoringService->saveScores($judge, $submission, $validated['scores']);
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('judging.dashboard')
            ->with('success', 'Scores saved for "' . $submission->title . '".');
    }
}
