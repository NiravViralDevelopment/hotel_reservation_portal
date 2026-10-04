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
            'response_date' => 'response_date',
            'check_in' => 'check_in',
            'day' => 'day',
            'nights' => 'nights',
            'rooms_per_night' => 'rooms_per_night',
            'email' => 'email',
            'status' => 'status',
            'total_revenue' => 'total_revenue',
            'option_date' => 'option_date',
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

        $statuses = StatusMaster::query()
            ->active()
            ->whereRaw("LOWER(title) NOT IN ('confirmed', 'cancelled')")
            ->orderBy('title')
            ->pluck('title');

        return view('enquiries.create', compact('statuses'));
    }

    public function store(StoreEnquiryRequest $request): RedirectResponse
    {
        $this->authorize('create', Enquiry::class);

        $data = $request->validated();
        unset($data['contact_id'], $data['assigned_to']);

        $hotelId = \App\Support\HotelAccess::currentHotelId();
        if ($hotelId) {
            $data['hotel_id'] = $hotelId;
        }
        \App\Support\HotelAccess::ensure(null, $data['hotel_id'] ?? null);

        if (empty($data['year']) && ! empty($data['enquiry_date'])) {
            $data['year'] = (int) Carbon::parse($data['enquiry_date'])->format('Y');
        }

        $data = $this->normalizeEnquiryDefaults($data);
        $data = array_merge($data, $this->applyStayDates($data));
        if (! empty($data['check_in'])) {
            $data['day'] = Carbon::parse($data['check_in'])->format('l');
            $data['check_in_day'] = $data['day'];
        }
        $data['total_revenue'] = $this->revenueFromRoomNights($data);
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

        $statuses = StatusMaster::query()
            ->active()
            ->whereRaw("LOWER(title) NOT IN ('confirmed', 'cancelled')")
            ->orderBy('title')
            ->pluck('title');

        return view('enquiries.edit', compact('enquiry', 'statuses'));
    }

    public function groupBooking(Enquiry $enquiry): RedirectResponse
    {
        $this->authorize('update', $enquiry);

        return redirect()->route('group-bookings.edit', $enquiry);
    }

    public function storeGroupBooking(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $this->authorize('update', $enquiry);

        foreach ([
            'agency_ref', 'contact_name', 'saved_to_doc', 'payment_term', 'payment_status',
            'cxl_policy', 'booking_update', 'rooming', 'invoice_status', 'commission_payable_status',
            'basis', 'client', 'email', 'day',
            'contract_sent_on', 'contract_received_on', 'payment_due_date', 'cxl_due_date', 'cxl_date', 'invoice_sent_on',
            'commission', 'bb_revenue', 'dinner_revenue', 'nett_rev_ex_vat', 'invoice_amount',
            'single_rooms', 'single_rate', 'double_rooms', 'double_rate', 'triple_rooms', 'triple_rate',
        ] as $field) {
            if ($request->input($field) === '') {
                $request->merge([$field => null]);
            }
        }

        $data = $request->validate([
            'check_in' => ['required', 'date'],
            'check_out' => ['required', 'date', 'after_or_equal:check_in'],
            'day' => ['nullable', 'string', 'max:20'],
            'nights' => ['required', 'integer', 'min:0'],
            'block_id' => ['required', 'string', 'max:255', Rule::unique('enquiries', 'block_id')->ignore($enquiry->id)],
            'client' => ['nullable', 'string', 'max:255'],
            'agency_ref' => ['nullable', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'contract_sent_on' => ['nullable', 'date'],
            'contract_received_on' => ['nullable', 'date'],
            'saved_to_doc' => ['nullable', 'string', 'max:255'],
            'payment_term' => ['nullable', 'string', 'max:255'],
            'payment_due_date' => ['nullable', 'date'],
            'payment_status' => ['nullable', 'string', 'max:255'],
            'cxl_policy' => ['nullable', 'string', 'max:255'],
            'cxl_due_date' => ['nullable', 'date'],
            'cxl_date' => ['nullable', 'date'],
            'commission' => ['nullable', 'numeric', 'min:0'],
            'single_rooms' => ['nullable', 'integer', 'min:0'],
            'single_rate' => ['nullable', 'numeric', 'min:0'],
            'double_rooms' => ['nullable', 'integer', 'min:0'],
            'double_rate' => ['nullable', 'numeric', 'min:0'],
            'triple_rooms' => ['nullable', 'integer', 'min:0'],
            'triple_rate' => ['nullable', 'numeric', 'min:0'],
            'bb_revenue' => ['nullable', 'numeric', 'min:0'],
            'dinner_revenue' => ['nullable', 'numeric', 'min:0'],
            'nett_rev_ex_vat' => ['nullable', 'numeric', 'min:0'],
            'basis' => ['nullable', 'string', Rule::in(['BB', 'DBB'])],
            'booking_update' => ['nullable', 'string', 'max:255'],
            'rooming' => ['nullable', 'string', 'max:255'],
            'invoice_status' => ['nullable', 'string', 'max:255'],
            'invoice_sent_on' => ['nullable', 'date'],
            'invoice_amount' => ['nullable', 'numeric', 'min:0'],
            'commission_payable_status' => ['nullable', 'string', 'max:255'],
        ], [
            'check_in.required' => 'Date of arrival is required.',
            'check_out.required' => 'Date of departure is required.',
            'check_out.after_or_equal' => 'Date of departure cannot be before the date of arrival.',
            'nights.required' => 'No. of nights is required.',
            'block_id.required' => 'Block ID is required.',
            'block_id.unique' => 'This block ID is already used.',
            'basis.in' => 'Select BB or DBB.',
            'email.email' => 'Enter a valid email address.',
        ]);

        foreach (['single_rooms', 'double_rooms', 'triple_rooms'] as $key) {
            $data[$key] = (int) ($data[$key] ?? 0);
        }
        foreach (['single_rate', 'double_rate', 'triple_rate'] as $key) {
            $data[$key] = round((float) ($data[$key] ?? 0), 2);
        }
        $data['nights'] = max(0, (int) $data['nights']);
        $data['day'] = Carbon::parse($data['check_in'])->format('l');
        $data['check_in_day'] = $data['day'];

        $nights = $data['nights'];
        $data['total_rns'] = ($data['single_rooms'] + $data['double_rooms'] + $data['triple_rooms']) * $nights;
        $data['total_revenue'] = round((
            ($data['single_rooms'] * $data['single_rate'])
            + ($data['double_rooms'] * $data['double_rate'])
            + ($data['triple_rooms'] * $data['triple_rate'])
        ) * $nights, 2);
        $data['status'] = 'Confirmed';
        $data['is_confirm'] = true;
        $data['is_cancel'] = false;
        $data['cancellation_reason'] = null;

        $enquiry->update($data);
        Audit::log('confirmed', 'group_bookings', $enquiry->block_id ?: $enquiry->ref, $enquiry);

        return redirect()
            ->route('group-bookings.index')
            ->with('success', 'Group booking saved. Status set to Confirmed.');
    }

    public function cancel(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $this->authorize('update', $enquiry);

        $data = $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:2000'],
        ], [
            'cancellation_reason.required' => 'Enter the cancellation reason.',
        ]);

        $enquiry->update([
            'status' => 'Cancelled',
            'is_cancel' => true,
            'is_confirm' => false,
            'cancellation_reason' => $data['cancellation_reason'],
        ]);
        Audit::log('cancelled', 'enquiries', $enquiry->group_name ?: $enquiry->ref, $enquiry);

        return redirect()
            ->route('cancelled-inquiries.index')
            ->with('success', 'Enquiry cancelled.');
    }

    public function cancelBooking(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $this->authorize('update', $enquiry);
        abort_unless($enquiry->is_confirm && ! $enquiry->is_cancel, 404);

        $data = $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:2000'],
        ], [
            'cancellation_reason.required' => 'Enter the cancellation reason.',
        ]);

        $enquiry->update([
            'status' => 'Cancelled',
            'is_cancel' => true,
            'is_confirm' => true,
            'cancellation_reason' => $data['cancellation_reason'],
        ]);
        Audit::log('cancelled', 'group_bookings', $enquiry->block_id ?: $enquiry->group_name, $enquiry);

        return redirect()
            ->route('cancelled-bookings.index')
            ->with('success', 'Group booking cancelled.');
    }

    public function update(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $this->authorize('update', $enquiry);

        if ($request->input('ref') === '') {
            $request->merge(['ref' => null]);
        }

        foreach (['day', 'basis', 'cxl_policy', 'remarks', 'status', 'option_date'] as $field) {
            if ($request->input($field) === '') {
                $request->merge([$field => null]);
            }
        }

        $data = $request->validate(EnquiryFieldRules::create($enquiry->id), [
            'enquiry_date.required' => 'Enquiry date is required.',
            'response_date.required' => 'Response date is required.',
            'response_date.after_or_equal' => 'Response date cannot be before enquiry date.',
            'check_in.required' => 'Arrival date is required.',
            'nights.required' => 'Nights is required.',
            'nights.min' => 'Nights must be at least 1.',
            'group_name.required' => 'Group name is required.',
            'group_name.unique' => 'This group name is already used. Enter a different name.',
            'rooms_per_night.required' => 'Total room per night is required.',
            'email.required' => 'Email ID is required.',
            'email.email' => 'Enter a valid email address.',
            'ref.unique' => 'This reference is already used. Enter a different one.',
            'status.exists' => 'Select a valid active status.',
            'basis.in' => 'Select a valid basis.',
        ]);

        if (empty($data['year']) && ! empty($data['enquiry_date'])) {
            $data['year'] = (int) Carbon::parse($data['enquiry_date'])->format('Y');
        }

        $data = $this->normalizeEnquiryDefaults($data);
        $data = array_merge($data, $this->applyStayDates($data));
        if (! empty($data['check_in'])) {
            $data['day'] = Carbon::parse($data['check_in'])->format('l');
            $data['check_in_day'] = $data['day'];
        }
        $data['total_revenue'] = $this->revenueFromRoomNights($data);

        if (empty($data['status'])) {
            $data['status'] = $enquiry->status ?: StatusMaster::query()
                ->active()
                ->whereRaw("LOWER(title) NOT IN ('confirmed', 'cancelled')")
                ->orderBy('title')
                ->value('title') ?? 'Chesed';
        }

        $enquiry->update($data);
        Audit::log('updated', 'enquiries', $enquiry->ref, $enquiry);

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
     * (Single × Single Rate + Double × Double Rate + Triple × Triple Rate) × Nights
     *
     * @param  array<string, mixed>  $data
     */
    private function revenueFromRoomNights(array $data): float
    {
        $nights = max(0, (int) ($data['nights'] ?? 0));
        $nightly = ((int) ($data['single_rooms'] ?? 0) * (float) ($data['single_rate'] ?? 0))
            + ((int) ($data['double_rooms'] ?? 0) * (float) ($data['double_rate'] ?? 0))
            + ((int) ($data['triple_rooms'] ?? 0) * (float) ($data['triple_rate'] ?? 0));

        return round($nightly * $nights, 2);
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
