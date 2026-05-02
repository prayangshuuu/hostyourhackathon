<?php

namespace App\Services;

use App\Enums\TeamRole;
use App\Models\Hackathon;
use App\Models\Segment;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class SubmissionService
{
    public function __construct(protected SettingService $settings) {}

    public function assertWindowOpen(Hackathon $hackathon, ?Segment $segment = null): void
    {
        $isOpen = $segment ? $segment->isSubmissionOpen() : $hackathon->isSubmissionOpen();

        if (!$isOpen) {
            throw new InvalidArgumentException('The submission window is closed.');
        }
    }

    public function assertSegmentSubmissionOpen(Segment $segment): void
    {
        if (!$segment->isSubmissionOpen()) {
            throw new InvalidArgumentException('The submission window for this segment is closed.');
        }
    }

    public function assertNoExistingSubmission(Team $team, Hackathon $hackathon): void
    {
        $segment = $team->segment;
        $limit = $segment?->submission_limit ?? 1; // Default to 1 if no segment or no limit? Wait, request says null = unlimited.
        
        // Actually, if it's null, it's unlimited. But usually it's 1.
        // The request says: "submission_limit (unsignedSmallInteger nullable) — max submissions per team in this segment (null = unlimited)"
        
        if ($segment && $segment->submission_limit !== null) {
            $count = Submission::where('team_id', $team->id)
                ->where('segment_id', $segment->id)
                ->count();
            
            if ($count >= $segment->submission_limit) {
                throw new InvalidArgumentException("Submission limit reached for this segment ({$segment->submission_limit}).");
            }
        } else {
            // Default behavior for no segment or unlimited segment: 1 per team per hackathon?
            // Let's stick to 1 if no segments, or use the limit if segment exists.
            $exists = Submission::where('team_id', $team->id)
                ->where('hackathon_id', $hackathon->id)
                ->exists();

            if ($exists && (!$segment || $segment->submission_limit !== null)) {
                throw new InvalidArgumentException('Your team already has a submission for this hackathon.');
            }
        }
    }

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

    public function assertEditable(Submission $submission): void
    {
        if (! $submission->isEditable()) {
            if (! $submission->hackathon?->isSubmissionOpen()) {
                throw new InvalidArgumentException('The submission window is closed.');
            }

            throw new InvalidArgumentException('This submission cannot be edited.');
        }
    }

    public function saveDraft(Team $team, Hackathon $hackathon, User $user, array $data): Submission
    {
        $this->assertWindowOpen($hackathon, $team->segment);
        $this->assertIsLeader($team, $user);
        $this->assertNoExistingSubmission($team, $hackathon);

        return Submission::create([
            'team_id' => $team->id,
            'hackathon_id' => $hackathon->id,
            'segment_id' => $team->segment_id,
            'title' => $data['title'],
            'problem_statement' => $data['problem_statement'],
            'description' => $data['description'],
            'tech_stack' => $data['tech_stack'] ?? null,
            'demo_url' => $data['demo_url'] ?? null,
            'repo_url' => $data['repo_url'] ?? null,
            'is_draft' => true,
        ]);
    }

    public function updateDraft(Submission $submission, User $user, array $data): Submission
    {
        $this->assertEditable($submission);
        $this->assertIsLeader($submission->team, $user);

        $submission->update([
            'title' => $data['title'],
            'problem_statement' => $data['problem_statement'],
            'description' => $data['description'],
            'tech_stack' => $data['tech_stack'] ?? null,
            'demo_url' => $data['demo_url'] ?? null,
            'repo_url' => $data['repo_url'] ?? null,
        ]);

        return $submission;
    }

    public function finalize(Submission $submission, User $user): void
    {
        $this->assertEditable($submission);
        $this->assertIsLeader($submission->team, $user);

        $submission->update([
            'is_draft' => false,
            'submitted_at' => now(),
            're_open_submission' => false,
        ]);
    }

    public function reOpen(Submission $submission): void
    {
        if ($submission->is_draft) {
            throw new InvalidArgumentException('This submission is already a draft.');
        }

        $submission->update([
            're_open_submission' => true,
        ]);
    }

    public function disqualify(Submission $submission, User $user, string $reason): void
    {
        $submission->update([
            'disqualified' => true,
            'disqualified_reason' => $reason,
            'disqualified_by' => $user->id,
            'disqualified_at' => now(),
        ]);
    }

    public function storeFile(Submission $submission, UploadedFile $file): SubmissionFile
    {
        $this->assertEditable($submission);

        $extension = strtolower($file->getClientOriginalExtension());
        $allowedExtensions = config('hackathon.submission.allowed_extensions', ['pdf', 'ppt', 'pptx']);

        if (! in_array($extension, $allowedExtensions, true)) {
            throw new InvalidArgumentException('Only '.implode(', ', $allowedExtensions).' files are allowed.');
        }

        $maxSizeMb = $this->settings->get('max_file_upload_mb', 10);
        $maxSizeKb = $maxSizeMb * 1024;
        $fileSizeKb = (int) ceil($file->getSize() / 1024);

        if ($fileSizeKb > $maxSizeKb) {
            throw new InvalidArgumentException('File size exceeds the maximum of '.($maxSizeKb / 1024).' MB.');
        }

        $maxFiles = config('hackathon.submission.max_files', 5);
        $currentCount = $submission->files()->count();

        if ($currentCount >= $maxFiles) {
            throw new InvalidArgumentException("Maximum of {$maxFiles} files per submission.");
        }

        $directory = "public/submissions/{$submission->id}";
        $path = $file->store($directory);

        return $submission->files()->create([
            'file_path' => $path,
            'file_type' => $extension,
            'original_name' => $file->getClientOriginalName(),
            'file_size_kb' => max(0, $fileSizeKb),
        ]);
    }

    public function deleteFile(SubmissionFile $file): void
    {
        $submission = $file->submission;
        $this->assertEditable($submission);

        Storage::delete($file->file_path);
        $file->delete();
    }
}
