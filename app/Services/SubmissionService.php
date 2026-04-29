<?php

namespace App\Services;

use App\Enums\TeamRole;
use App\Models\Hackathon;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class SubmissionService
{
    /**
     * Ensure the submission window is currently open for a hackathon.
     *
     * @throws InvalidArgumentException
     */
    public function assertWindowOpen(Hackathon $hackathon): void
    {
        $now = now();

        if ($hackathon->submission_opens_at && $now->lt($hackathon->submission_opens_at)) {
            throw new InvalidArgumentException('The submission window has not opened yet.');
        }

        if ($hackathon->submission_closes_at && $now->gt($hackathon->submission_closes_at)) {
            throw new InvalidArgumentException('The submission window is closed.');
        }
    }

    /**
     * Ensure the team doesn't already have a submission for this hackathon.
     *
     * @throws InvalidArgumentException
     */
    public function assertNoExistingSubmission(Team $team, Hackathon $hackathon): void
    {
        $exists = Submission::where('team_id', $team->id)
            ->where('hackathon_id', $hackathon->id)
            ->exists();

        if ($exists) {
            throw new InvalidArgumentException('Your team already has a submission for this hackathon.');
        }
    }

    /**
     * Ensure the user is the team leader.
     *
     * @throws InvalidArgumentException
     */
    public function assertIsLeader(Team $team, User $user): void
    {
        $isLeader = $team->members()
            ->where('user_id', $user->id)
            ->where('role', TeamRole::Leader)
            ->exists();

        if (! $isLeader) {
            throw new InvalidArgumentException('Only the team leader can manage submissions.');
        }
    }

    /**
     * Ensure the submission is still editable (draft + window open).
     *
     * @throws InvalidArgumentException
     */
    public function assertEditable(Submission $submission): void
    {
        if (! $submission->is_draft) {
            throw new InvalidArgumentException('This submission has been finalized and cannot be edited.');
        }

        $this->assertWindowOpen($submission->hackathon);
    }

    /**
     * Create a new draft submission.
     */
    public function saveDraft(Team $team, Hackathon $hackathon, User $user, array $data): Submission
    {
        $this->assertWindowOpen($hackathon);
        $this->assertIsLeader($team, $user);
        $this->assertNoExistingSubmission($team, $hackathon);

        return Submission::create([
            'team_id' => $team->id,
            'hackathon_id' => $hackathon->id,
            'title' => $data['title'],
            'problem_statement' => $data['problem_statement'],
            'description' => $data['description'],
            'tech_stack' => $data['tech_stack'],
            'demo_url' => $data['demo_url'] ?? null,
            'repo_url' => $data['repo_url'] ?? null,
            'is_draft' => true,
        ]);
    }

    /**
     * Update an existing draft submission.
     */
    public function updateDraft(Submission $submission, User $user, array $data): Submission
    {
        $this->assertEditable($submission);
        $this->assertIsLeader($submission->team, $user);

        $submission->update([
            'title' => $data['title'],
            'problem_statement' => $data['problem_statement'],
            'description' => $data['description'],
            'tech_stack' => $data['tech_stack'],
            'demo_url' => $data['demo_url'] ?? null,
            'repo_url' => $data['repo_url'] ?? null,
        ]);

        return $submission;
    }

    /**
     * Finalize a submission. Irreversible unless organizer re-opens.
     */
    public function finalize(Submission $submission, User $user): void
    {
        $this->assertEditable($submission);
        $this->assertIsLeader($submission->team, $user);

        $submission->update([
            'is_draft' => false,
            'submitted_at' => now(),
        ]);
    }

    /**
     * Re-open a finalized submission (organizer action).
     */
    public function reOpen(Submission $submission): void
    {
        if ($submission->is_draft) {
            throw new InvalidArgumentException('This submission is already a draft.');
        }

        $submission->update([
            'is_draft' => true,
            'submitted_at' => null,
        ]);
    }

    /**
     * Store a file attachment for a submission.
     */
    public function storeFile(Submission $submission, UploadedFile $file): SubmissionFile
    {
        $this->assertEditable($submission);

        // Validate extension
        $extension = strtolower($file->getClientOriginalExtension());
        $allowedExtensions = config('hackathon.submission.allowed_extensions', ['pdf', 'ppt', 'pptx']);

        if (! in_array($extension, $allowedExtensions)) {
            throw new InvalidArgumentException('Only ' . implode(', ', $allowedExtensions) . ' files are allowed.');
        }

        // Validate size
        $maxSizeKb = config('hackathon.submission.max_file_size_kb', 10240);
        $fileSizeKb = $file->getSize() / 1024;

        if ($fileSizeKb > $maxSizeKb) {
            throw new InvalidArgumentException('File size exceeds the maximum of ' . ($maxSizeKb / 1024) . ' MB.');
        }

        // Validate count
        $maxFiles = config('hackathon.submission.max_files', 5);
        $currentCount = $submission->files()->count();

        if ($currentCount >= $maxFiles) {
            throw new InvalidArgumentException("Maximum of {$maxFiles} files per submission.");
        }

        // Store the file
        $directory = "public/submissions/{$submission->id}";
        $path = $file->store($directory);

        return $submission->files()->create([
            'file_path' => $path,
            'file_type' => $extension,
            'original_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
        ]);
    }

    /**
     * Delete a file attachment.
     */
    public function deleteFile(SubmissionFile $file): void
    {
        $submission = $file->submission;
        $this->assertEditable($submission);

        Storage::delete($file->file_path);
        $file->delete();
    }
}
