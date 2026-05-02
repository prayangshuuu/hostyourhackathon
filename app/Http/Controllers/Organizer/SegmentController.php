<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Segment\StoreSegmentRequest;
use App\Http\Requests\Segment\UpdateSegmentRequest;
use App\Models\Hackathon;
use App\Models\Segment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SegmentController extends Controller
{
    public function index(Hackathon $hackathon): View
    {
        $this->authorize('update', $hackathon);

        $segments = $hackathon->segments()->orderBy('order')->get();

        return view('organizer.segments.index', compact('hackathon', 'segments'));
    }

    public function create(Hackathon $hackathon): View
    {
        $this->authorize('update', $hackathon);

        return view('organizer.segments.create', compact('hackathon'));
    }

    public function store(StoreSegmentRequest $request, Hackathon $hackathon): RedirectResponse
    {
        $this->authorize('update', $hackathon);

        $data = $request->validated();
        
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store("hackathons/{$hackathon->id}/segments", 'public');
        }

        if ($request->hasFile('rulebook')) {
            $data['rulebook_path'] = $request->file('rulebook')->store("hackathons/{$hackathon->id}/segments/rulebooks", 'public');
        }

        $segment = $hackathon->segments()->create($data);

        return redirect()->route('organizer.segments.show', [$hackathon, $segment])
            ->with('success', 'Segment created successfully.');
    }

    public function show(Hackathon $hackathon, Segment $segment): View
    {
        $this->authorize('update', $hackathon);

        $segment->load(['teams', 'submissions.team', 'judges.user', 'criteria', 'faqs', 'sponsors', 'prizeRecords']);

        return view('organizer.segments.show', compact('hackathon', 'segment'));
    }

    public function edit(Hackathon $hackathon, Segment $segment): View
    {
        $this->authorize('update', $hackathon);

        return view('organizer.segments.edit', compact('hackathon', 'segment'));
    }

    public function update(UpdateSegmentRequest $request, Hackathon $hackathon, Segment $segment): RedirectResponse
    {
        $this->authorize('update', $hackathon);

        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            if ($segment->cover_image) {
                Storage::disk('public')->delete($segment->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store("hackathons/{$hackathon->id}/segments", 'public');
        }

        if ($request->hasFile('rulebook')) {
            if ($segment->rulebook_path) {
                Storage::disk('public')->delete($segment->rulebook_path);
            }
            $data['rulebook_path'] = $request->file('rulebook')->store("hackathons/{$hackathon->id}/segments/rulebooks", 'public');
        }

        $segment->update($data);

        return redirect()->route('organizer.segments.show', [$hackathon, $segment])
            ->with('success', 'Segment updated successfully.');
    }

    public function destroy(Hackathon $hackathon, Segment $segment): RedirectResponse
    {
        $this->authorize('update', $hackathon);

        if ($segment->cover_image) {
            Storage::disk('public')->delete($segment->cover_image);
        }

        if ($segment->rulebook_path) {
            Storage::disk('public')->delete($segment->rulebook_path);
        }

        $segment->delete();

        return redirect()->route('organizer.segments.index', $hackathon)
            ->with('success', 'Segment deleted successfully.');
    }

    public function reorder(Request $request, Hackathon $hackathon): \Illuminate\Http\JsonResponse
    {
        $this->authorize('update', $hackathon);

        $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['required', 'integer', 'exists:segments,id'],
        ]);

        foreach ($request->order as $index => $id) {
            Segment::where('id', $id)->where('hackathon_id', $hackathon->id)->update(['order' => $index]);
        }

        return response()->json(['message' => 'Order updated.']);
    }
}
