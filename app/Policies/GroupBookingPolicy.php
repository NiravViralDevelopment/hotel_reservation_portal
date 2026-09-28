<?php

namespace App\Policies;

use App\Models\GroupBooking;
use App\Models\User;
use App\Support\HotelAccess;

class GroupBookingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('bookings.view');
    }

    public function view(User $user, GroupBooking $groupBooking): bool
    {
        return $user->hasPermissionTo('bookings.view')
            && HotelAccess::allows($user, $groupBooking->hotel_id);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('bookings.create');
    }

    public function update(User $user, GroupBooking $groupBooking): bool
    {
        return $user->hasPermissionTo('bookings.edit')
            && HotelAccess::allows($user, $groupBooking->hotel_id);
    }

    public function delete(User $user, GroupBooking $groupBooking): bool
    {
        return $user->hasPermissionTo('bookings.delete')
            && HotelAccess::allows($user, $groupBooking->hotel_id);
    }

    public function cancel(User $user, GroupBooking $groupBooking): bool
    {
        return $user->hasPermissionTo('bookings.cancel')
            && HotelAccess::allows($user, $groupBooking->hotel_id)
            && ! $groupBooking->isCancelled();
    }
}
