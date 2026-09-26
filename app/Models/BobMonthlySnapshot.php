<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BobMonthlySnapshot extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'year',
        'month',
        'label',
        'bob_current',
        'bob_previous',
        'bob_variance',
        'stly_bob',
        'adr_current',
        'adr_previous',
        'adr_variance',
        'adr_stly',
        'room_nights_current',
        'room_nights_previous',
        'stly_room_nights',
        'breakfast_revenue',
        'dinner_revenue',
        'dinner_covers',
        'stly_breakfast_revenue',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'month' => 'integer',
            'bob_current' => 'decimal:2',
            'bob_previous' => 'decimal:2',
            'bob_variance' => 'decimal:2',
            'stly_bob' => 'decimal:2',
            'adr_current' => 'decimal:2',
            'adr_previous' => 'decimal:2',
            'adr_variance' => 'decimal:2',
            'adr_stly' => 'decimal:2',
            'room_nights_current' => 'integer',
            'room_nights_previous' => 'integer',
            'stly_room_nights' => 'integer',
            'breakfast_revenue' => 'decimal:2',
            'dinner_revenue' => 'decimal:2',
            'dinner_covers' => 'integer',
            'stly_breakfast_revenue' => 'decimal:2',
        ];
    }
}
