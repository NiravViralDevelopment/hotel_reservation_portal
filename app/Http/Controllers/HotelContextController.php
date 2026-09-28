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

        return redirect()->route('dashboard')->with('success', 'Active hotel updated. Showing data for the selected hotel only.');
    }
}
