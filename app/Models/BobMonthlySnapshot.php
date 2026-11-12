<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BobMonthlySnapshot extends Model
{
    /**
     * Figures for a single calendar year and month.
     * The selected year on the BOB index reads this row; the previous year is the prior row.
     *
     * @var list<string>
     */
    protected $fillable = [
        'year',
        'month',
        'label',
        'bob_nett',
        'bob_nett_actual',
        'stly_bob_nett',
        'adr_nett',
        'stly_adr_nett',
        'room_nights',
        'stly_room_nights',
        'breakfast_revenue',
        'dinner_revenue',
        'dinner_covers',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'month' => 'integer',
            'bob_nett' => 'decimal:2',
            'bob_nett_actual' => 'decimal:2',
            'stly_bob_nett' => 'decimal:2',
            'adr_nett' => 'decimal:2',
            'stly_adr_nett' => 'decimal:2',
            'room_nights' => 'integer',
            'stly_room_nights' => 'integer',
            'breakfast_revenue' => 'decimal:2',
            'dinner_revenue' => 'decimal:2',
            'dinner_covers' => 'integer',
        ];
    }
}
