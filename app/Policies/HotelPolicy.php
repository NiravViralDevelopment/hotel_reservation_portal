<?php

namespace App\Policies;

use App\Policies\Concerns\AllowsAuthenticatedUsers;

class HotelPolicy
{
    use AllowsAuthenticatedUsers;
}
