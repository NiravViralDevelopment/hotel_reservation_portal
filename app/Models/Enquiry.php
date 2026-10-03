<?php

namespace App\Models;

use App\Models\Concerns\HasPublicUuid;
use App\Support\HotelAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enquiry extends Model
{
    use HasPublicUuid;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'ref',
        'year',
        'enquiry_date',
        'response_date',
        'day',
        'check_in',
        'check_in_day',
        'check_out',
        'nights',
        'days',
        'breakdown',
        'group_name',
        'client',
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
        'has_tax',
        'tax_percentage',
        'tax_revenue',
        'cxl_policy',
        'option_date',
        'email',
        'mobile',
        'source',
        'service_person',
        'subject',
        'booking_msg',
        'adults_price',
        'child_price',
        'adults_extra',
        'child_extra',
        'total_pax',
        'agent_price',
        'our_cost',
        'package_price',
        'gst_policy',
        'total_price',
        'net_price',
        'advance',
        'remaining',
        'agent_comm_percent',
        'agent_comm_amount',
        'payable_to_agent',
        'service_total',
        'total_tax',
        'grand_total',
        'remarks',
        'client_response',
        'status',
        'is_confirm',
        'is_cancel',
        'cancellation_reason',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'enquiry_date' => 'date',
            'response_date' => 'date',
            'check_in' => 'date',
            'check_out' => 'date',
            'option_date' => 'date',
            'year' => 'integer',
            'nights' => 'integer',
            'days' => 'integer',
            'rooms_per_night' => 'integer',
            'single_rooms' => 'integer',
            'double_rooms' => 'integer',
            'triple_rooms' => 'integer',
            'total_pax' => 'integer',
            'single_rate' => 'decimal:2',
            'double_rate' => 'decimal:2',
            'triple_rate' => 'decimal:2',
            'total_revenue' => 'decimal:2',
            'has_tax' => 'boolean',
            'is_confirm' => 'boolean',
            'is_cancel' => 'boolean',
            'tax_percentage' => 'decimal:2',
            'tax_revenue' => 'decimal:2',
            'adults_price' => 'decimal:2',
            'child_price' => 'decimal:2',
            'adults_extra' => 'decimal:2',
            'child_extra' => 'decimal:2',
            'agent_price' => 'decimal:2',
            'our_cost' => 'decimal:2',
            'package_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'net_price' => 'decimal:2',
            'advance' => 'decimal:2',
            'remaining' => 'decimal:2',
            'agent_comm_percent' => 'decimal:2',
            'agent_comm_amount' => 'decimal:2',
            'payable_to_agent' => 'decimal:2',
            'service_total' => 'decimal:2',
            'total_tax' => 'decimal:2',
            'grand_total' => 'decimal:2',
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

    public function responses(): HasMany
    {
        return $this->hasMany(EnquiryResponse::class)->latest();
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

    /**
     * @param  Builder<Enquiry>  $query
     * @return Builder<Enquiry>
     */
    public function scopeOpenPipeline(Builder $query): Builder
    {
        return $query->where('is_confirm', false)->where('is_cancel', false);
    }

    /**
     * @param  Builder<Enquiry>  $query
     * @return Builder<Enquiry>
     */
    public function scopeGroupBookings(Builder $query): Builder
    {
        return $query->where('is_confirm', true)->where('is_cancel', false);
    }

    /**
     * @param  Builder<Enquiry>  $query
     * @return Builder<Enquiry>
     */
    public function scopeCancelledBookings(Builder $query): Builder
    {
        return $query->where('is_cancel', true);
    }
}
