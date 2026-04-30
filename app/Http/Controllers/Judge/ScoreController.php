<?php

namespace App\Http\Controllers\Judge;

use App\Http\Controllers\Controller;
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
     * Show the scoring form for a specific submission.
     */
    public function create(Submission $submission): View
    {
        $user = Auth::user();
        $submission->load(['team.segment', 'hackathon.criteria', 'files']);

        // Find the judge assignment
        $judge = Judge::where('user_id', $user->id)
            ->where('hackathon_id', $submission->hackathon_id)
            ->firstOrFail();

        // Get existing scores
        $existingScores = $judge->scores()
            ->where('submission_id', $submission->id)
            ->get()
            ->keyBy('criteria_id');

        $criteria = $submission->hackathon->criteria;
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
        $criteria = $submission->hackathon->criteria;
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
            ->route('judge.dashboard')
            ->with('success', 'Scores saved for "'.$submission->title.'".');
    }

    /**
     * Update existing scores.
     */
    public function update(Request $request, Submission $submission): RedirectResponse
    {
        return $this->store($request, $submission);
    }
}
