<?php

namespace App\Http\Controllers;

use App\Models\GroupBooking;
use App\Models\Hotel;
use App\Support\HotelAccess;
use App\Support\QuerySort;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartureController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', GroupBooking::class);

        $date = $request->date('date')?->toDateString() ?? now()->toDateString();

        $query = GroupBooking::query()
            ->accessibleBy()
            ->with(['hotel', 'travelAgency', 'contact'])
            ->active()
            ->departingOn($date);

        if ($request->filled('hotel_id')) {
            HotelAccess::ensure(null, $request->integer('hotel_id'));
            $query->where('hotel_id', $request->integer('hotel_id'));
        }

        QuerySort::apply($query, $request, [
            'block_id' => 'block_id',
            'group_name' => 'group_name',
            'nights' => 'nights',
            'status' => 'status',
        ], 'group_name');

        $bookings = $query->get();
        $hotels = Hotel::optionsForSelect();

        return view('departures.index', compact('bookings', 'hotels', 'date'));
    }
}
