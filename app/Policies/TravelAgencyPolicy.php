<?php

namespace App\Policies;

use App\Models\TravelAgency;
use App\Models\User;

class TravelAgencyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('agencies.view');
    }

    public function view(User $user, TravelAgency $travelAgency): bool
    {
        return $user->hasPermissionTo('agencies.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('agencies.create');
    }

    public function update(User $user, TravelAgency $travelAgency): bool
    {
        return $user->hasPermissionTo('agencies.edit');
    }

    public function delete(User $user, TravelAgency $travelAgency): bool
    {
        return $user->hasPermissionTo('agencies.delete');
    }
}
