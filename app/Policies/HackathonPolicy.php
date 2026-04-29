<?php

namespace App\Policies;

use App\Models\Hackathon;
use App\Models\User;

class HackathonPolicy
{
    /**
     * Any organizer or super_admin may view the hackathon list.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['organizer', 'super_admin']);
    }

    /**
     * Creator or co-organizer may view a hackathon.
     */
    public function view(User $user, Hackathon $hackathon): bool
    {
        return $this->isOrganizer($user, $hackathon);
    }

    /**
     * Any organizer or super_admin may create hackathons.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['organizer', 'super_admin']);
    }

    /**
     * Creator or co-organizer may update.
     */
    public function update(User $user, Hackathon $hackathon): bool
    {
        return $this->isOrganizer($user, $hackathon);
    }

    /**
     * Only the creator may delete.
     */
    public function delete(User $user, Hackathon $hackathon): bool
    {
        return $user->id === $hackathon->created_by;
    }

    /**
     * Creator or co-organizer may change status.
     */
    public function changeStatus(User $user, Hackathon $hackathon): bool
    {
        return $this->isOrganizer($user, $hackathon);
    }

    /**
     * Check whether a user is the creator or a co-organizer.
     */
    protected function isOrganizer(User $user, Hackathon $hackathon): bool
    {
        if ($user->id === $hackathon->created_by) {
            return true;
        }

        return $hackathon->organizers()->where('user_id', $user->id)->exists();
    }
}
