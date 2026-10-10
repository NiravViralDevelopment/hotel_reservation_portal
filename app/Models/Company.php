<?php

namespace App\Models;

use App\Models\Concerns\HasPublicUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasPublicUuid;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'reg_number',
        'vat_number',
        'city',
        'country',
        'address',
        'registered_address',
        'trading_address',
        'status',
        'notes',
    ];

    public function hotels(): HasMany
    {
        return $this->hasMany(Hotel::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(CompanyContract::class);
    }

    public function canBeDeleted(): bool
    {
        if (isset($this->hotels_count)) {
            return (int) $this->hotels_count === 0;
        }

        if ($this->relationLoaded('hotels')) {
            return $this->hotels->isEmpty();
        }

        return ! $this->hotels()->exists();
    }
}
