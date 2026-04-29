<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Segment\StoreSegmentRequest;
use App\Http\Requests\Segment\UpdateSegmentRequest;
use App\Models\Hackathon;
use App\Models\Segment;
use Illuminate\Http\RedirectResponse;

class SegmentController extends Controller
{
    /**
     * Store a new segment under a hackathon.
     */
    public function store(StoreSegmentRequest $request, Hackathon $hackathon): RedirectResponse
    {
        $hackathon->segments()->create($request->validated());

        return back()->with('success', 'Segment created.');
    }

    /**
     * Update an existing segment.
     */
    public function update(UpdateSegmentRequest $request, Hackathon $hackathon, Segment $segment): RedirectResponse
    {
        $segment->update($request->validated());

        return back()->with('success', 'Segment updated.');
    }

    /**
     * Delete a segment — nullifies segment_id on related records.
     */
    public function destroy(Hackathon $hackathon, Segment $segment): RedirectResponse
    {
        $this->authorize('update', $hackathon);

        // Nullify segment references on related records before deleting
        $segment->teams()->update(['segment_id' => null]);
        $segment->judges()->update(['segment_id' => null]);
        $segment->announcements()->update(['segment_id' => null]);

        $segment->delete();

        return back()->with('success', 'Segment deleted.');
    }
}
