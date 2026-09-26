<?php

namespace App\Policies;

use App\Models\GroupBooking;
use App\Models\User;
use App\Policies\Concerns\AllowsAuthenticatedUsers;

class GroupBookingPolicy
{
    use AllowsAuthenticatedUsers;

    public function cancel(User $user, GroupBooking $groupBooking): bool
    {
        return ! $groupBooking->isCancelled();
    }
}
