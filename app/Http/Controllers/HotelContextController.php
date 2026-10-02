<?php

namespace App\Http\Controllers;

use App\Support\HotelAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HotelContextController extends Controller
{
    public function switch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hotel_id' => ['required', 'integer', Rule::in(HotelAccess::assignedHotelIds())],
        ]);

        HotelAccess::setCurrentHotelId((int) $validated['hotel_id']);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Working in '.HotelAccess::currentHotel()?->name.'.');
    }

    public function clear(): RedirectResponse
    {
        HotelAccess::clearCurrentHotel();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Select a hotel to continue.');
    }
}
