<?php

namespace App\Models;

use App\Support\HotelAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hotel extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'code',
        'name',
        'city',
        'country',
        'rooms',
        'manager_name',
        'manager_user_id',
        'phone',
        'email',
        'status',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rooms' => 'integer',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function managerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'hotel_user')->withTimestamps();
    }

    public function groupBookings(): HasMany
    {
        return $this->hasMany(GroupBooking::class);
    }

    public function enquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class);
    }

    /**
     * Hotels assigned to users cannot be deleted.
     */
    public function canBeDeleted(): bool
    {
        if ($this->relationLoaded('users')) {
            return $this->users->isEmpty();
        }

        if (isset($this->users_count)) {
            return (int) $this->users_count === 0;
        }

        return ! $this->users()->exists();
    }

    /**
     * @param  Builder<Hotel>  $query
     * @return Builder<Hotel>
     */
    public function scopeAccessibleBy(Builder $query, ?User $user = null): Builder
    {
        // Hotel list uses assigned hotels (Administrator = all hotels).
        $ids = HotelAccess::assignedHotelIds($user);
        if ($ids === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn($query->getModel()->getTable().'.id', $ids);
    }
}
