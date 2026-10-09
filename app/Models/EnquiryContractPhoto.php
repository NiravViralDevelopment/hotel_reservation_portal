<?php

namespace App\Models;

use App\Models\Concerns\HasPublicUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class EnquiryContractPhoto extends Model
{
    use HasPublicUuid;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'enquiry_id',
        'uploaded_by',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (EnquiryContractPhoto $photo): void {
            $photo->deleteStoredFile();
        });
    }

    public function enquiry(): BelongsTo
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function hasFile(): bool
    {
        $path = (string) $this->path;

        return $path !== '' && ! str_contains($path, '..');
    }

    public function deleteStoredFile(): void
    {
        if (! $this->hasFile()) {
            return;
        }

        $disk = $this->disk ?: 'local';
        $path = (string) $this->path;

        if (Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }
}
