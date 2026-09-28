<?php

namespace App\Models;

use App\Enums\EnquiryStatus;
use App\Support\HotelAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enquiry extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'ref',
        'year',
        'enquiry_date',
        'day',
        'nights',
        'group_name',
        'travel_agency_id',
        'hotel_id',
        'contact_id',
        'assigned_to',
        'rooms_per_night',
        'single_rooms',
        'single_rate',
        'double_rooms',
        'double_rate',
        'triple_rooms',
        'triple_rate',
        'basis',
        'total_revenue',
        'cxl_policy',
        'option_date',
        'email',
        'remarks',
        'status',
        'converted_booking_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'enquiry_date' => 'date',
            'option_date' => 'date',
            'year' => 'integer',
            'nights' => 'integer',
            'rooms_per_night' => 'integer',
            'single_rooms' => 'integer',
            'double_rooms' => 'integer',
            'triple_rooms' => 'integer',
            'single_rate' => 'decimal:2',
            'double_rate' => 'decimal:2',
            'triple_rate' => 'decimal:2',
            'total_revenue' => 'decimal:2',
            'status' => EnquiryStatus::class,
        ];
    }

    public function travelAgency(): BelongsTo
    {
        return $this->belongsTo(TravelAgency::class);
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function convertedBooking(): BelongsTo
    {
        return $this->belongsTo(GroupBooking::class, 'converted_booking_id');
    }

    /**
     * @param  Builder<Enquiry>  $query
     * @return Builder<Enquiry>
     */
    public function scopeAccessibleBy(Builder $query, ?User $user = null): Builder
    {
        $ids = HotelAccess::hotelIds($user);
        if ($ids === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn($query->getModel()->getTable().'.hotel_id', $ids);
    }
}
