<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Enums\TeamRole;
use App\Models\Judge;
use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('teams.viewAny') || $user->can('teams.view');
    }

    public function view(User $user, Team $team): bool
    {
        if (! $user->can('teams.view')) {
            return false;
        }

        if ($user->can('teams.viewAll')) {
            return true;
        }

        $hackathon = $team->hackathon;

        if ($hackathon && $hackathon->isOwnedByUser($user)) {
            return true;
        }

        if ($user->hasRole(RoleEnum::Mentor->value) && $user->can('teams.viewAny') && $hackathon && in_array($hackathon->status->value, ['published', 'ongoing', 'ended'], true)) {
            return true;
        }

        return $team->isMember($user);
    }

    public function create(User $user): bool
    {
        return $user->can('teams.create');
    }

    public function update(User $user, Team $team): bool
    {
        if ($user->can('teams.viewAll')) {
            return true;
        }

        $hackathon = $team->hackathon;

        if ($user->can('teams.update') && $hackathon && $hackathon->isOwnedByUser($user)) {
            return true;
        }

        if ($user->can('teams.update') && $this->isLeader($team, $user)) {
            return true;
        }

        return $user->can('teams.create') && $this->isLeader($team, $user);
    }

    public function ban(User $user, Team $team): bool
    {
        if (! $user->can('teams.ban')) {
            return false;
        }

        if ($user->can('teams.viewAll')) {
            return true;
        }

        $hackathon = $team->hackathon;

        return $hackathon && $hackathon->isOwnedByUser($user);
    }

    public function unban(User $user, Team $team): bool
    {
        if (! $user->can('teams.unban')) {
            return false;
        }

        if ($user->can('teams.viewAll')) {
            return true;
        }

        $hackathon = $team->hackathon;

        return $hackathon && $hackathon->isOwnedByUser($user);
    }

    public function banAsJudge(User $user, Team $team): bool
    {
        if (! $user->can('judges.banTeam')) {
            return false;
        }

        return Judge::query()
            ->where('user_id', $user->id)
            ->where('hackathon_id', $team->hackathon_id)
            ->where(function ($q) use ($team) {
                $q->whereNull('segment_id')
                    ->orWhere('segment_id', $team->segment_id);
            })
            ->exists();
    }

    public function delete(User $user, Team $team): bool
    {
        if ($user->can('teams.viewAll')) {
            return true;
        }

        if ($user->can('teams.delete') && $this->isLeader($team, $user)) {
            return true;
        }

        return $user->can('teams.create') && $this->isLeader($team, $user);
    }

    protected function isLeader(Team $team, User $user): bool
    {
        return $team->members()
            ->where('user_id', $user->id)
            ->where('role', TeamRole::Leader)
            ->exists();
    }
}
