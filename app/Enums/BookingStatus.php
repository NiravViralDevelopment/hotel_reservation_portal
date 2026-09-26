<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Provisional = 'Provisional';
    case Pending = 'Pending';
    case Def = 'DEF';
    case Confirmed = 'Confirmed';
    case Cancelled = 'Cancelled';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
