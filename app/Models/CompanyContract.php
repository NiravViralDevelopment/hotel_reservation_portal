<?php

namespace App\Models;

use App\Models\Concerns\HasPublicUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyContract extends Model
{
    use HasPublicUuid;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'title',
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
            'size' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (CompanyContract $contract): void {
            $fullPath = $contract->absolutePathOrNull();

            if ($fullPath !== null && is_file($fullPath)) {
                unlink($fullPath);
            }
        });
    }

    public function previewUrl(): string
    {
        return '/'.ltrim(str_replace('\\', '/', (string) $this->path), '/');
    }

    public function absolutePath(): string
    {
        $fullPath = $this->absolutePathOrNull();

        abort_unless($fullPath !== null && is_file($fullPath), 404);

        return $fullPath;
    }

    public function absolutePathOrNull(): ?string
    {
        $relative = str_replace('\\', '/', (string) $this->path);

        if ($relative === '' || str_contains($relative, '..') || ! str_starts_with($relative, 'company-contracts/')) {
            return null;
        }

        $root = realpath(public_path('company-contracts'));
        $fullPath = realpath(public_path($relative));
        $rootPrefix = $root === false ? '' : rtrim($root, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

        if ($root === false || $fullPath === false || ! str_starts_with($fullPath, $rootPrefix)) {
            return null;
        }

        return $fullPath;
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
