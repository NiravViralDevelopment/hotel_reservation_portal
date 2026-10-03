<?php

namespace App\Enums;

enum EnquiryStatus: string
{
    case Quoted = 'Quoted';
    case Lost = 'Lost';
    case Chesed = 'Chesed';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
