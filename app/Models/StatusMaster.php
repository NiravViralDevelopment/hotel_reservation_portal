<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StatusMaster extends Model
{
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
}
