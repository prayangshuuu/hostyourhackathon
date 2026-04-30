<?php

namespace App\Enums;

enum BanType: string
{
    case Manual = 'manual';
    case TeamBan = 'team_ban';

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
