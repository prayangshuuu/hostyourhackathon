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
    public function store(StoreSegmentRequest $request, Hackathon $hackathon)
    {
        $segment = $hackathon->segments()->create($request->safe()->except('rulebook'));

        if ($request->hasFile('rulebook')) {
            $path = $request->file('rulebook')->store("hackathons/{$hackathon->id}/segments/{$segment->id}", 'public');
            $segment->update(['rulebook' => $path]);
        }

        if ($request->wantsJson() || str_contains($request->header('Accept'), 'application/json')) {
            return response()->json(['message' => 'Segment created', 'segment' => $segment]);
        }

        return back()->with('success', 'Segment created.');
    }

    /**
     * Update an existing segment.
     */
    public function update(UpdateSegmentRequest $request, Hackathon $hackathon, Segment $segment)
    {
        $segment->update($request->safe()->except('rulebook'));

        if ($request->hasFile('rulebook')) {
            if ($segment->rulebook) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($segment->rulebook);
            }
            $path = $request->file('rulebook')->store("hackathons/{$hackathon->id}/segments/{$segment->id}", 'public');
            $segment->update(['rulebook' => $path]);
        }

        if ($request->wantsJson() || str_contains($request->header('Accept'), 'application/json')) {
            return response()->json(['message' => 'Segment updated', 'segment' => $segment]);
        }

        return back()->with('success', 'Segment updated.');
    }

    /**
     * Delete a segment — nullifies segment_id on related records.
     */
    public function destroy(Hackathon $hackathon, Segment $segment)
    {
        $this->authorize('update', $hackathon);

        // Nullify segment references on related records before deleting
        $segment->teams()->update(['segment_id' => null]);
        $segment->judges()->update(['segment_id' => null]);
        $segment->announcements()->update(['segment_id' => null]);

        if ($segment->rulebook) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($segment->rulebook);
            \Illuminate\Support\Facades\Storage::disk('public')->deleteDirectory("hackathons/{$hackathon->id}/segments/{$segment->id}");
        }

        $segment->delete();

        if (request()->wantsJson() || str_contains(request()->header('Accept'), 'application/json')) {
            return response()->json(['message' => 'Segment deleted']);
        }

        return back()->with('success', 'Segment deleted.');
    }
}
