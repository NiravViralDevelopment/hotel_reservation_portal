<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnquiryRequest;
use App\Models\Enquiry;
use App\Models\Hotel;
use App\Models\StatusMaster;
use App\Models\TravelAgency;
use App\Support\Audit;
use App\Support\EnquiryFieldRules;
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
            ->with(['travelAgency', 'hotel', 'assignedTo'])
            ->openPipeline();

        if ($request->filled('q')) {
            $search = $request->string('q')->trim()->toString();
            $query->where(function ($builder) use ($search) {
                $builder->where('ref', 'like', "%{$search}%")
                    ->orWhere('group_name', 'like', "%{$search}%")
                    ->orWhere('client', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('source', 'like', "%{$search}%")
                    ->orWhere('service_person', 'like', "%{$search}%");
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
            'group_name' => 'group_name',
            'enquiry_date' => 'enquiry_date',
            'check_in' => 'check_in',
            'check_out' => 'check_out',
            'days' => 'days',
            'nights' => 'nights',
            'total_price' => 'total_price',
            'grand_total' => 'grand_total',
            'status' => 'status',
        ], 'enquiry_date', 'desc');

        $enquiries = $query->paginate(10)->withQueryString();

        $hotels = Hotel::optionsForSelect();
        $travelAgencies = TravelAgency::query()->orderBy('name')->get(['id', 'name', 'code']);
        $statuses = StatusMaster::query()
            ->active()
            ->orderBy('title')
            ->pluck('title')
            ->merge(['confirmed', 'cancelled'])
            ->unique()
            ->values();

        return view('enquiries.index', compact('enquiries', 'hotels', 'travelAgencies', 'statuses'));
    }

    public function create(): View
    {
        $this->authorize('create', Enquiry::class);

        $travelAgencies = TravelAgency::query()->orderBy('name')->get(['id', 'name', 'code']);
        $hotels = Hotel::optionsForSelect();
        $statuses = StatusMaster::query()
            ->active()
            ->whereRaw("LOWER(title) NOT IN ('confirmed', 'cancelled')")
            ->orderBy('title')
            ->pluck('title');

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
        $data = array_merge($data, $this->applyCommercialTotals($data));
        $data['is_confirm'] = false;
        $data['is_cancel'] = false;
        if (empty($data['status'])) {
            $data['status'] = StatusMaster::query()
                ->active()
                ->whereRaw("LOWER(title) NOT IN ('confirmed', 'cancelled')")
                ->orderBy('title')
                ->value('title') ?? 'Chesed';
        }

        $enquiry = Enquiry::query()->create($data);
        Audit::log('created', 'enquiries', $enquiry->ref, $enquiry);

        return redirect()->route('enquiries.index')->with('success', 'Enquiry created.');
    }

    public function show(Enquiry $enquiry): View
    {
        $this->authorize('view', $enquiry);

        $enquiry->load(['travelAgency', 'hotel', 'assignedTo', 'responses.user']);

        return view('enquiries.show', compact('enquiry'));
    }

    public function edit(Enquiry $enquiry): View
    {
        $this->authorize('update', $enquiry);

        $travelAgencies = TravelAgency::query()->orderBy('name')->get(['id', 'name', 'code']);
        $hotels = Hotel::optionsForSelect($enquiry->hotel_id);
        $statuses = StatusMaster::query()
            ->active()
            ->whereRaw("LOWER(title) NOT IN ('confirmed', 'cancelled')")
            ->orderBy('title')
            ->pluck('title');

        return view('enquiries.edit', compact('enquiry', 'travelAgencies', 'hotels', 'statuses'));
    }

    public function update(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $this->authorize('update', $enquiry);

        if ($request->input('ref') === '') {
            $request->merge(['ref' => null]);
        }

        $rules = EnquiryFieldRules::base($enquiry->id, $enquiry->hotel_id);
        $rules['response_date'] = [
            'nullable',
            'date',
            Rule::when($request->filled('enquiry_date'), ['after_or_equal:enquiry_date']),
        ];
        $rules['cxl_policy'] = ['nullable', 'string', 'max:255'];
        $rules['option_date'] = ['nullable', 'date'];
        $rules['confirm_booking'] = ['nullable', 'boolean'];
        $rules['cancel_booking'] = ['nullable', 'boolean'];
        $rules['cancellation_reason'] = ['nullable', 'string', 'max:500'];

        $data = $request->validate($rules, [
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
            $data['is_confirm'] = true;
            $data['is_cancel'] = false;
            $data['cancellation_reason'] = null;
        } elseif ($cancelBooking) {
            $data['status'] = 'cancelled';
            $data['is_confirm'] = false;
            $data['is_cancel'] = true;
            $data['cancellation_reason'] = $request->string('cancellation_reason')->toString();
        }

        if (! empty($data['enquiry_date'])) {
            $data['day'] = Carbon::parse($data['enquiry_date'])->format('l');
        }

        $data = $this->normalizeEnquiryDefaults($data);
        $data = array_merge($data, $this->applyStayDates($data));
        $data = array_merge($data, $this->calculateRoomTotals($data));
        $data = array_merge($data, $this->applyTaxRevenue($data));
        $data = array_merge($data, $this->applyCommercialTotals($data));

        unset($data['confirm_booking'], $data['cancel_booking']);

        $enquiry->update($data);
        Audit::log('updated', 'enquiries', $enquiry->ref, $enquiry);

        if ($confirmBooking) {
            return redirect()
                ->route('group-bookings.index')
                ->with('success', 'Enquiry confirmed. Moved to Group Bookings.');
        }

        if ($cancelBooking) {
            return redirect()
                ->route('cancelled-bookings.index')
                ->with('success', 'Enquiry cancelled. Moved to Cancelled Bookings.');
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

        $enquiry->update([
            'status' => 'confirmed',
            'is_confirm' => true,
            'is_cancel' => false,
            'cancellation_reason' => null,
        ]);
        Audit::log('confirmed', 'enquiries', $enquiry->ref, $enquiry);

        return redirect()
            ->route('group-bookings.index')
            ->with('success', 'Enquiry confirmed. Moved to Group Bookings.');
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
        $result['days'] = max(1, $nights);

        return $result;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function applyCommercialTotals(array $data): array
    {
        $moneyKeys = [
            'adults_price', 'child_price', 'adults_extra', 'child_extra',
            'agent_price', 'our_cost', 'package_price', 'total_price', 'net_price',
            'advance', 'remaining', 'agent_comm_percent', 'agent_comm_amount',
            'payable_to_agent', 'service_total', 'total_tax', 'grand_total',
        ];

        foreach ($moneyKeys as $key) {
            if (! isset($data[$key]) || $data[$key] === '' || $data[$key] === null) {
                $data[$key] = 0;
            } else {
                $data[$key] = round((float) $data[$key], 2);
            }
        }

        if (! isset($data['total_pax']) || $data['total_pax'] === '' || $data['total_pax'] === null) {
            $data['total_pax'] = 0;
        } else {
            $data['total_pax'] = max(0, (int) $data['total_pax']);
        }

        $builtTotal = $data['adults_price'] + $data['child_price'] + $data['adults_extra'] + $data['child_extra'];
        if ($data['total_price'] <= 0 && $builtTotal > 0) {
            $data['total_price'] = round($builtTotal, 2);
        }

        if ($data['service_total'] <= 0 && $data['total_price'] > 0) {
            $data['service_total'] = $data['total_price'];
        }

        if ($data['total_tax'] <= 0 && ! empty($data['has_tax'])) {
            $data['total_tax'] = round((float) ($data['tax_revenue'] ?? 0), 2);
        }

        $data['agent_comm_amount'] = $data['agent_comm_percent'] > 0
            ? round(($data['agent_price'] > 0 ? $data['agent_price'] : $data['total_price']) * ($data['agent_comm_percent'] / 100), 2)
            : round((float) $data['agent_comm_amount'], 2);

        $data['payable_to_agent'] = $data['agent_comm_amount'];

        if ($data['net_price'] <= 0) {
            $data['net_price'] = round(max(0, $data['total_price'] - $data['agent_comm_amount']), 2);
        }

        if (abs($data['service_total'] - $data['total_price']) < 0.001) {
            $data['grand_total'] = round($data['total_price'] + $data['total_tax'], 2);
        } else {
            $data['grand_total'] = round($data['total_price'] + $data['service_total'] + $data['total_tax'], 2);
        }

        $data['remaining'] = round(max(0, $data['grand_total'] - $data['advance']), 2);

        if (empty($data['days']) && ! empty($data['nights'])) {
            $data['days'] = (int) $data['nights'];
        }

        return $data;
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
