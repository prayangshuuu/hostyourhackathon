<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Hackathon;
use App\Models\Segment;
use App\Models\Sponsor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SegmentSponsorController extends Controller
{
    public function store(Request $request, Hackathon $hackathon, Segment $segment)
    {
        $this->authorize('update', $hackathon);

        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'required|image|max:2048',
            'url' => 'nullable|url',
            'tier' => 'required|in:title,gold,silver,bronze',
            'order' => 'nullable|integer',
        ]);

        $logoPath = $request->file('logo')->store("hackathons/{$hackathon->id}/sponsors", 'public');

        $segment->sponsors()->create([
            'hackathon_id' => $hackathon->id,
            'name' => $request->name,
            'logo' => $logoPath,
            'url' => $request->url,
            'tier' => $request->tier,
            'order' => $request->order ?? 0,
        ]);

        return back()->with('success', 'Sponsor added to segment.');
    }

    public function update(Request $request, Hackathon $hackathon, Segment $segment, Sponsor $sponsor)
    {
        $this->authorize('update', $hackathon);

        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'url' => 'nullable|url',
            'tier' => 'required|in:title,gold,silver,bronze',
            'order' => 'nullable|integer',
        ]);

        $data = $request->only(['name', 'url', 'tier', 'order']);

        if ($request->hasFile('logo')) {
            Storage::disk('public')->delete($sponsor->logo);
            $data['logo'] = $request->file('logo')->store("hackathons/{$hackathon->id}/sponsors", 'public');
        }

        $sponsor->update($data);

        return back()->with('success', 'Sponsor updated.');
    }

    public function destroy(Hackathon $hackathon, Segment $segment, Sponsor $sponsor)
    {
        $this->authorize('update', $hackathon);

        Storage::disk('public')->delete($sponsor->logo);
        $sponsor->delete();

        return back()->with('success', 'Sponsor removed.');
    }
}
