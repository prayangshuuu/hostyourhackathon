<?php

namespace App\Enums;

enum HackathonStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Ongoing = 'ongoing';
    case Ended = 'ended';
    case Archived = 'archived';

    /**
     * Get all enum values as an array.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
