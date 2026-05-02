<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Hackathon;
use App\Models\Segment;
use Illuminate\Http\Request;

class SegmentFaqController extends Controller
{
    public function store(Request $request, Hackathon $hackathon, Segment $segment)
    {
        $this->authorize('update', $hackathon);

        $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
            'order' => 'nullable|integer',
        ]);

        $segment->faqs()->create([
            'hackathon_id' => $hackathon->id,
            'question' => $request->question,
            'answer' => $request->answer,
            'order' => $request->order ?? 0,
        ]);

        return back()->with('success', 'FAQ added.');
    }

    public function update(Request $request, Hackathon $hackathon, Segment $segment, Faq $faq)
    {
        $this->authorize('update', $hackathon);

        $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
            'order' => 'nullable|integer',
        ]);

        $faq->update($request->all());

        return back()->with('success', 'FAQ updated.');
    }

    public function destroy(Hackathon $hackathon, Segment $segment, Faq $faq)
    {
        $this->authorize('update', $hackathon);

        $faq->delete();

        return back()->with('success', 'FAQ removed.');
    }
}
