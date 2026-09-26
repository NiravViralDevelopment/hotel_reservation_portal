<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use App\Policies\Concerns\AllowsAuthenticatedUsers;

class DocumentPolicy
{
    use AllowsAuthenticatedUsers;

    public function download(User $user, Document $document): bool
    {
        return true;
    }
}
