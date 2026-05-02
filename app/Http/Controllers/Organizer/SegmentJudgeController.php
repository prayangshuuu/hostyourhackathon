<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Hackathon;
use App\Models\Judge;
use App\Models\Segment;
use App\Models\User;
use Illuminate\Http\Request;

class SegmentJudgeController extends Controller
{
    public function store(Request $request, Hackathon $hackathon, Segment $segment)
    {
        $this->authorize('update', $hackathon);

        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->firstOrFail();

        // Check if already a judge for this hackathon
        $exists = Judge::where('hackathon_id', $hackathon->id)
            ->where('user_id', $user->id)
            ->first();

        if ($exists) {
            if ($exists->segment_id === $segment->id) {
                return back()->with('error', 'This user is already a judge for this segment.');
            }
            
            // Move judge to this segment? Or just say they are already a judge.
            // Let's allow updating their segment.
            $exists->update(['segment_id' => $segment->id]);
            return back()->with('success', 'Judge assigned to this segment.');
        }

        Judge::create([
            'hackathon_id' => $hackathon->id,
            'user_id' => $user->id,
            'segment_id' => $segment->id,
        ]);

        // Ensure user has judge role
        if (!$user->hasRole('judge') && !$user->hasRole('super_admin')) {
            $user->assignRole('judge');
        }

        return back()->with('success', 'Judge assigned to segment.');
    }

    public function destroy(Hackathon $hackathon, Segment $segment, Judge $judge)
    {
        $this->authorize('update', $hackathon);

        $judge->delete();

        return back()->with('success', 'Judge removed from segment.');
    }
}
