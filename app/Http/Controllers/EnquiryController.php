<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Enums\EnquiryStatus;
use App\Http\Requests\StoreEnquiryRequest;
use App\Models\Enquiry;
use App\Models\GroupBooking;
use App\Models\Hotel;
use App\Models\TravelAgency;
use App\Support\Audit;
use App\Support\QuerySort;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EnquiryController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Enquiry::class);

        $query = Enquiry::query()
            ->accessibleBy()
            ->with(['travelAgency', 'hotel', 'contact', 'assignedTo'])
            ->whereNotIn('status', [
                EnquiryStatus::Confirmed->value,
                EnquiryStatus::Cancelled->value,
            ])
            ->whereNull('converted_booking_id');

        QuerySort::apply($query, $request, [
            'ref' => 'ref',
            'group_name' => 'group_name',
            'enquiry_date' => 'enquiry_date',
            'status' => 'status',
            'total_revenue' => 'total_revenue',
            'nights' => 'nights',
        ], 'enquiry_date', 'desc');

        $enquiries = $query->paginate(20)->withQueryString();

        return view('enquiries.index', compact('enquiries'));
    }

    public function create(): View
    {
        $this->authorize('create', Enquiry::class);

        $travelAgencies = TravelAgency::query()->orderBy('name')->get(['id', 'name', 'code']);
        $hotels = Hotel::query()->accessibleBy()->orderBy('name')->get(['id', 'name', 'code']);
        $statuses = EnquiryStatus::values();

        return view('enquiries.create', compact('travelAgencies', 'hotels', 'statuses'));
    }

    public function store(StoreEnquiryRequest $request): RedirectResponse
    {
        $this->authorize('create', Enquiry::class);

        $data = $request->validated();
        unset($data['contact_id'], $data['assigned_to']);
        \App\Support\HotelAccess::ensure(null, $data['hotel_id'] ?? null);
        if (! empty($data['enquiry_date'])) {
            $data['day'] = Carbon::parse($data['enquiry_date'])->format('l');
        }
        if (empty($data['year']) && ! empty($data['enquiry_date'])) {
            $data['year'] = (int) Carbon::parse($data['enquiry_date'])->format('Y');
        }

        $data = array_merge($data, $this->calculateRoomTotals($data));

        $enquiry = Enquiry::query()->create($data);
        Audit::log('created', 'enquiries', $enquiry->ref, $enquiry);

        return redirect()->route('enquiries.index')->with('success', 'Enquiry created.');
    }

    public function show(Enquiry $enquiry): View
    {
        $this->authorize('view', $enquiry);

        $enquiry->load(['travelAgency', 'hotel', 'contact', 'assignedTo', 'convertedBooking']);

        return view('enquiries.show', compact('enquiry'));
    }

    public function edit(Enquiry $enquiry): View
    {
        $this->authorize('update', $enquiry);

        $travelAgencies = TravelAgency::query()->orderBy('name')->get(['id', 'name', 'code']);
        $hotels = Hotel::query()->accessibleBy()->orderBy('name')->get(['id', 'name', 'code']);
        $statuses = EnquiryStatus::values();

        return view('enquiries.edit', compact('enquiry', 'travelAgencies', 'hotels', 'statuses'));
    }

    public function update(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $this->authorize('update', $enquiry);

        $data = $request->validate([
            'ref' => ['required', 'string', 'max:255', 'unique:enquiries,ref,'.$enquiry->id],
            'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'enquiry_date' => ['nullable', 'date'],
            'group_name' => ['required', 'string', 'max:255'],
            'travel_agency_id' => ['nullable', 'integer', 'exists:travel_agencies,id'],
            'hotel_id' => ['nullable', 'integer', Rule::in(\App\Support\HotelAccess::hotelIds())],
            'nights' => ['nullable', 'integer', 'min:1'],
            'rooms_per_night' => ['nullable', 'integer', 'min:0'],
            'single_rooms' => ['nullable', 'integer', 'min:0'],
            'single_rate' => ['nullable', 'numeric', 'min:0'],
            'double_rooms' => ['nullable', 'integer', 'min:0'],
            'double_rate' => ['nullable', 'numeric', 'min:0'],
            'triple_rooms' => ['nullable', 'integer', 'min:0'],
            'triple_rate' => ['nullable', 'numeric', 'min:0'],
            'total_revenue' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'in:'.implode(',', EnquiryStatus::values())],
            'email' => ['nullable', 'email', 'max:255'],
            'remarks' => ['nullable', 'string'],
            'cxl_policy' => ['nullable', 'string', 'max:255'],
            'option_date' => ['nullable', 'date'],
            'confirm_booking' => ['nullable', 'boolean'],
            'cancel_booking' => ['nullable', 'boolean'],
            'cancellation_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $confirmBooking = $request->boolean('confirm_booking');
        $cancelBooking = $request->boolean('cancel_booking');

        if ($confirmBooking && $cancelBooking) {
            return back()
                ->withInput()
                ->withErrors(['confirm_booking' => 'Choose either Confirm booking or Cancel, not both.']);
        }

        if ($cancelBooking) {
            $request->validate([
                'cancellation_reason' => ['required', 'string', 'max:500'],
            ]);
        }

        if ($confirmBooking) {
            $data['status'] = EnquiryStatus::Confirmed->value;
        } elseif ($cancelBooking) {
            $data['status'] = EnquiryStatus::Cancelled->value;
        }

        if (! empty($data['enquiry_date'])) {
            $data['day'] = Carbon::parse($data['enquiry_date'])->format('l');
        }

        $data = array_merge($data, $this->calculateRoomTotals($data));
        $cancellationReason = $cancelBooking
            ? $request->string('cancellation_reason')->toString()
            : null;

        unset($data['confirm_booking'], $data['cancel_booking'], $data['cancellation_reason']);

        $enquiry->update($data);
        Audit::log('updated', 'enquiries', $enquiry->ref, $enquiry);
        $enquiry->refresh();

        if ($confirmBooking) {
            $booking = $this->ensureConvertedBooking($enquiry, [
                'status' => BookingStatus::Confirmed->value,
            ]);

            return redirect()
                ->route('group-bookings.show', $booking)
                ->with('success', 'Enquiry confirmed and moved to group bookings.');
        }

        if ($cancelBooking) {
            $this->ensureConvertedBooking($enquiry, [
                'status' => BookingStatus::Cancelled->value,
                'cancelled_at' => now(),
                'cxl_date' => now()->toDateString(),
                'cancellation_reason' => $cancellationReason,
                'revenue_lost' => $enquiry->total_revenue,
            ]);

            return redirect()
                ->route('cancelled-bookings.index')
                ->with('success', 'Enquiry cancelled and moved to cancelled bookings.');
        }

        return redirect()->route('enquiries.index')->with('success', 'Enquiry updated.');
    }

    public function destroy(Enquiry $enquiry): RedirectResponse
    {
        $this->authorize('delete', $enquiry);

        $ref = $enquiry->ref;
        $enquiry->delete();
        Audit::log('deleted', 'enquiries', $ref);

        return redirect()->route('enquiries.index')->with('success', 'Enquiry deleted.');
    }

    public function convert(Enquiry $enquiry): RedirectResponse
    {
        $this->authorize('convert', $enquiry);

        $booking = $this->ensureConvertedBooking($enquiry, [
            'status' => BookingStatus::Confirmed->value,
        ]);

        $enquiry->update(['status' => EnquiryStatus::Confirmed->value]);

        return redirect()
            ->route('group-bookings.show', $booking)
            ->with('success', 'Enquiry converted to group booking.');
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    private function ensureConvertedBooking(Enquiry $enquiry, array $extra = []): GroupBooking
    {
        if ($enquiry->converted_booking_id) {
            $booking = GroupBooking::query()->find($enquiry->converted_booking_id);
            if ($booking) {
                $booking->update($extra);
                Audit::log('updated', 'group_bookings', $booking->block_id, $booking);

                return $booking;
            }
        }

        $arrival = $enquiry->option_date
            ? Carbon::parse($enquiry->option_date)->startOfDay()
            : now()->addMonth()->startOfDay();
        $nights = max(1, (int) $enquiry->nights);
        $departure = $arrival->copy()->addDays($nights);

        $booking = GroupBooking::query()->create(array_merge([
            'block_id' => 'ENQ-'.$enquiry->ref,
            'enquiry_id' => $enquiry->id,
            'hotel_id' => $enquiry->hotel_id,
            'travel_agency_id' => $enquiry->travel_agency_id,
            'contact_id' => $enquiry->contact_id,
            'created_by' => auth()->id(),
            'group_name' => $enquiry->group_name,
            'email' => $enquiry->email,
            'arrival' => $arrival,
            'departure' => $departure,
            'arrival_day' => $arrival->format('l'),
            'nights' => $nights,
            'status' => BookingStatus::Provisional->value,
            'single_rns' => $enquiry->single_rooms,
            'single_rate' => $enquiry->single_rate,
            'double_rns' => $enquiry->double_rooms,
            'double_rate' => $enquiry->double_rate,
            'triple_rns' => $enquiry->triple_rooms,
            'triple_rate' => $enquiry->triple_rate,
            'revenue' => $enquiry->total_revenue,
            'cxl_policy' => $enquiry->cxl_policy,
            'rooms' => $enquiry->rooms_per_night,
        ], $extra));

        $enquiry->update(['converted_booking_id' => $booking->id]);
        Audit::log('converted', 'enquiries', $enquiry->ref.' → '.$booking->block_id, $booking);

        return $booking;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{rooms_per_night: int, total_revenue: float}
     */
    private function calculateRoomTotals(array $data): array
    {
        $singleRooms = (int) ($data['single_rooms'] ?? 0);
        $doubleRooms = (int) ($data['double_rooms'] ?? 0);
        $tripleRooms = (int) ($data['triple_rooms'] ?? 0);
        $nights = max(1, (int) ($data['nights'] ?? 1));

        $nightly = ($singleRooms * (float) ($data['single_rate'] ?? 0))
            + ($doubleRooms * (float) ($data['double_rate'] ?? 0))
            + ($tripleRooms * (float) ($data['triple_rate'] ?? 0));

        return [
            'rooms_per_night' => $singleRooms + $doubleRooms + $tripleRooms,
            'total_revenue' => round($nightly * $nights, 2),
        ];
    }
}
