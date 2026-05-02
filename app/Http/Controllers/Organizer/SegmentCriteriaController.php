<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Hackathon;
use App\Models\ScoringCriterion;
use App\Models\Segment;
use Illuminate\Http\Request;

class SegmentCriteriaController extends Controller
{
    public function store(Request $request, Hackathon $hackathon, Segment $segment)
    {
        $this->authorize('update', $hackathon);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'max_score' => 'required|integer|min:1|max:100',
            'order' => 'nullable|integer',
        ]);

        $segment->criteria()->create([
            'hackathon_id' => $hackathon->id,
            'name' => $request->name,
            'description' => $request->description,
            'max_score' => $request->max_score,
            'order' => $request->order ?? 0,
        ]);

        return back()->with('success', 'Criterion added to segment.');
    }

    public function update(Request $request, Hackathon $hackathon, Segment $segment, ScoringCriterion $criterion)
    {
        $this->authorize('update', $hackathon);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'max_score' => 'required|integer|min:1|max:100',
            'order' => 'nullable|integer',
        ]);

        $criterion->update($request->all());

        return back()->with('success', 'Criterion updated.');
    }

    public function destroy(Hackathon $hackathon, Segment $segment, ScoringCriterion $criterion)
    {
        $this->authorize('update', $hackathon);

        $criterion->delete();

        return back()->with('success', 'Criterion removed.');
    }
}
