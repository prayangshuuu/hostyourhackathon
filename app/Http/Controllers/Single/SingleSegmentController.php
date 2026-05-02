<?php

namespace App\Http\Controllers\Single;

use App\Http\Controllers\Controller;
use App\Models\Segment;
use App\Services\HackathonModeService;
use Illuminate\View\View;

class SingleSegmentController extends Controller
{
    public function index(): View
    {
        $hackathon = app(HackathonModeService::class)->getActiveHackathon();
        
        if (!$hackathon) {
            return view('single.no-hackathon');
        }
        
        $segments = $hackathon->segments()
            ->active()
            ->with(['prizes', 'teams', 'submissions'])
            ->orderBy('order')
            ->get();
            
        return view('single.segments.index', compact('hackathon', 'segments'));
    }

    public function show(Segment $segment): View
    {
        abort_if(!$segment->is_active, 404);
        
        $segment->load(['prizes', 'faqs', 'sponsors', 'judges.user', 'criteria']);
        
        return view('single.segments.show', compact('segment'));
    }
}
