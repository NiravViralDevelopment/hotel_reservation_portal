<?php

namespace App\Enums;

enum EnquiryStatus: string
{
    case New = 'new';
    case FollowUp = 'follow_up';
    case Quoted = 'quoted';
    case Confirmed = 'confirmed';
    case Lost = 'lost';
    case Cancelled = 'cancelled';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
