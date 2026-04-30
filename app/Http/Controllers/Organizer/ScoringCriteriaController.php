<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Hackathon;
use App\Models\ScoringCriterion;
use App\Services\ScoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScoringCriteriaController extends Controller
{
    public function __construct(
        protected ScoringService $scoringService,
    ) {}

    /**
     * Show criteria management view for a hackathon.
     */
    public function index(Hackathon $hackathon): View
    {
        $hackathon->load('criteria');

        return view('organizer.criteria', compact('hackathon'));
    }

    /**
     * Store a new criterion.
     */
    public function store(Request $request, Hackathon $hackathon): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'max_score' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $this->scoringService->addCriterion($hackathon, $data);

        return back()->with('success', 'Criterion added.');
    }

    /**
     * Update a criterion.
     */
    public function update(Request $request, Hackathon $hackathon, ScoringCriterion $criterion): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'max_score' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $this->scoringService->updateCriterion($criterion, $data);

        return back()->with('success', 'Criterion updated.');
    }

    /**
     * Delete a criterion.
     */
    public function destroy(Hackathon $hackathon, ScoringCriterion $criterion): RedirectResponse
    {
        $this->scoringService->deleteCriterion($criterion);

        return back()->with('success', 'Criterion removed.');
    }
}
