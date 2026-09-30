<?php

namespace App\Support;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class HotelAccess
{
    public static function user(?User $user = null): ?User
    {
        return $user ?? Auth::user();
    }

    public static function canAccessAllHotels(?User $user = null): bool
    {
        $user = self::user($user);

        return $user !== null && $user->hasRole('Administrator');
    }

    /**
     * Hotel IDs the user can access for listing / filtering.
     * Administrators get every hotel; others get their assigned hotels.
     *
     * @return list<int>
     */
    public static function hotelIds(?User $user = null): array
    {
        $user = self::user($user);
        if ($user === null) {
            return [];
        }

        if (self::canAccessAllHotels($user)) {
            return Hotel::query()->orderBy('name')->pluck('id')->map(fn ($id) => (int) $id)->all();
        }

        return $user->hotels()->orderBy('name')->pluck('hotels.id')->map(fn ($id) => (int) $id)->all();
    }

    /**
     * @deprecated Use hotelIds() — kept for any leftover callers.
     *
     * @return list<int>
     */
    public static function assignedHotelIds(?User $user = null): array
    {
        return self::hotelIds($user);
    }

    public static function hotels(?User $user = null): Collection
    {
        $ids = self::hotelIds($user);
        if ($ids === []) {
            return collect();
        }

        return Hotel::query()->whereIn('id', $ids)->orderBy('name')->get();
    }

    public static function allows(?User $user, int|string|null $hotelId): bool
    {
        if ($hotelId === null || $hotelId === '') {
            return self::canAccessAllHotels($user);
        }

        $user = self::user($user);
        if ($user === null) {
            return false;
        }

        return in_array((int) $hotelId, self::hotelIds($user), true);
    }

    public static function ensure(?User $user, int|string|null $hotelId): void
    {
        if (! self::allows($user, $hotelId)) {
            abort(403, 'You do not have access to this hotel.');
        }
    }
}
