<?php

namespace App\Enums;

enum TeamRole: string
{
    case Leader = 'leader';
    case Member = 'member';

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
