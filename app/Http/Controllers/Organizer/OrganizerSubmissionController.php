<?php

namespace App\Http\Controllers\Organizer;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\Hackathon;
use App\Models\Submission;
use App\Services\SubmissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganizerSubmissionController extends Controller
{
    public function __construct(
        protected SubmissionService $submissionService,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Submission::class);

        $user = $request->user();

        $query = Submission::query()
            ->with(['hackathon', 'team.segment', 'team.members.user']);

        if (! $user->hasRole(RoleEnum::SuperAdmin->value)) {
            $query->whereHas('hackathon', function ($q) use ($user) {
                $q->where('created_by', $user->id)
                    ->orWhereHas('organizers', fn ($q2) => $q2->where('users.id', $user->id));
            });
        }

        if ($request->filled('hackathon')) {
            $query->where('hackathon_id', $request->integer('hackathon'));
        }

        $submissions = $query->latest()->paginate(20)->withQueryString();

        $hackathonsQuery = Hackathon::query()->orderBy('title');

        if (! $user->hasRole(RoleEnum::SuperAdmin->value)) {
            $hackathonsQuery->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                    ->orWhereHas('organizers', fn ($q2) => $q2->where('users.id', $user->id));
            });
        }

        $hackathons = $hackathonsQuery->get();

        return view('organizer.submissions.index', compact('submissions', 'hackathons'));
    }

    public function show(Submission $submission): View
    {
        $this->authorize('view', $submission);

        $submission->load(['hackathon', 'team.members.user', 'segment', 'files', 'disqualifier']);

        return view('organizer.submissions.show', compact('submission'));
    }

    public function disqualify(Request $request, Submission $submission): RedirectResponse
    {
        $this->authorize('disqualify', $submission);

        $validated = $request->validate([
            'reason' => 'required|string|max:2000',
        ]);

        $this->submissionService->disqualify($submission, $request->user(), $validated['reason']);

        return back()->with('success', 'Submission disqualified.');
    }

    public function reopen(Submission $submission): RedirectResponse
    {
        $this->authorize('reopen', $submission);

        try {
            $this->submissionService->reOpen($submission);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Submission re-opened. The team can now edit it.');
    }
}
