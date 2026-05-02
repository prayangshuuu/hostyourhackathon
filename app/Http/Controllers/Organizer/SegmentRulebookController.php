<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Hackathon;
use App\Models\Segment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SegmentRulebookController extends Controller
{
    public function store(Request $request, Hackathon $hackathon, Segment $segment)
    {
        $this->authorize('update', $hackathon);

        $request->validate([
            'rulebook' => 'required|file|mimes:pdf|max:10240',
        ]);

        if ($segment->rulebook_path) {
            Storage::disk('public')->delete($segment->rulebook_path);
        }

        $path = $request->file('rulebook')->store("hackathons/{$hackathon->id}/segments/rulebooks", 'public');
        $segment->update(['rulebook_path' => $path]);

        return back()->with('success', 'Rulebook uploaded.');
    }

    public function destroy(Hackathon $hackathon, Segment $segment)
    {
        $this->authorize('update', $hackathon);

        if ($segment->rulebook_path) {
            Storage::disk('public')->delete($segment->rulebook_path);
            $segment->update(['rulebook_path' => null]);
        }

        return back()->with('success', 'Rulebook removed.');
    }
}
