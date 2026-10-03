<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\Hotel;
use App\Support\HotelAccess;
use App\Support\QuerySort;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CancelledBookingController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(auth()->user()?->can('bookings.view'), 403);

        $query = Enquiry::query()
            ->accessibleBy()
            ->with(['hotel', 'travelAgency'])
            ->cancelledBookings();

        if ($request->filled('hotel_id')) {
            HotelAccess::ensure(null, $request->integer('hotel_id'));
            $query->where('hotel_id', $request->integer('hotel_id'));
        }

        QuerySort::apply($query, $request, [
            'ref' => 'ref',
            'group_name' => 'group_name',
            'check_in' => 'check_in',
            'check_out' => 'check_out',
            'days' => 'days',
            'nights' => 'nights',
            'total_price' => 'total_price',
            'grand_total' => 'grand_total',
            'status' => 'status',
        ], 'updated_at', 'desc');

        $bookings = $query->paginate(10)->withQueryString();
        $hotels = Hotel::optionsForSelect();

        return view('cancelled-bookings.index', compact('bookings', 'hotels'));
    }
}
