<?php

namespace App\Policies;

use App\Policies\Concerns\AllowsAuthenticatedUsers;

class ContactPolicy
{
    use AllowsAuthenticatedUsers;
}
