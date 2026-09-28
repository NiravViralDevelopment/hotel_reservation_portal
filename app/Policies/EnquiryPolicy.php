<?php

namespace App\Policies;

use App\Models\Enquiry;
use App\Models\User;
use App\Support\HotelAccess;

class EnquiryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('enquiries.view');
    }

    public function view(User $user, Enquiry $enquiry): bool
    {
        return $user->hasPermissionTo('enquiries.view')
            && HotelAccess::allows($user, $enquiry->hotel_id);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('enquiries.create');
    }

    public function update(User $user, Enquiry $enquiry): bool
    {
        return $user->hasPermissionTo('enquiries.edit')
            && HotelAccess::allows($user, $enquiry->hotel_id);
    }

    public function delete(User $user, Enquiry $enquiry): bool
    {
        return $user->hasPermissionTo('enquiries.delete')
            && HotelAccess::allows($user, $enquiry->hotel_id);
    }

    public function convert(User $user, Enquiry $enquiry): bool
    {
        return $user->hasPermissionTo('enquiries.convert')
            && HotelAccess::allows($user, $enquiry->hotel_id)
            && $enquiry->converted_booking_id === null;
    }
}
