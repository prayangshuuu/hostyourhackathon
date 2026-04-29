<?php

namespace App\Enums;

enum AnnouncementVisibility: string
{
    case All = 'all';
    case Registered = 'registered';
    case Segment = 'segment';

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
