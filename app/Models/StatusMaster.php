<?php

namespace App\Models;

use App\Models\Concerns\HasPublicUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StatusMaster extends Model
{
    use HasPublicUuid;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'status',
    ];

    /**
     * @param  Builder<StatusMaster>  $query
     * @return Builder<StatusMaster>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Statuses assigned to any enquiry cannot be deleted.
     */
    public function isUsedByEnquiries(): bool
    {
        return Enquiry::query()
            ->where('status', $this->title)
            ->exists();
    }

    public function canBeDeleted(): bool
    {
        return ! $this->isUsedByEnquiries();
    }
}
