<?php

namespace App\Enums;

enum SponsorTier: string
{
    case Title = 'title';
    case Gold = 'gold';
    case Silver = 'silver';
    case Bronze = 'bronze';

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
