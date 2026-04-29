<?php

namespace App\Enums;

enum RoleEnum: string
{
    case SuperAdmin = 'super_admin';
    case Organizer = 'organizer';
    case Participant = 'participant';
    case Judge = 'judge';
    case Mentor = 'mentor';

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
