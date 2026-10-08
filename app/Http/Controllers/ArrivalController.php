<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\Hotel;
use App\Support\EnquiryIndexFilters;
use App\Support\QuerySort;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArrivalController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(auth()->user()?->can('bookings.view'), 403);

        [$dateFrom, $dateTo] = EnquiryIndexFilters::dateBounds($request);

        $query = Enquiry::query()
            ->accessibleBy()
            ->with(['hotel', 'travelAgency'])
            ->groupBookings()
            ->where(function ($query) {
                $query->whereNull('check_out')
                    ->orWhereDate('check_out', '>', now()->toDateString());
            });

        EnquiryIndexFilters::apply($query, $request);
        EnquiryIndexFilters::applyDateRange($query, $request, 'check_in');

        QuerySort::apply($query, $request, [
            'ref' => 'ref',
            'group_name' => 'group_name',
            'enquiry_date' => 'enquiry_date',
            'response_date' => 'response_date',
            'check_in' => 'check_in',
            'check_out' => 'check_out',
            'day' => 'day',
            'nights' => 'nights',
            'rooms_per_night' => 'rooms_per_night',
            'email' => 'email',
            'status' => 'status',
            'total_revenue' => 'total_revenue',
            'option_date' => 'option_date',
            'cxl_due_date' => 'cxl_due_date',
        ], 'check_in');

        $bookings = $query->get();
        $hotels = Hotel::optionsForSelect();

        return view('arrivals.index', compact('bookings', 'hotels', 'dateFrom', 'dateTo'));
    }

    public function show(Enquiry $enquiry): View
    {
        abort_unless(auth()->user()?->can('bookings.view'), 403);
        abort_unless($this->isVisibleArrival($enquiry), 404);

        $enquiry->load(['hotel', 'responses.user']);

        return view('stay-lists.show', [
            'enquiry' => $enquiry,
            'list' => 'arrivals',
        ]);
    }

    private function isVisibleArrival(Enquiry $enquiry): bool
    {
        return Enquiry::query()
            ->accessibleBy()
            ->groupBookings()
            ->whereKey($enquiry->getKey())
            ->where(function ($query) {
                $query->whereNull('check_out')
                    ->orWhereDate('check_out', '>', now()->toDateString());
            })
            ->exists();
    }
}
