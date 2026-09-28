<?php

namespace App\Policies;

use App\Models\Hotel;
use App\Models\User;
use App\Support\HotelAccess;

class HotelPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('hotels.view');
    }

    public function view(User $user, Hotel $hotel): bool
    {
        return $user->hasPermissionTo('hotels.view')
            && HotelAccess::allows($user, $hotel->id);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('hotels.create');
    }

    public function update(User $user, Hotel $hotel): bool
    {
        return $user->hasPermissionTo('hotels.edit')
            && HotelAccess::allows($user, $hotel->id);
    }

    public function delete(User $user, Hotel $hotel): bool
    {
        return $user->hasPermissionTo('hotels.delete')
            && HotelAccess::allows($user, $hotel->id);
    }
}
