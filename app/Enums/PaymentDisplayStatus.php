<?php

namespace App\Enums;

enum PaymentDisplayStatus: string
{
    case Paid = 'Paid';
    case Partial = 'Partial';
    case Unpaid = 'Unpaid';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
