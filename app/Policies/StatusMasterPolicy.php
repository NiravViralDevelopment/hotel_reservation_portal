<?php

namespace App\Policies;

use App\Models\StatusMaster;
use App\Models\User;

class StatusMasterPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('statuses.view');
    }

    public function view(User $user, StatusMaster $statusMaster): bool
    {
        return $user->hasPermissionTo('statuses.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('statuses.create');
    }

    public function update(User $user, StatusMaster $statusMaster): bool
    {
        return $user->hasPermissionTo('statuses.edit');
    }

    public function delete(User $user, StatusMaster $statusMaster): bool
    {
        return $user->hasPermissionTo('statuses.delete');
    }
}
