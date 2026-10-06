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
        'single_from_date',
        'single_to_date',
        'double_rooms',
        'double_rate',
        'double_from_date',
        'double_to_date',
        'triple_rooms',
        'triple_rate',
        'triple_from_date',
        'triple_to_date',
        'daily_room_rates',
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
        'block_id',
        'agency_ref',
        'contact_name',
        'contract_sent_on',
        'contract_received_on',
        'saved_to_doc',
        'payment_term',
        'payment_due_date',
        'payment_status',
        'cxl_due_date',
        'cxl_date',
        'commission',
        'total_rns',
        'bb_revenue',
        'dinner_revenue',
        'nett_rev_ex_vat',
        'booking_update',
        'rooming',
        'invoice_status',
        'invoice_sent_on',
        'invoice_amount',
        'commission_payable_status',
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
            'single_from_date' => 'date',
            'single_to_date' => 'date',
            'double_from_date' => 'date',
            'double_to_date' => 'date',
            'triple_from_date' => 'date',
            'triple_to_date' => 'date',
            'daily_room_rates' => 'array',
            'option_date' => 'date',
            'contract_sent_on' => 'date',
            'contract_received_on' => 'date',
            'payment_due_date' => 'date',
            'cxl_due_date' => 'date',
            'cxl_date' => 'date',
            'invoice_sent_on' => 'date',
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
            'commission' => 'decimal:2',
            'bb_revenue' => 'decimal:2',
            'dinner_revenue' => 'decimal:2',
            'nett_rev_ex_vat' => 'decimal:2',
            'invoice_amount' => 'decimal:2',
            'total_rns' => 'integer',
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

        $column = $query->getModel()->getTable().'.hotel_id';

        if (HotelAccess::currentHotelId($user) === null && HotelAccess::canAccessAllHotels($user)) {
            return $query->where(function (Builder $builder) use ($column, $ids) {
                $builder->whereIn($column, $ids)->orWhereNull($column);
            });
        }

        return $query->whereIn($column, $ids);
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
        return $query->where('is_cancel', true)->where('is_confirm', true);
    }

    /**
     * Enquiries cancelled from the edit flow, before they are a group booking.
     *
     * @param  Builder<Enquiry>  $query
     * @return Builder<Enquiry>
     */
    public function scopeCancelledInquiries(Builder $query): Builder
    {
        return $query->where('is_cancel', true)->where('is_confirm', false);
    }
}
