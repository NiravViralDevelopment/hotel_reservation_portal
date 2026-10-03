<?php

namespace App\Models;

use App\Models\Concerns\HasPublicUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasPublicUuid;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'category',
        'group_booking_id',
        'uploaded_by',
        'disk',
        'path',
        'mime_type',
        'size',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }

    public function groupBooking(): BelongsTo
    {
        return $this->belongsTo(GroupBooking::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
