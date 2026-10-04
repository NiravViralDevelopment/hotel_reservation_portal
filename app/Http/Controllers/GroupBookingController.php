<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\Hotel;
use App\Models\TravelAgency;
use App\Support\Audit;
use App\Support\GroupBookingExcelImporter;
use App\Support\HotelAccess;
use App\Support\QuerySort;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroupBookingController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(auth()->user()?->can('bookings.view'), 403);

        $query = Enquiry::query()
            ->accessibleBy()
            ->with(['travelAgency', 'hotel', 'assignedTo'])
            ->groupBookings();

        if ($request->filled('q')) {
            $search = $request->string('q')->trim()->toString();
            $query->where(function ($builder) use ($search) {
                $builder->where('ref', 'like', "%{$search}%")
                    ->orWhere('group_name', 'like', "%{$search}%")
                    ->orWhere('client', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        if ($request->filled('hotel_id')) {
            HotelAccess::ensure(null, $request->integer('hotel_id'));
            $query->where('hotel_id', $request->integer('hotel_id'));
        }

        if ($request->filled('travel_agency_id')) {
            $query->where('travel_agency_id', $request->integer('travel_agency_id'));
        }

        if ($request->filled('arrival_from')) {
            $query->whereDate('check_in', '>=', $request->string('arrival_from'));
        }

        if ($request->filled('arrival_to')) {
            $query->whereDate('check_in', '<=', $request->string('arrival_to'));
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
        ], 'check_in', 'asc');

        $bookings = $query->paginate(10)->withQueryString();
        $hotels = Hotel::optionsForSelect();
        $travelAgencies = TravelAgency::query()->orderBy('name')->get(['id', 'name', 'code']);

        return view('group-bookings.index', compact('bookings', 'hotels', 'travelAgencies'));
    }

    public function show(Enquiry $enquiry): RedirectResponse
    {
        abort_unless(auth()->user()?->can('bookings.view'), 403);
        abort_unless($enquiry->is_confirm && ! $enquiry->is_cancel, 404);
        HotelAccess::ensure(null, $enquiry->hotel_id);

        return redirect()->route('enquiries.show', $enquiry);
    }

    public function import(Request $request, GroupBookingExcelImporter $importer): RedirectResponse
    {
        abort_unless(auth()->user()?->can('bookings.view') || auth()->user()?->can('bookings.create'), 403);

        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,application/zip,application/octet-stream,text/csv,text/plain', 'max:10240'],
            'hotel_id' => ['nullable', 'integer'],
        ]);

        $hotelId = isset($validated['hotel_id']) ? (int) $validated['hotel_id'] : null;
        if ($hotelId) {
            HotelAccess::ensure(null, $hotelId);
        } else {
            $hotelId = HotelAccess::currentHotel()?->id;
        }

        $path = $request->file('file')->getRealPath();
        $result = $importer->import($path, $hotelId, auth()->id());

        Audit::log(
            'imported',
            'group_bookings',
            sprintf('imported=%d updated=%d skipped=%d', $result['imported'], $result['updated'], $result['skipped'])
        );

        return redirect()
            ->route('group-bookings.index')
            ->with(
                'success',
                "Import complete: {$result['imported']} created, {$result['updated']} updated, {$result['skipped']} skipped. All rows set is_confirm = 1."
            );
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('enquiries.create');
    }

    public function store(): RedirectResponse
    {
        return redirect()->route('enquiries.create');
    }

    public function edit(Enquiry $enquiry): RedirectResponse
    {
        return redirect()->route('enquiries.edit', $enquiry);
    }

    public function update(Enquiry $enquiry): RedirectResponse
    {
        return redirect()->route('enquiries.edit', $enquiry);
    }

    public function destroy(Enquiry $enquiry): RedirectResponse
    {
        return redirect()->route('enquiries.show', $enquiry);
    }
}
