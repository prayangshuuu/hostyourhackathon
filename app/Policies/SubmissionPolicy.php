<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Enums\TeamRole;
use App\Models\Judge;
use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('submissions.viewAny');
    }

    public function view(User $user, Submission $submission): bool
    {
        if (! $user->can('submissions.view')) {
            return false;
        }

        if ($user->can('submissions.viewAll')) {
            return true;
        }

        $team = $submission->team;
        if ($team && $team->isMember($user)) {
            return true;
        }

        if ($this->judgeCanViewSubmission($user, $submission)) {
            return true;
        }

        return $this->mentorCanViewSubmission($user, $submission);
    }

    protected function judgeCanViewSubmission(User $user, Submission $submission): bool
    {
        if (! $user->hasRole(RoleEnum::Judge->value)) {
            return false;
        }

        return Judge::query()
            ->where('user_id', $user->id)
            ->where('hackathon_id', $submission->hackathon_id)
            ->where(function ($q) use ($submission) {
                $q->whereNull('segment_id')
                    ->orWhere('segment_id', $submission->segment_id);
            })
            ->exists();
    }

    protected function mentorCanViewSubmission(User $user, Submission $submission): bool
    {
        if (! $user->hasRole(RoleEnum::Mentor->value) || ! $user->can('submissions.viewAny')) {
            return false;
        }

        $hackathon = $submission->hackathon;

        return $hackathon && in_array($hackathon->status->value, ['published', 'ongoing', 'ended'], true);
    }

    public function create(User $user): bool
    {
        return $user->can('submissions.create');
    }

    public function update(User $user, Submission $submission): bool
    {
        if (! $user->can('submissions.update')) {
            return false;
        }

        if ($user->can('submissions.viewAll')) {
            return $submission->isEditable();
        }

        $team = $submission->team;

        if (! $team || ! $this->isLeader($team, $user)) {
            return false;
        }

        return $submission->isEditable();
    }

    public function reopen(User $user, Submission $submission): bool
    {
        if (! $user->can('submissions.reopen')) {
            return false;
        }

        if ($user->can('submissions.viewAll')) {
            return true;
        }

        return (bool) ($submission->hackathon?->isOwnedByUser($user));
    }

    public function disqualify(User $user, Submission $submission): bool
    {
        if (! $user->can('submissions.disqualify')) {
            return false;
        }

        if ($user->can('submissions.viewAll')) {
            return true;
        }

        return (bool) ($submission->hackathon?->isOwnedByUser($user));
    }

    protected function isLeader(\App\Models\Team $team, User $user): bool
    {
        return $team->members()
            ->where('user_id', $user->id)
            ->where('role', TeamRole::Leader)
            ->exists();
    }
}
