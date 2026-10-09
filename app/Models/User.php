<?php

namespace App\Models;

use App\Models\Concerns\HasPublicUuid;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasPublicUuid, HasRoles, Notifiable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'job_title',
        'department',
        'status',
        'password',
        'last_login_at',
        'signature_disk',
        'signature_path',
        'signature_original_name',
        'signature_mime_type',
        'signature_size',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'signature_size' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (User $user): void {
            $user->deleteStoredSignature();
        });
    }

    public function hasSignature(): bool
    {
        $path = (string) $this->signature_path;

        return $path !== '' && ! str_contains($path, '..');
    }

    public function deleteStoredSignature(): void
    {
        if (! $this->hasSignature()) {
            return;
        }

        $disk = $this->signature_disk ?: 'local';
        $path = (string) $this->signature_path;

        if (Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }

    public function clearSignatureAttributes(): void
    {
        $this->signature_disk = null;
        $this->signature_path = null;
        $this->signature_original_name = null;
        $this->signature_mime_type = null;
        $this->signature_size = 0;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Users with roles or hotel assignments can only be activated/deactivated, not deleted.
     */
    public function canBeDeleted(): bool
    {
        if ($this->relationLoaded('roles') && $this->relationLoaded('hotels')) {
            return $this->roles->isEmpty() && $this->hotels->isEmpty();
        }

        return ! $this->roles()->exists() && ! $this->hotels()->exists();
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->name)) ?: [];
        $initials = '';

        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= mb_strtoupper(mb_substr($part, 0, 1));
        }

        return $initials !== '' ? $initials : 'U';
    }

    public function hotels(): BelongsToMany
    {
        return $this->belongsToMany(Hotel::class, 'hotel_user')->withTimestamps();
    }

    public function canAccessAllHotels(): bool
    {
        return $this->hasRole('Administrator');
    }

    /**
     * @return list<int>
     */
    public function accessibleHotelIds(): array
    {
        return \App\Support\HotelAccess::hotelIds($this);
    }

    public function hasHotelAccess(int|string|null $hotelId): bool
    {
        return \App\Support\HotelAccess::allows($this, $hotelId);
    }

    public function managedHotels(): HasMany
    {
        return $this->hasMany(Hotel::class, 'manager_user_id');
    }

    public function assignedEnquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class, 'assigned_to');
    }

    public function uploadedDocuments(): HasMany
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }
}
