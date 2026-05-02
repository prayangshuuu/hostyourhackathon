<?php

namespace App\Services;

use App\Enums\TeamRole;
use App\Models\Hackathon;
use App\Models\Segment;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class TeamService
{
    /**
     * Ensure the registration window is currently open for a hackathon.
     *
     * @throws InvalidArgumentException
     */
    public function assertRegistrationOpen(Hackathon $hackathon): void
    {
        if (!$hackathon->isRegistrationOpen()) {
            throw new InvalidArgumentException('Registration is closed for this hackathon.');
        }
    }

    /**
     * Ensure a segment is active and registration is open.
     *
     * @throws InvalidArgumentException
     */
    public function assertSegmentStatus(Segment $segment): void
    {
        if (!$segment->is_active) {
            throw new InvalidArgumentException('This segment is currently inactive.');
        }

        if (!$segment->isRegistrationOpen()) {
            throw new InvalidArgumentException('Registration is closed for this segment.');
        }

        if ($segment->isFull()) {
            throw new InvalidArgumentException('This segment is full.');
        }
    }

    /**
     * Ensure a user doesn't already belong to a team in this hackathon.
     *
     * @throws InvalidArgumentException
     */
    public function assertNoExistingTeam(User $user, Hackathon $hackathon): void
    {
        $exists = TeamMember::whereHas('team', function ($q) use ($hackathon) {
            $q->where('hackathon_id', $hackathon->id)->whereNull('deleted_at');
        })->where('user_id', $user->id)->exists();

        if ($exists) {
            throw new InvalidArgumentException('You are already on a team for this hackathon.');
        }
    }

    /**
     * Ensure the team hasn't reached the hackathon's max size.
     *
     * @throws InvalidArgumentException
     */
    public function assertTeamNotFull(Team $team): void
    {
        $currentSize = $team->members()->count();
        $maxSize = $team->hackathon->max_team_size;

        if ($currentSize >= $maxSize) {
            throw new InvalidArgumentException("This team is full ({$currentSize}/{$maxSize} members).");
        }
    }

    /**
     * Create a team and add the creator as leader.
     */
    public function createTeam(Hackathon $hackathon, User $user, array $data): Team
    {
        return DB::transaction(function () use ($hackathon, $user, $data) {
            $this->assertNoExistingTeam($user, $hackathon);

            $segmentId = $data['segment_id'] ?? null;

            if ($hackathon->hasSegments()) {
                if (!$segmentId) {
                    throw new InvalidArgumentException('You must select a segment.');
                }

                $segment = $hackathon->segments()->findOrFail($segmentId);
                $this->assertSegmentStatus($segment);
            } else {
                $this->assertRegistrationOpen($hackathon);
            }

            $team = Team::create([
                'hackathon_id' => $hackathon->id,
                'segment_id' => $segmentId,
                'name' => $data['name'],
                'invite_code' => $this->generateUniqueInviteCode(),
                'created_by' => $user->id,
            ]);

            // Add creator as leader
            $team->members()->create([
                'user_id' => $user->id,
                'role' => TeamRole::Leader,
                'joined_at' => now(),
            ]);

            return $team;
        });
    }

    /**
     * Add a user to a team via invite code.
     */
    public function joinTeam(Team $team, User $user): TeamMember
    {
        $hackathon = $team->hackathon;

        $this->assertRegistrationOpen($hackathon);
        $this->assertNoExistingTeam($user, $hackathon);
        $this->assertTeamNotFull($team);

        return $team->members()->create([
            'user_id' => $user->id,
            'role' => TeamRole::Member,
            'joined_at' => now(),
        ]);
    }

    /**
     * Remove a member from a team.
     * Only the team leader can remove others. Members can leave themselves.
     *
     * @throws InvalidArgumentException
     */
    public function removeMember(Team $team, User $acting, TeamMember $member): void
    {
        $isLeader = $this->isLeader($team, $acting);
        $isSelf = $member->user_id === $acting->id;

        // Leader can't leave — must disband
        if ($isSelf && $isLeader) {
            throw new InvalidArgumentException('Team leaders cannot leave. Disband the team instead.');
        }

        // Only leader can kick others
        if (! $isSelf && ! $isLeader) {
            throw new InvalidArgumentException('Only the team leader can remove members.');
        }

        $member->delete();
    }

    /**
     * Disband (soft-delete) a team. Only the leader can do this.
     *
     * @throws InvalidArgumentException
     */
    public function disbandTeam(Team $team, User $acting): void
    {
        DB::transaction(function () use ($team, $acting) {
            if (! $this->isLeader($team, $acting)) {
                throw new InvalidArgumentException('Only the team leader can disband the team.');
            }

            $team->members()->delete();
            $team->delete();
        });
    }

    /**
     * Update the team name. Only the leader can do this.
     *
     * @throws InvalidArgumentException
     */
    public function updateName(Team $team, User $acting, string $name): void
    {
        if (! $this->isLeader($team, $acting)) {
            throw new InvalidArgumentException('Only the team leader can rename the team.');
        }

        $team->update(['name' => $name]);
    }

    /**
     * Check if a user is the team leader.
     */
    public function isLeader(Team $team, User $user): bool
    {
        return $team->members()
            ->where('user_id', $user->id)
            ->where('role', TeamRole::Leader)
            ->exists();
    }

    /**
     * Generate a unique invite code.
     */
    protected function generateUniqueInviteCode(): string
    {
        do {
            $code = Str::random(12);
        } while (Team::where('invite_code', $code)->exists());

        return $code;
    }
}
