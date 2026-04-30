<?php

namespace App\Policies;

use App\Models\Hackathon;
use App\Models\User;

class HackathonPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Hackathon $hackathon): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->can('hackathons.create');
    }

    public function update(User $user, Hackathon $hackathon): bool
    {
        if (! $user->can('hackathons.update')) {
            return false;
        }

        return $this->ownsOrOrganizesOrViewAll($user, $hackathon);
    }

    public function delete(User $user, Hackathon $hackathon): bool
    {
        if (! $user->can('hackathons.delete')) {
            return false;
        }

        return $this->ownsOrOrganizesOrViewAll($user, $hackathon);
    }

    public function forceDelete(User $user, Hackathon $hackathon): bool
    {
        return $user->can('hackathons.forceDelete');
    }

    public function restore(User $user, Hackathon $hackathon): bool
    {
        return $user->can('hackathons.restore');
    }

    public function changeStatus(User $user, Hackathon $hackathon): bool
    {
        if (! $user->can('hackathons.changeStatus')) {
            return false;
        }

        return $this->ownsOrOrganizesOrViewAll($user, $hackathon);
    }

    public function viewAll(User $user): bool
    {
        return $user->can('hackathons.viewAll');
    }

    protected function ownsOrOrganizesOrViewAll(User $user, Hackathon $hackathon): bool
    {
        if ($user->can('hackathons.viewAll')) {
            return true;
        }

        if ((int) $hackathon->created_by === (int) $user->id) {
            return true;
        }

        return $hackathon->organizers()->where('users.id', $user->id)->exists();
    }
}
