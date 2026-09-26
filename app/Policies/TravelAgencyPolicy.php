<?php

namespace App\Policies;

use App\Policies\Concerns\AllowsAuthenticatedUsers;

class TravelAgencyPolicy
{
    use AllowsAuthenticatedUsers;
}
