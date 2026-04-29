<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Services\SubmissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubmissionFileController extends Controller
{
    public function __construct(
        protected SubmissionService $submissionService,
    ) {}

    /**
     * Upload a file to a submission.
     */
    public function store(Request $request, Submission $submission): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file'],
        ]);

        try {
            $this->submissionService->storeFile($submission, $request->file('file'));
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'File uploaded successfully.');
    }

    /**
     * Delete a submission file.
     */
    public function destroy(SubmissionFile $submissionFile): RedirectResponse
    {
        try {
            $this->submissionService->deleteFile($submissionFile);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'File deleted.');
    }
}
