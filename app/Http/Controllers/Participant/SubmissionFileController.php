<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Services\SettingService;
use App\Services\SubmissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $this->authorize('update', $submission);

        $maxMb = app(SettingService::class)->get('max_file_upload_mb', 10);

        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,ppt,pptx', "max:{$maxMb}000"],
        ]);

        try {
            $this->submissionService->storeFile($submission, $request->file('file'));
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'File uploaded successfully.');
    }

    /**
     * Download a submission attachment.
     */
    public function download(Submission $submission, SubmissionFile $submissionFile)
    {
        $this->authorize('view', $submission);

        if ((int) $submissionFile->submission_id !== (int) $submission->id) {
            abort(404);
        }

        if (! Storage::exists($submissionFile->file_path)) {
            abort(404);
        }

        return Storage::download($submissionFile->file_path, $submissionFile->original_name);
    }

    /**
     * Delete a submission file.
     */
    public function destroy(SubmissionFile $submissionFile): RedirectResponse
    {
        $this->authorize('update', $submissionFile->submission);

        try {
            $this->submissionService->deleteFile($submissionFile);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'File deleted.');
    }
}
