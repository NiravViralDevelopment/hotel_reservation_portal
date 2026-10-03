<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\PaymentDisplayStatus;
use App\Models\Concerns\HasPublicUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroupBooking extends Model
{
    use HasPublicUuid;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'block_id',
        'enquiry_id',
        'company_id',
        'hotel_id',
        'travel_agency_id',
        'contact_id',
        'created_by',
        'group_name',
        'client',
        'agency_name',
        'contact_name',
        'email',
        'arrival',
        'departure',
        'arrival_day',
        'nights',
        'status',
        'contract_sent',
        'contract_recd',
        'saved_doc',
        'payment_term',
        'due_date',
        'payment_status',
        'payment_status_display',
        'cxl_policy',
        'cxl_due_date',
        'cxl_date',
        'commission',
        'single_rns',
        'single_rate',
        'double_rns',
        'double_rate',
        'triple_rns',
        'triple_rate',
        'total_rns',
        'rooms',
        'pax',
        'revenue',
        'bb_revenue',
        'dinner_revenue',
        'nett_rev',
        'meal_plan',
        'rooming_status',
        'invoice_status',
        'invoice_date',
        'invoice_amount',
        'commission_payable',
        'opera_cross_check',
        'update_notes',
        'internal_notes',
        'cancelled_at',
        'cancellation_reason',
        'revenue_lost',
        'city_tax',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'arrival' => 'date',
            'departure' => 'date',
            'contract_sent' => 'date',
            'contract_recd' => 'date',
            'due_date' => 'date',
            'cxl_due_date' => 'date',
            'cxl_date' => 'date',
            'invoice_date' => 'date',
            'cancelled_at' => 'datetime',
            'nights' => 'integer',
            'single_rns' => 'integer',
            'double_rns' => 'integer',
            'triple_rns' => 'integer',
            'total_rns' => 'integer',
            'rooms' => 'integer',
            'pax' => 'integer',
            'commission' => 'decimal:2',
            'single_rate' => 'decimal:2',
            'double_rate' => 'decimal:2',
            'triple_rate' => 'decimal:2',
            'revenue' => 'decimal:2',
            'bb_revenue' => 'decimal:2',
            'dinner_revenue' => 'decimal:2',
            'nett_rev' => 'decimal:2',
            'invoice_amount' => 'decimal:2',
            'commission_payable' => 'decimal:2',
            'revenue_lost' => 'decimal:2',
            'city_tax' => 'decimal:2',
            'status' => BookingStatus::class,
            'payment_status_display' => PaymentDisplayStatus::class,
        ];
    }

    public function enquiry(): BelongsTo
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function travelAgency(): BelongsTo
    {
        return $this->belongsTo(TravelAgency::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function dailyRows(): HasMany
    {
        return $this->hasMany(GroupBookingDailyRow::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * @param  Builder<GroupBooking>  $query
     * @return Builder<GroupBooking>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->whereNull('cancelled_at')
            ->where('status', '!=', BookingStatus::Cancelled->value);
    }

    /**
     * @param  Builder<GroupBooking>  $query
     * @return Builder<GroupBooking>
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereNotNull('cancelled_at')
                ->orWhere('status', BookingStatus::Cancelled->value);
        });
    }

    /**
     * @param  Builder<GroupBooking>  $query
     * @return Builder<GroupBooking>
     */
    public function scopeArrivingOn(Builder $query, string $date): Builder
    {
        return $query->whereDate('arrival', $date);
    }

    /**
     * @param  Builder<GroupBooking>  $query
     * @return Builder<GroupBooking>
     */
    public function scopeDepartingOn(Builder $query, string $date): Builder
    {
        return $query->whereDate('departure', $date);
    }

    public function isCancelled(): bool
    {
        return $this->cancelled_at !== null
            || $this->status === BookingStatus::Cancelled;
    }

    /**
     * @param  Builder<GroupBooking>  $query
     * @return Builder<GroupBooking>
     */
    public function scopeAccessibleBy(Builder $query, ?User $user = null): Builder
    {
        $ids = \App\Support\HotelAccess::hotelIds($user);
        if ($ids === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn($query->getModel()->getTable().'.hotel_id', $ids);
    }
}
