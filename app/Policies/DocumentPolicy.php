<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use App\Support\HotelAccess;

class DocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('documents.view');
    }

    public function view(User $user, Document $document): bool
    {
        return $user->hasPermissionTo('documents.view')
            && $this->canAccessDocument($user, $document);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('documents.upload');
    }

    public function update(User $user, Document $document): bool
    {
        return $user->hasPermissionTo('documents.upload')
            && $this->canAccessDocument($user, $document);
    }

    public function delete(User $user, Document $document): bool
    {
        return $user->hasPermissionTo('documents.delete')
            && $this->canAccessDocument($user, $document);
    }

    public function download(User $user, Document $document): bool
    {
        return $user->hasPermissionTo('documents.download')
            && $this->canAccessDocument($user, $document);
    }

    private function canAccessDocument(User $user, Document $document): bool
    {
        if (HotelAccess::canAccessAllHotels($user)) {
            return true;
        }

        if ($document->group_booking_id === null) {
            return true;
        }

        $document->loadMissing('groupBooking');

        return HotelAccess::allows($user, $document->groupBooking?->hotel_id);
    }
}
