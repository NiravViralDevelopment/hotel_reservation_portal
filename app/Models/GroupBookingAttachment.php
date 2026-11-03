<?php

namespace App\Models;

use App\Models\Concerns\HasPublicUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class GroupBookingAttachment extends Model
{
    use HasPublicUuid;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'enquiry_id',
        'title',
        'feedback',
        'attachment_date',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
        'uploaded_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'attachment_date' => 'date',
            'size' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (GroupBookingAttachment $attachment): void {
            $attachment->deleteStoredFile();
        });
    }

    public function enquiry(): BelongsTo
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function hasFile(): bool
    {
        $path = (string) $this->path;

        return $path !== '' && ! str_contains($path, '..');
    }

    public function isPublicFile(): bool
    {
        return $this->absolutePublicPath() !== null;
    }

    public function publicUrl(): string
    {
        return '/'.ltrim(str_replace('\\', '/', (string) $this->path), '/');
    }

    public function fileUrl(Enquiry $enquiry): string
    {
        if ($this->isPublicFile()) {
            return $this->publicUrl();
        }

        return route('group-bookings.attachments.download', [$enquiry, $this]);
    }

    public function absolutePublicPath(): ?string
    {
        $relative = str_replace('\\', '/', (string) $this->path);

        if ($relative === '' || str_contains($relative, '..') || ! str_starts_with($relative, 'group-booking-attachments/')) {
            return null;
        }

        $root = realpath(public_path('group-booking-attachments'));
        $fullPath = realpath(public_path($relative));
        $rootPrefix = $root === false ? '' : rtrim($root, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

        if ($root === false || $fullPath === false || ! str_starts_with($fullPath, $rootPrefix) || ! is_file($fullPath)) {
            return null;
        }

        return $fullPath;
    }

    public function deleteStoredFile(): void
    {
        if (! $this->hasFile()) {
            return;
        }

        $publicPath = $this->absolutePublicPath();
        if ($publicPath !== null) {
            unlink($publicPath);

            return;
        }

        $disk = $this->disk ?: 'local';
        if ($disk === 'public') {
            return;
        }

        $path = (string) $this->path;

        if (Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }
}
