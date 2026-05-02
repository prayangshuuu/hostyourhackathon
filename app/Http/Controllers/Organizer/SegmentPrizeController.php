<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Hackathon;
use App\Models\Segment;
use App\Models\SegmentPrize;
use Illuminate\Http\Request;

class SegmentPrizeController extends Controller
{
    public function store(Request $request, Hackathon $hackathon, Segment $segment)
    {
        $this->authorize('update', $hackathon);

        $request->validate([
            'rank' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'amount' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $segment->prizeRecords()->create($request->all());

        return back()->with('success', 'Prize added.');
    }

    public function update(Request $request, Hackathon $hackathon, Segment $segment, SegmentPrize $prize)
    {
        $this->authorize('update', $hackathon);

        $request->validate([
            'rank' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'amount' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $prize->update($request->all());

        return back()->with('success', 'Prize updated.');
    }

    public function destroy(Hackathon $hackathon, Segment $segment, SegmentPrize $prize)
    {
        $this->authorize('update', $hackathon);

        $prize->delete();

        return back()->with('success', 'Prize removed.');
    }
}
