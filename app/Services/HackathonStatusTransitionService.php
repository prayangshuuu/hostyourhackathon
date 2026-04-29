<?php

namespace App\Services;

use App\Enums\HackathonStatus;
use App\Models\Hackathon;
use InvalidArgumentException;

class HackathonStatusTransitionService
{
    /**
     * Valid status transitions: current → [allowed next statuses].
     *
     * @var array<string, array<string>>
     */
    protected static array $transitions = [
        'draft'     => ['published'],
        'published' => ['ongoing', 'draft'],
        'ongoing'   => ['ended'],
        'ended'     => ['archived'],
        'archived'  => [],
    ];

    /**
     * Transition a hackathon to a new status.
     *
     * @throws InvalidArgumentException
     */
    public function transition(Hackathon $hackathon, HackathonStatus $newStatus): Hackathon
    {
        $current = $hackathon->status->value;
        $allowed = self::$transitions[$current] ?? [];

        if (! in_array($newStatus->value, $allowed, true)) {
            throw new InvalidArgumentException(
                "Cannot transition from \"{$current}\" to \"{$newStatus->value}\". "
                . "Allowed: " . (empty($allowed) ? 'none' : implode(', ', $allowed)) . "."
            );
        }

        $hackathon->update(['status' => $newStatus]);

        return $hackathon;
    }

    /**
     * Get the next valid status for a hackathon (if any).
     */
    public function nextStatus(Hackathon $hackathon): ?HackathonStatus
    {
        $allowed = self::$transitions[$hackathon->status->value] ?? [];

        return ! empty($allowed) ? HackathonStatus::from($allowed[0]) : null;
    }

    /**
     * Check if a transition is valid.
     */
    public function canTransition(Hackathon $hackathon, HackathonStatus $newStatus): bool
    {
        $allowed = self::$transitions[$hackathon->status->value] ?? [];

        return in_array($newStatus->value, $allowed, true);
    }
}
