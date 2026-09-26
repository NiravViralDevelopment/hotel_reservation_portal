<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use App\Policies\Concerns\AllowsAuthenticatedUsers;

class CompanyPolicy
{
    use AllowsAuthenticatedUsers;

    public function delete(User $user, Company $company): bool
    {
        return true;
    }
}
