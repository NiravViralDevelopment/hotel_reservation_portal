<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupBookingDailyRow extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'group_booking_id',
        'sheet',
        'arrival',
        'departure',
        'nights',
        'total_rns',
        'total_rev',
        'meal_plan',
        'update_note',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'arrival' => 'date',
            'departure' => 'date',
            'nights' => 'integer',
            'total_rns' => 'integer',
            'total_rev' => 'decimal:2',
        ];
    }

    public function groupBooking(): BelongsTo
    {
        return $this->belongsTo(GroupBooking::class);
    }
}
