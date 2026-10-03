<?php

namespace App\Models;

use App\Models\Concerns\HasPublicUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TravelAgency extends Model
{
    use HasPublicUuid;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'code',
        'contact_name',
        'email',
        'phone',
        'city',
        'country',
        'status',
    ];

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function enquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class);
    }

    public function confirmedBookings(): HasMany
    {
        return $this->hasMany(Enquiry::class)->where('is_confirm', true)->where('is_cancel', false);
    }
}
