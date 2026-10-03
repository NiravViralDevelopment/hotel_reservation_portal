<?php

namespace App\Models;

use App\Models\Concerns\HasPublicUuid;
use App\Support\HotelAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hotel extends Model
{
    use HasPublicUuid;

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

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * @param  Builder<Hotel>  $query
     * @return Builder<Hotel>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where($query->getModel()->getTable().'.status', 'active');
    }

    /**
     * Hotels for dropdowns / assignment UIs.
     * Only active hotels, plus an optional currently selected hotel (even if inactive).
     *
     * @param  list<int|string>|int|string|null  $includeIds
     * @return \Illuminate\Support\Collection<int, Hotel>
     */
    public static function optionsForSelect(mixed $includeIds = null, ?User $user = null): \Illuminate\Support\Collection
    {
        $include = collect(is_array($includeIds) ? $includeIds : [$includeIds])
            ->filter(fn ($id) => $id !== null && $id !== '')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $hotels = static::query()
            ->accessibleBy($user)
            ->active()
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'status']);

        if ($include !== []) {
            $missingIds = array_values(array_diff($include, $hotels->pluck('id')->map(fn ($id) => (int) $id)->all()));
            if ($missingIds !== []) {
                $extra = static::query()
                    ->accessibleBy($user)
                    ->whereIn('id', $missingIds)
                    ->orderBy('name')
                    ->get(['id', 'name', 'code', 'status']);

                $hotels = $hotels->concat($extra)->unique('id')->sortBy('name')->values();
            }
        }

        return $hotels;
    }

    /**
     * Hotels for dropdowns when full hotel list is allowed (e.g. user assignment).
     *
     * @param  list<int|string>|int|string|null  $includeIds
     * @return \Illuminate\Support\Collection<int, Hotel>
     */
    public static function optionsForAssignment(mixed $includeIds = null): \Illuminate\Support\Collection
    {
        $include = collect(is_array($includeIds) ? $includeIds : [$includeIds])
            ->filter(fn ($id) => $id !== null && $id !== '')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        return static::query()
            ->where(function (Builder $query) use ($include) {
                $query->where('status', 'active');
                if ($include !== []) {
                    $query->orWhereIn('id', $include);
                }
            })
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'status']);
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
