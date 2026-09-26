<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\AllowsAuthenticatedUsers;

class UserPolicy
{
    use AllowsAuthenticatedUsers;

    public function delete(User $user, User $model): bool
    {
        return $user->id !== $model->id;
    }
}
