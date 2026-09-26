<?php

namespace App\Policies;

use App\Models\Enquiry;
use App\Models\User;
use App\Policies\Concerns\AllowsAuthenticatedUsers;

class EnquiryPolicy
{
    use AllowsAuthenticatedUsers;

    public function convert(User $user, Enquiry $enquiry): bool
    {
        return $enquiry->converted_booking_id === null;
    }
}
