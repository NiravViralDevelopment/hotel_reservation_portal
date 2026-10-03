<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Http\Requests\StoreEnquiryRequest;
use App\Models\Enquiry;
use App\Models\GroupBooking;
use App\Models\Hotel;
use App\Models\StatusMaster;
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
            ->whereNotIn('status', ['confirmed', 'cancelled'])
            ->whereNull('converted_booking_id');

        if ($request->filled('q')) {
            $search = $request->string('q')->trim()->toString();
            $query->where(function ($builder) use ($search) {
                $builder->where('ref', 'like', "%{$search}%")
                    ->orWhere('group_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('hotel_id')) {
            \App\Support\HotelAccess::ensure(null, $request->integer('hotel_id'));
            $query->where('hotel_id', $request->integer('hotel_id'));
        }

        if ($request->filled('travel_agency_id')) {
            $query->where('travel_agency_id', $request->integer('travel_agency_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('enquiry_date_from')) {
            $query->whereDate('enquiry_date', '>=', $request->string('enquiry_date_from'));
        }

        if ($request->filled('enquiry_date_to')) {
            $query->whereDate('enquiry_date', '<=', $request->string('enquiry_date_to'));
        }

        if ($request->string('response') === 'awaiting') {
            $query->whereNull('response_date');
        } elseif ($request->string('response') === 'received') {
            $query->whereNotNull('response_date');
        }

        QuerySort::apply($query, $request, [
            'ref' => 'ref',
            'year' => 'year',
            'group_name' => 'group_name',
            'enquiry_date' => 'enquiry_date',
            'response_date' => 'response_date',
            'status' => 'status',
            'total_revenue' => 'total_revenue',
            'nights' => 'nights',
        ], 'enquiry_date', 'desc');

        $enquiries = $query->paginate(10)->withQueryString();

        $hotels = Hotel::optionsForSelect();
        $travelAgencies = TravelAgency::query()->orderBy('name')->get(['id', 'name', 'code']);
        $statuses = StatusMaster::query()
            ->active()
            ->whereNotIn('title', ['confirmed', 'cancelled'])
            ->orderBy('title')
            ->pluck('title');

        return view('enquiries.index', compact('enquiries', 'hotels', 'travelAgencies', 'statuses'));
    }

    public function create(): View
    {
        $this->authorize('create', Enquiry::class);

        $travelAgencies = TravelAgency::query()->orderBy('name')->get(['id', 'name', 'code']);
        $hotels = Hotel::optionsForSelect();
        $statuses = StatusMaster::query()->active()->orderBy('title')->pluck('title');

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

        $data = $this->normalizeEnquiryDefaults($data);
        $data = array_merge($data, $this->applyStayDates($data));
        $data = array_merge($data, $this->calculateRoomTotals($data));
        $data = array_merge($data, $this->applyTaxRevenue($data));

        $enquiry = Enquiry::query()->create($data);
        Audit::log('created', 'enquiries', $enquiry->ref, $enquiry);

        return redirect()->route('enquiries.index')->with('success', 'Enquiry created.');
    }

    public function show(Enquiry $enquiry): View
    {
        $this->authorize('view', $enquiry);

        $enquiry->load(['travelAgency', 'hotel', 'contact', 'assignedTo', 'convertedBooking', 'responses.user']);

        return view('enquiries.show', compact('enquiry'));
    }

    public function edit(Enquiry $enquiry): View
    {
        $this->authorize('update', $enquiry);

        $travelAgencies = TravelAgency::query()->orderBy('name')->get(['id', 'name', 'code']);
        $hotels = Hotel::optionsForSelect($enquiry->hotel_id);
        $statuses = StatusMaster::query()->active()->orderBy('title')->pluck('title');

        return view('enquiries.edit', compact('enquiry', 'travelAgencies', 'hotels', 'statuses'));
    }

    public function update(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $this->authorize('update', $enquiry);

        if ($request->input('ref') === '') {
            $request->merge(['ref' => null]);
        }

        $data = $request->validate([
            'ref' => ['nullable', 'string', 'max:255', 'unique:enquiries,ref,'.$enquiry->id],
            'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'enquiry_date' => ['nullable', 'date'],
            'response_date' => [
                'nullable',
                'date',
                Rule::when($request->filled('enquiry_date'), ['after_or_equal:enquiry_date']),
            ],
            'check_in' => ['nullable', 'date'],
            'check_in_day' => ['nullable', 'string', 'max:20'],
            'check_out' => ['nullable', 'date', 'after:check_in'],
            'group_name' => ['required', 'string', 'max:255', Rule::unique('enquiries', 'group_name')->ignore($enquiry->id)],
            'travel_agency_id' => ['nullable', 'integer', 'exists:travel_agencies,id'],
            'hotel_id' => ['nullable', 'integer', Rule::in(\App\Support\HotelAccess::selectableHotelIds(null, $enquiry->hotel_id))],
            'nights' => ['nullable', 'integer', 'min:1'],
            'rooms_per_night' => ['nullable', 'integer', 'min:0'],
            'single_rooms' => ['nullable', 'integer', 'min:0'],
            'single_rate' => ['nullable', 'numeric', 'min:0'],
            'double_rooms' => ['nullable', 'integer', 'min:0'],
            'double_rate' => ['nullable', 'numeric', 'min:0'],
            'triple_rooms' => ['nullable', 'integer', 'min:0'],
            'triple_rate' => ['nullable', 'numeric', 'min:0'],
            'total_revenue' => ['nullable', 'numeric', 'min:0'],
            'has_tax' => ['sometimes', 'boolean'],
            'tax_percentage' => ['nullable', 'numeric', 'min:0', 'max:100', 'required_if:has_tax,1,true'],
            'tax_revenue' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'max:255', Rule::exists('status_masters', 'title')],
            'email' => ['nullable', 'email', 'max:255'],
            'remarks' => ['nullable', 'string'],
            'cxl_policy' => ['nullable', 'string', 'max:255'],
            'option_date' => ['nullable', 'date'],
            'confirm_booking' => ['nullable', 'boolean'],
            'cancel_booking' => ['nullable', 'boolean'],
            'cancellation_reason' => ['nullable', 'string', 'max:500'],
        ], [
            'group_name.unique' => 'This group name is already used. Enter a different name.',
            'check_out.after' => 'Check-out must be after check-in.',
            'response_date.after_or_equal' => 'Response date cannot be before enquiry date.',
            'tax_percentage.required_if' => 'Enter the tax percentage.',
        ]);

        $data['has_tax'] = $request->boolean('has_tax');

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
            $data['status'] = 'confirmed';
        } elseif ($cancelBooking) {
            $data['status'] = 'cancelled';
        }

        if (! empty($data['enquiry_date'])) {
            $data['day'] = Carbon::parse($data['enquiry_date'])->format('l');
        }

        $data = $this->normalizeEnquiryDefaults($data);
        $data = array_merge($data, $this->applyStayDates($data));
        $data = array_merge($data, $this->calculateRoomTotals($data));
        $data = array_merge($data, $this->applyTaxRevenue($data));
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

    public function storeResponse(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $this->authorize('update', $enquiry);

        $data = $request->validate([
            'response_date' => [
                'required',
                'date',
                Rule::when(
                    $enquiry->enquiry_date,
                    ['after_or_equal:'.$enquiry->enquiry_date->format('Y-m-d')]
                ),
            ],
            'client_response' => ['required', 'string', 'max:2000'],
        ], [
            'response_date.required' => 'Enter the date the client responded.',
            'response_date.after_or_equal' => 'Response date cannot be before enquiry date.',
            'client_response.required' => 'Enter the remark for this enquiry.',
        ]);

        $enquiry->responses()->create([
            'user_id' => $request->user()?->id,
            'response_date' => $data['response_date'],
            'client_response' => $data['client_response'],
        ]);
        $enquiry->update($data);
        Audit::log('updated', 'enquiries', $enquiry->ref.' remark', $enquiry);

        return redirect()
            ->route('enquiries.show', $enquiry)
            ->with('success', 'Remark saved for '.$enquiry->group_name.'.');
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

        $enquiry->update(['status' => 'confirmed']);

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

        $arrival = $enquiry->check_in
            ? Carbon::parse($enquiry->check_in)->startOfDay()
            : ($enquiry->option_date
                ? Carbon::parse($enquiry->option_date)->startOfDay()
                : now()->addMonth()->startOfDay());
        $nights = max(1, (int) $enquiry->nights);
        $departure = $enquiry->check_out
            ? Carbon::parse($enquiry->check_out)->startOfDay()
            : $arrival->copy()->addDays($nights);
        if ($enquiry->check_in && $enquiry->check_out) {
            $nights = max(1, $arrival->diffInDays($departure));
        }

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
     * @return array{nights?: int}
     */
    private function applyStayDates(array $data): array
    {
        $result = [];

        if (! empty($data['check_in'])) {
            $checkIn = Carbon::parse($data['check_in'])->startOfDay();
            $result['check_in_day'] = $checkIn->format('l');
        } else {
            $result['check_in_day'] = null;
        }

        if (empty($data['check_in']) || empty($data['check_out'])) {
            return $result;
        }

        $checkIn = Carbon::parse($data['check_in'])->startOfDay();
        $checkOut = Carbon::parse($data['check_out'])->startOfDay();
        $nights = $checkIn->diffInDays($checkOut);

        $result['nights'] = max(1, $nights);

        return $result;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizeEnquiryDefaults(array $data): array
    {
        foreach (['single_rooms', 'double_rooms', 'triple_rooms', 'rooms_per_night'] as $key) {
            if (! isset($data[$key]) || $data[$key] === '' || $data[$key] === null) {
                $data[$key] = 0;
            } else {
                $data[$key] = (int) $data[$key];
            }
        }

        foreach (['single_rate', 'double_rate', 'triple_rate'] as $key) {
            if (! isset($data[$key]) || $data[$key] === '' || $data[$key] === null) {
                $data[$key] = 0;
            } else {
                $data[$key] = round((float) $data[$key], 2);
            }
        }

        if (! isset($data['nights']) || $data['nights'] === '' || $data['nights'] === null) {
            $data['nights'] = 1;
        } else {
            $data['nights'] = max(1, (int) $data['nights']);
        }

        if (! isset($data['total_revenue']) || $data['total_revenue'] === '' || $data['total_revenue'] === null) {
            $data['total_revenue'] = 0;
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{has_tax: bool, tax_percentage: float|null, tax_revenue: float}
     */
    private function applyTaxRevenue(array $data): array
    {
        $hasTax = ! empty($data['has_tax']);
        $percentage = $hasTax ? (float) ($data['tax_percentage'] ?? 0) : null;
        $total = (float) ($data['total_revenue'] ?? 0);

        return [
            'has_tax' => $hasTax,
            'tax_percentage' => $hasTax ? $percentage : null,
            'tax_revenue' => ($hasTax && $percentage > 0)
                ? round($total * ($percentage / 100), 2)
                : 0.0,
        ];
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
        $fromBreakdown = $singleRooms + $doubleRooms + $tripleRooms;

        $nightly = ($singleRooms * (float) ($data['single_rate'] ?? 0))
            + ($doubleRooms * (float) ($data['double_rate'] ?? 0))
            + ($tripleRooms * (float) ($data['triple_rate'] ?? 0));

        $enteredRooms = isset($data['rooms_per_night']) && $data['rooms_per_night'] !== ''
            ? (int) $data['rooms_per_night']
            : null;

        return [
            'rooms_per_night' => $enteredRooms ?? $fromBreakdown,
            'total_revenue' => $nightly > 0
                ? round($nightly * $nights, 2)
                : round((float) ($data['total_revenue'] ?? 0), 2),
        ];
    }
}
