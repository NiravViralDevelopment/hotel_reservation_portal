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
     * Hotels allocated to the user.
     * Administrators always get every hotel.
     *
     * @return list<int>
     */
    public static function assignedHotelIds(?User $user = null): array
    {
        $user = self::user($user);
        if ($user === null) {
            return [];
        }

        if (self::canAccessAllHotels($user)) {
            return Hotel::query()->orderBy('name')->pluck('id')->map(fn ($id) => (int) $id)->all();
        }

        return $user->hotels()
            ->orderBy('name')
            ->pluck('hotels.id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * Hotel IDs used for data filtering.
     * Selected hotel when set; Administrator without a selection sees all hotels.
     *
     * @return list<int>
     */
    public static function hotelIds(?User $user = null): array
    {
        $assigned = self::assignedHotelIds($user);
        if ($assigned === []) {
            return [];
        }

        $current = self::currentHotelId($user);
        if ($current === null) {
            return self::canAccessAllHotels($user) ? $assigned : [];
        }

        return in_array($current, $assigned, true) ? [$current] : [];
    }

    public static function hotels(?User $user = null): Collection
    {
        $ids = self::assignedHotelIds($user);
        if ($ids === []) {
            return collect();
        }

        return Hotel::query()
            ->whereIn('id', $ids)
            ->active()
            ->orderBy('name')
            ->get();
    }

    /**
     * Active hotel IDs available for selection forms.
     *
     * @return list<int>
     */
    public static function selectableHotelIds(?User $user = null, int|string|null $includeId = null): array
    {
        $ids = Hotel::query()
            ->accessibleBy($user)
            ->active()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if ($includeId !== null && $includeId !== '') {
            $includeId = (int) $includeId;
            if ($includeId > 0 && ! in_array($includeId, $ids, true) && self::allows($user, $includeId)) {
                $ids[] = $includeId;
            }
        }

        return $ids;
    }

    public static function currentHotel(?User $user = null): ?Hotel
    {
        $id = self::currentHotelId($user);
        if ($id === null) {
            return null;
        }

        return Hotel::query()->find($id);
    }

    public static function currentHotelId(?User $user = null): ?int
    {
        $assigned = self::assignedHotelIds($user);
        if ($assigned === []) {
            return null;
        }

        $sessionId = session('current_hotel_id');
        if ($sessionId && in_array((int) $sessionId, $assigned, true)) {
            $hotel = Hotel::query()->find((int) $sessionId);
            if ($hotel === null || ! $hotel->isActive()) {
                self::clearCurrentHotel();

                return null;
            }

            return (int) $sessionId;
        }

        return null;
    }

    public static function hasCurrentHotel(?User $user = null): bool
    {
        return self::currentHotelId($user) !== null;
    }

    public static function setCurrentHotelId(int $hotelId, ?User $user = null): void
    {
        self::ensure($user, $hotelId);
        session(['current_hotel_id' => $hotelId]);
    }

    public static function clearCurrentHotel(): void
    {
        session()->forget('current_hotel_id');
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

        return in_array((int) $hotelId, self::assignedHotelIds($user), true);
    }

    public static function ensure(?User $user, int|string|null $hotelId): void
    {
        if (! self::allows($user, $hotelId)) {
            abort(403, 'You do not have access to this hotel.');
        }
    }
}
