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
            'check_in' => 'check_in',
            'check_out' => 'check_out',
            'day' => 'day',
            'nights' => 'nights',
            'block_id' => 'block_id',
            'client' => 'client',
            'email' => 'email',
            'status' => 'status',
            'total_rns' => 'total_rns',
            'total_revenue' => 'total_revenue',
            'updated_at' => 'updated_at',
        ], 'updated_at', 'desc');

        $bookings = $query->paginate(10)->withQueryString();
        $hotels = Hotel::optionsForSelect();

        return view('cancelled-bookings.index', compact('bookings', 'hotels'));
    }

    public function show(Enquiry $enquiry): View
    {
        abort_unless(auth()->user()?->can('bookings.view'), 403);
        abort_unless($enquiry->is_confirm && $enquiry->is_cancel, 404);
        HotelAccess::ensure(null, $enquiry->hotel_id);

        return view('group-bookings.show', [
            'enquiry' => $enquiry,
            'cancelledContext' => true,
        ]);
    }
}
