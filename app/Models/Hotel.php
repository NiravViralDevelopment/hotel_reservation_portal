<?php

namespace App\Models;

use App\Models\Concerns\HasPublicUuid;
use App\Support\HotelAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

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
        'document_disk',
        'document_path',
        'document_original_name',
        'document_mime_type',
        'document_size',
        'logo_disk',
        'logo_path',
        'logo_original_name',
        'logo_mime_type',
        'logo_size',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rooms' => 'integer',
            'document_size' => 'integer',
            'logo_size' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (Hotel $hotel): void {
            $hotel->deleteStoredDocument();
            $hotel->deleteStoredLogo();
        });
    }

    public function hasDocument(): bool
    {
        $path = (string) $this->document_path;

        return $path !== '' && ! str_contains($path, '..');
    }

    public function deleteStoredDocument(): void
    {
        if (! $this->hasDocument()) {
            return;
        }

        $disk = $this->document_disk ?: 'local';
        $path = (string) $this->document_path;

        if (Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }

    public function clearDocumentAttributes(): void
    {
        $this->document_disk = null;
        $this->document_path = null;
        $this->document_original_name = null;
        $this->document_mime_type = null;
        $this->document_size = 0;
    }

    public function hasLogo(): bool
    {
        $path = (string) $this->logo_path;

        return $path !== '' && ! str_contains($path, '..');
    }

    public function deleteStoredLogo(): void
    {
        if (! $this->hasLogo()) {
            return;
        }

        $disk = $this->logo_disk ?: 'local';
        $path = (string) $this->logo_path;

        if (Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }

    public function clearLogoAttributes(): void
    {
        $this->logo_disk = null;
        $this->logo_path = null;
        $this->logo_original_name = null;
        $this->logo_mime_type = null;
        $this->logo_size = 0;
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

    public function enquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class);
    }

    public function confirmedBookings(): HasMany
    {
        return $this->hasMany(Enquiry::class)->where('is_confirm', true)->where('is_cancel', false);
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
     * Hotels in use (users, enquiries, or group bookings) cannot be deleted.
     */
    public function canBeDeleted(): bool
    {
        if (isset($this->users_count) || isset($this->enquiries_count) || isset($this->group_bookings_count)) {
            return (int) ($this->users_count ?? 0) === 0
                && (int) ($this->enquiries_count ?? 0) === 0
                && (int) ($this->group_bookings_count ?? 0) === 0;
        }

        if ($this->relationLoaded('users') && $this->users->isNotEmpty()) {
            return false;
        }

        if ($this->relationLoaded('enquiries') && $this->enquiries->isNotEmpty()) {
            return false;
        }

        if ($this->relationLoaded('groupBookings') && $this->groupBookings->isNotEmpty()) {
            return false;
        }

        return ! $this->users()->exists()
            && ! $this->enquiries()->exists()
            && ! $this->groupBookings()->exists();
    }

    public function usageBlockReason(): ?string
    {
        if ($this->canBeDeleted()) {
            return null;
        }

        return 'This hotel is in use and cannot be deleted.';
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
