<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\Hotel;
use App\Support\HotelAccess;
use App\Support\QuerySort;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArrivalController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(auth()->user()?->can('bookings.view'), 403);

        $date = $request->date('date')?->toDateString() ?? now()->toDateString();

        $query = Enquiry::query()
            ->accessibleBy()
            ->with(['hotel', 'travelAgency'])
            ->groupBookings()
            ->whereDate('check_in', $date);

        if ($request->filled('hotel_id')) {
            HotelAccess::ensure(null, $request->integer('hotel_id'));
            $query->where('hotel_id', $request->integer('hotel_id'));
        }

        QuerySort::apply($query, $request, [
            'ref' => 'ref',
            'group_name' => 'group_name',
            'nights' => 'nights',
            'status' => 'status',
        ], 'group_name');

        $bookings = $query->get();
        $hotels = Hotel::optionsForSelect();

        return view('arrivals.index', compact('bookings', 'hotels', 'date'));
    }
}
