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
use App\Support\SimpleXlsxWriter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EnquiryController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Enquiry::class);

        $enquiryMonth = $this->enquiryMonthValue($request);
        $enquiries = $this->filteredQuery($request)->paginate(10)->withQueryString();

        $reminderBase = Enquiry::query()->accessibleBy()->openPipeline();
        $today = now()->toDateString();
        $in7 = now()->addDays(7)->toDateString();

        $reminders = [
            'arrivals' => (clone $reminderBase)
                ->whereNotNull('check_in')
                ->whereDate('check_in', '>=', $today)
                ->whereDate('check_in', '<=', $in7)
                ->count(),
            'options' => (clone $reminderBase)
                ->whereNotNull('option_date')
                ->whereDate('option_date', '<=', $in7)
                ->count(),
            'cxl' => (clone $reminderBase)
                ->whereNotNull('cxl_due_date')
                ->whereDate('cxl_due_date', '<=', $in7)
                ->count(),
            'options_overdue' => (clone $reminderBase)
                ->whereNotNull('option_date')
                ->whereDate('option_date', '<', $today)
                ->count(),
            'cxl_overdue' => (clone $reminderBase)
                ->whereNotNull('cxl_due_date')
                ->whereDate('cxl_due_date', '<', $today)
                ->count(),
        ];

        $hotels = Hotel::optionsForSelect();
        $travelAgencies = TravelAgency::query()->orderBy('name')->get(['id', 'name', 'code']);
        $statuses = StatusMaster::query()
            ->active()
            ->orderBy('title')
            ->pluck('title')
            ->merge(['confirmed', 'cancelled'])
            ->unique()
            ->values();

        return view('enquiries.index', compact(
            'enquiries',
            'hotels',
            'travelAgencies',
            'statuses',
            'reminders',
            'enquiryMonth'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Enquiry::class);

        $rows = $this->filteredQuery($request)->get()->map(function (Enquiry $enquiry) {
            return $this->enquiryExportRow($enquiry);
        })->all();

        Audit::log('exported', 'enquiries', 'rows='.count($rows));

        return SimpleXlsxWriter::download(
            'enquiries-'.now()->format('Y-m-d').'.xlsx',
            $this->enquiryExportHeaders(),
            $rows
        );
    }

    public function create(): View
    {
        $this->authorize('create', Enquiry::class);

        $statuses = StatusMaster::query()
            ->active()
            ->whereRaw("LOWER(title) NOT IN ('confirmed', 'cancelled')")
            ->orderBy('title')
            ->pluck('title');

        $existingPairs = $this->existingGroupRefPairs();

        return view('enquiries.create', compact('statuses', 'existingPairs'));
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
        $data = $this->applyCxlDueDate($data);
        $data = $this->applyRoomPeriods($data);
        $data = $this->applyDailyRooms($data);
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

        $excluded = ['confirmed', 'cancelled'];
        if ($enquiry->is_cancel && ! $enquiry->is_confirm) {
            $excluded[] = 'chesed';
        }

        $statuses = StatusMaster::query()
            ->active()
            ->whereRaw('LOWER(title) NOT IN ('.implode(',', array_fill(0, count($excluded), '?')).')', $excluded)
            ->orderBy('title')
            ->pluck('title');

        $existingPairs = $this->existingGroupRefPairs($enquiry->id);

        return view('enquiries.edit', compact('enquiry', 'statuses', 'existingPairs'));
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
            'agency_ref', 'contact_name', 'saved_to_doc', 'payment_term', 'payment_term_days', 'payment_status',
            'cxl_policy', 'booking_update', 'rooming', 'invoice_status', 'commission_payable_status',
            'basis', 'client', 'email', 'day',
            'contract_sent_on', 'contract_received_on', 'payment_due_date', 'cxl_due_date', 'cxl_date', 'invoice_sent_on',
            'bb_revenue', 'dinner_revenue', 'nett_rev_ex_vat', 'invoice_amount',
            'single_rooms', 'single_rate', 'double_rooms', 'double_rate', 'triple_rooms', 'triple_rate',
        ] as $field) {
            if ($request->input($field) === '') {
                $request->merge([$field => null]);
            }
        }

        $data = $request->validate([
            'check_in' => ['required', 'date', 'after_or_equal:today'],
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
            'payment_term' => ['nullable', 'string', Rule::in(['Pre Arrival', 'Post Departure'])],
            'payment_term_days' => ['nullable', 'integer', 'min:0', 'max:999', 'required_with:payment_term'],
            'payment_due_date' => ['nullable', 'date'],
            'payment_status' => ['nullable', 'string', 'max:255'],
            'cxl_policy' => ['nullable', 'string', 'max:255'],
            'cxl_due_date' => ['nullable', 'date'],
            'cxl_date' => ['nullable', 'date'],
            'has_commission' => ['nullable', 'in:0,1'],
            'single_rooms' => ['nullable', 'integer', 'min:0'],
            'single_rate' => ['nullable', 'numeric', 'min:0'],
            'double_rooms' => ['nullable', 'integer', 'min:0'],
            'double_rate' => ['nullable', 'numeric', 'min:0'],
            'triple_rooms' => ['nullable', 'integer', 'min:0'],
            'triple_rate' => ['nullable', 'numeric', 'min:0'],
            'daily_rooms' => ['nullable', 'array', 'max:400'],
            'daily_rooms.*.single_rooms' => ['nullable', 'numeric', 'min:0'],
            'daily_rooms.*.single_rate' => ['nullable', 'numeric', 'min:0'],
            'daily_rooms.*.double_rooms' => ['nullable', 'numeric', 'min:0'],
            'daily_rooms.*.double_rate' => ['nullable', 'numeric', 'min:0'],
            'daily_rooms.*.triple_rooms' => ['nullable', 'numeric', 'min:0'],
            'daily_rooms.*.triple_rate' => ['nullable', 'numeric', 'min:0'],
            'bb_revenue' => ['nullable', 'numeric'],
            'dinner_revenue' => ['nullable', 'numeric'],
            'nett_rev_ex_vat' => ['nullable', 'numeric'],
            'basis' => ['nullable', 'string', Rule::in(['BB', 'DBB'])],
            'booking_update' => ['nullable', 'string', 'max:255'],
            'rooming' => ['nullable', 'string', 'max:255'],
            'invoice_status' => ['nullable', 'string', 'max:255'],
            'invoice_sent_on' => ['nullable', 'date'],
            'invoice_amount' => ['nullable', 'numeric', 'min:0'],
            'commission_payable_status' => ['nullable', 'string', Rule::in(['Pending', 'Received']), 'required_if:has_commission,1'],
        ], [
            'check_in.required' => 'Date of arrival is required.',
            'check_in.after_or_equal' => 'Date of arrival cannot be before today.',
            'check_out.required' => 'Date of departure is required.',
            'check_out.after_or_equal' => 'Date of departure cannot be before the date of arrival.',
            'nights.required' => 'No. of nights is required.',
            'block_id.required' => 'Block ID is required.',
            'block_id.unique' => 'This block ID is already used.',
            'basis.in' => 'Select BB or DBB.',
            'email.email' => 'Enter a valid email address.',
            'payment_term.in' => 'Select Pre Arrival or Post Departure.',
            'payment_term_days.required_with' => 'Enter the number of days.',
            'payment_term_days.integer' => 'Number of days must be a whole number.',
            'payment_term_days.min' => 'Number of days cannot be negative.',
            'has_commission.in' => 'Select Yes or No for commission.',
            'commission_payable_status.required_if' => 'Select the commission payable status.',
            'commission_payable_status.in' => 'Select Pending or Received.',
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
        $data['payment_due_date'] = $this->paymentDueDate(
            $data['payment_term'] ?? null,
            isset($data['payment_term_days']) ? (int) $data['payment_term_days'] : null,
            $data['check_in'],
            $data['check_out']
        );
        if ($data['payment_due_date'] === null) {
            $data['payment_term'] = null;
            $data['payment_term_days'] = null;
        }

        if (array_key_exists('has_commission', $data) && $data['has_commission'] !== null && $data['has_commission'] !== '') {
            $data['has_commission'] = (string) $data['has_commission'] === '1';
        } else {
            $data['has_commission'] = null;
        }
        if (! $data['has_commission']) {
            $data['commission_payable_status'] = null;
        }

        $nights = $data['nights'];
        $dailyRows = $this->cleanDailyRooms($data['daily_rooms'] ?? null, $data['check_in'], $data['check_out']);
        unset($data['daily_rooms']);
        if ($dailyRows === [] && is_array($enquiry->daily_room_rates) && $enquiry->daily_room_rates !== []) {
            $dailyRows = $enquiry->daily_room_rates;
        }

        if ($dailyRows !== []) {
            $totals = $this->totalsFromDailyRows($dailyRows);
            foreach (['single', 'double', 'triple'] as $type) {
                $data[$type.'_rooms'] = $totals[$type.'_rooms'];
                $data[$type.'_rate'] = $totals[$type.'_rate'];
            }
            $data['total_rns'] = $totals['total_rns'] * $nights;
            $data['total_revenue'] = round((
                ($data['single_rooms'] * $data['single_rate'])
                + ($data['double_rooms'] * $data['double_rate'])
                + ($data['triple_rooms'] * $data['triple_rate'])
            ) * $nights, 2);
            $data['bb_revenue'] = $totals['bb_revenue'];
            $data['daily_room_rates'] = $dailyRows;
        } else {
            $data['total_rns'] = ($data['single_rooms'] + $data['double_rooms'] + $data['triple_rooms']) * $nights;
            $data['total_revenue'] = round((
                ($data['single_rooms'] * $data['single_rate'])
                + ($data['double_rooms'] * $data['double_rate'])
                + ($data['triple_rooms'] * $data['triple_rate'])
            ) * $nights, 2);
            $data['bb_revenue'] = round((
                ($data['single_rooms'] * 10)
                + ($data['double_rooms'] * 20)
                + ($data['triple_rooms'] * 30)
            ) * $nights, 2);
        }
        $data['dinner_revenue'] = 0;
        $data['nett_rev_ex_vat'] = round((($data['total_revenue'] * 100) / 120) - $data['bb_revenue'], 2);
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

        if ($request->input('cxl_date') === '') {
            $request->merge(['cxl_date' => null]);
        }

        $data = $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:2000'],
            'cxl_date' => ['nullable', 'date'],
        ], [
            'cancellation_reason.required' => 'Enter the cancellation reason.',
            'cxl_date.date' => 'Enter a valid CXL date.',
        ]);

        $enquiry->update([
            'status' => 'Cancelled',
            'is_cancel' => true,
            'is_confirm' => false,
            'cancellation_reason' => $data['cancellation_reason'],
            'cxl_date' => $data['cxl_date'] ?? null,
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

        $request->merge([
            'group_name' => trim((string) $request->input('group_name', '')),
            'ref' => trim((string) $request->input('ref', '')),
        ]);

        if ($request->input('ref') === '') {
            $request->merge(['ref' => null]);
        }

        foreach (['day', 'basis', 'cxl_policy', 'remarks', 'status', 'option_date', 'single_from_date', 'single_to_date', 'double_from_date', 'double_to_date', 'triple_from_date', 'triple_to_date'] as $field) {
            if ($request->input($field) === '') {
                $request->merge([$field => null]);
            }
        }

        $data = $request->validate(EnquiryFieldRules::create($enquiry->id, $request->all()), [
            'enquiry_date.required' => 'Enquiry date is required.',
            'response_date.required' => 'Response date is required.',
            'response_date.after_or_equal' => 'Response date cannot be before enquiry date.',
            'check_in.required' => 'Arrival date is required.',
            'check_in.after_or_equal' => 'Arrival date cannot be before today.',
            'check_out.required' => 'Departure date is required.',
            'check_out.after' => 'Departure date must be after the arrival date.',
            'nights.required' => 'Nights is required.',
            'nights.min' => 'Nights must be at least 1.',
            'group_name.required' => 'Group name is required.',
            'group_name.unique' => 'This group name and ref no combination already exists.',
            'rooms_per_night.required' => 'Total room per night is required.',
            'email.required' => 'Email ID is required.',
            'email.email' => 'Enter a valid email address.',
            'ref.required' => 'Ref no is required.',
            'ref.unique' => 'This group name and ref no combination already exists.',
            'status.exists' => 'Select a valid active status.',
            'basis.in' => 'Select a valid basis.',
        ] + EnquiryFieldRules::roomPeriodMessages());

        if (empty($data['year']) && ! empty($data['enquiry_date'])) {
            $data['year'] = (int) Carbon::parse($data['enquiry_date'])->format('Y');
        }

        $data = $this->normalizeEnquiryDefaults($data);
        $data = array_merge($data, $this->applyStayDates($data));
        if (! empty($data['check_in'])) {
            $data['day'] = Carbon::parse($data['check_in'])->format('l');
            $data['check_in_day'] = $data['day'];
        }
        $data = $this->applyCxlDueDate($data);
        $data = $this->applyRoomPeriods($data);
        $data = $this->applyDailyRooms($data);
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
        $nights = (int) $checkIn->diff($checkOut)->days;

        $result['nights'] = max(1, $nights);
        $result['days'] = max(1, $nights);

        return $result;
    }

    /**
     * CXL due date is the arrival date minus the number of days written in the CXL policy.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function applyCxlDueDate(array $data): array
    {
        $policy = trim((string) ($data['cxl_policy'] ?? ''));
        if ($policy === '' || empty($data['check_in']) || ! preg_match('/\d+/', $policy, $matches)) {
            $data['cxl_due_date'] = null;

            return $data;
        }

        $data['cxl_due_date'] = Carbon::parse($data['check_in'])
            ->startOfDay()
            ->subDays((int) $matches[0])
            ->toDateString();

        return $data;
    }

    /**
     * Keep each room-type range inside the stay, and stop a later type using dates already taken by the previous type.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function applyRoomPeriods(array $data): array
    {
        if (empty($data['check_in']) || empty($data['check_out'])) {
            return $data;
        }

        $arrival = Carbon::parse($data['check_in'])->startOfDay();
        $departure = Carbon::parse($data['check_out'])->startOfDay();

        [$singleFrom, $singleTo] = $this->clampRoomPeriod(
            $data['single_from_date'] ?? null,
            $data['single_to_date'] ?? null,
            $arrival,
            $departure
        );

        $doubleStart = $singleTo ? $singleTo->copy()->addDay() : $arrival->copy();
        if ($doubleStart->gt($departure)) {
            $doubleFrom = null;
            $doubleTo = null;
        } else {
            [$doubleFrom, $doubleTo] = $this->clampRoomPeriod(
                $data['double_from_date'] ?? null,
                $data['double_to_date'] ?? null,
                $doubleStart,
                $departure
            );
        }

        $tripleStart = $doubleTo ? $doubleTo->copy()->addDay() : $doubleStart->copy();
        if ($tripleStart->gt($departure) || $doubleTo === null) {
            $tripleFrom = null;
            $tripleTo = null;
        } else {
            [$tripleFrom, $tripleTo] = $this->clampRoomPeriod(
                $data['triple_from_date'] ?? null,
                $data['triple_to_date'] ?? null,
                $tripleStart,
                $departure
            );
        }

        $data['single_from_date'] = $singleFrom?->toDateString();
        $data['single_to_date'] = $singleTo?->toDateString();
        $data['double_from_date'] = $doubleFrom?->toDateString();
        $data['double_to_date'] = $doubleTo?->toDateString();
        $data['triple_from_date'] = $tripleFrom?->toDateString();
        $data['triple_to_date'] = $tripleTo?->toDateString();

        return $data;
    }

    /**
     * @return array{0: ?\Carbon\Carbon, 1: ?\Carbon\Carbon}
     */
    private function clampRoomPeriod(mixed $from, mixed $to, Carbon $min, Carbon $max): array
    {
        $fromDate = $from ? Carbon::parse($from)->startOfDay() : null;
        $toDate = $to ? Carbon::parse($to)->startOfDay() : null;

        if ($fromDate && $fromDate->lt($min)) {
            $fromDate = $min->copy();
        }
        if ($fromDate && $fromDate->gt($max)) {
            $fromDate = null;
        }
        if ($toDate && $toDate->gt($max)) {
            $toDate = $max->copy();
        }
        if ($toDate && $toDate->lt($min)) {
            $toDate = $min->copy();
        }
        if ($fromDate && $toDate && $toDate->lt($fromDate)) {
            $toDate = $fromDate->copy();
        }

        return [$fromDate, $toDate];
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
     * Sum each stay date's rooms × rates. Older enquiries without daily rows use one rate × nights.
     *
     * @param  array<string, mixed>  $data
     */
    private function revenueFromRoomNights(array $data): float
    {
        $rows = $data['daily_room_rates'] ?? null;
        if (is_array($rows) && $rows !== []) {
            $total = 0.0;
            foreach ($rows as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $total += ((int) ($row['single_rooms'] ?? 0) * (float) ($row['single_rate'] ?? 0))
                    + ((int) ($row['double_rooms'] ?? 0) * (float) ($row['double_rate'] ?? 0))
                    + ((int) ($row['triple_rooms'] ?? 0) * (float) ($row['triple_rate'] ?? 0));
            }

            return round($total, 2);
        }

        $nights = max(0, (int) ($data['nights'] ?? 0));
        $nightly = ((int) ($data['single_rooms'] ?? 0) * (float) ($data['single_rate'] ?? 0))
            + ((int) ($data['double_rooms'] ?? 0) * (float) ($data['double_rate'] ?? 0))
            + ((int) ($data['triple_rooms'] ?? 0) * (float) ($data['triple_rate'] ?? 0));

        return round($nightly * $nights, 2);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function applyDailyRooms(array $data): array
    {
        if (! array_key_exists('daily_rooms', $data)) {
            return $data;
        }

        $rows = $data['daily_rooms'];
        unset($data['daily_rooms']);

        if (! is_array($rows) || empty($data['check_in']) || empty($data['check_out'])) {
            $data['daily_room_rates'] = [];

            return $data;
        }

        $start = Carbon::parse($data['check_in'])->startOfDay();
        $end = Carbon::parse($data['check_out'])->startOfDay();
        $clean = [];

        foreach ($rows as $date => $row) {
            if (! is_array($row) || ! is_string($date)) {
                continue;
            }

            try {
                $day = Carbon::parse($date)->startOfDay();
            } catch (\Throwable) {
                continue;
            }

            if ($day->lt($start) || $day->gt($end)) {
                continue;
            }

            $item = ['date' => $day->toDateString()];
            foreach (['single', 'double', 'triple'] as $type) {
                $item[$type.'_rooms'] = max(0, (int) ($row[$type.'_rooms'] ?? 0));
                $item[$type.'_rate'] = round(max(0, (float) ($row[$type.'_rate'] ?? 0)), 2);
            }
            $clean[$item['date']] = $item;

            if (count($clean) >= 400) {
                break;
            }
        }

        ksort($clean);
        $data['daily_room_rates'] = array_values($clean);
        if ($data['daily_room_rates'] !== []) {
            $totals = $this->totalsFromDailyRows($data['daily_room_rates']);
            foreach (['single', 'double', 'triple'] as $type) {
                $data[$type.'_rooms'] = $totals[$type.'_rooms'];
                $data[$type.'_rate'] = $totals[$type.'_rate'];
            }
        }

        return $data;
    }

    /**
     * @return list<array{date: string, single_rooms: int, single_rate: float, double_rooms: int, double_rate: float, triple_rooms: int, triple_rate: float}>
     */
    private function cleanDailyRooms(mixed $rows, mixed $checkIn, mixed $checkOut): array
    {
        if (! is_array($rows) || empty($checkIn) || empty($checkOut)) {
            return [];
        }

        try {
            $start = Carbon::parse($checkIn)->startOfDay();
            $end = Carbon::parse($checkOut)->startOfDay();
        } catch (\Throwable) {
            return [];
        }

        $clean = [];
        foreach ($rows as $date => $row) {
            if (! is_array($row) || ! is_string($date)) {
                continue;
            }

            try {
                $day = Carbon::parse($date)->startOfDay();
            } catch (\Throwable) {
                continue;
            }

            if ($day->lt($start) || $day->gt($end)) {
                continue;
            }

            $item = ['date' => $day->toDateString()];
            foreach (['single', 'double', 'triple'] as $type) {
                $item[$type.'_rooms'] = max(0, (int) ($row[$type.'_rooms'] ?? 0));
                $item[$type.'_rate'] = round(max(0, (float) ($row[$type.'_rate'] ?? 0)), 2);
            }
            $clean[$item['date']] = $item;

            if (count($clean) >= 400) {
                break;
            }
        }

        ksort($clean);

        return array_values($clean);
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return array{single_rooms: int, single_rate: float, double_rooms: int, double_rate: float, triple_rooms: int, triple_rate: float, total_rns: int, total_revenue: float, bb_revenue: float}
     */
    private function totalsFromDailyRows(array $rows): array
    {
        $totals = [
            'single_rooms' => 0,
            'single_rate' => 0.0,
            'double_rooms' => 0,
            'double_rate' => 0.0,
            'triple_rooms' => 0,
            'triple_rate' => 0.0,
            'total_rns' => 0,
            'total_revenue' => 0.0,
            'bb_revenue' => 0.0,
        ];
        $allowance = ['single' => 10, 'double' => 20, 'triple' => 30];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            foreach (['single', 'double', 'triple'] as $type) {
                $rooms = max(0, (int) ($row[$type.'_rooms'] ?? 0));
                $rate = max(0, (float) ($row[$type.'_rate'] ?? 0));
                $totals[$type.'_rooms'] += $rooms;
                $totals[$type.'_rate'] += $rate;
                $totals['total_rns'] += $rooms;
                $totals['total_revenue'] += $rooms * $rate;
                $totals['bb_revenue'] += $rooms * $allowance[$type];
            }
        }

        foreach (['single_rate', 'double_rate', 'triple_rate', 'total_revenue', 'bb_revenue'] as $key) {
            $totals[$key] = round($totals[$key], 2);
        }

        return $totals;
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

    /**
     * @return Builder<Enquiry>
     */
    private function filteredQuery(Request $request): Builder
    {
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

        $enquiryMonth = $this->selectedEnquiryMonth($request);
        if ($enquiryMonth) {
            $query->whereYear('enquiry_date', $enquiryMonth->year)
                ->whereMonth('enquiry_date', $enquiryMonth->month);
        }

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
        ], 'enquiry_date', 'desc');

        return $query;
    }

    private function enquiryMonthValue(Request $request): string
    {
        if (! $request->exists('month')) {
            return now()->format('Y-m');
        }

        return $request->string('month')->toString();
    }

    private function selectedEnquiryMonth(Request $request): ?Carbon
    {
        if (! $request->exists('month')) {
            return now()->startOfMonth();
        }

        $value = $request->string('month')->toString();
        if (! preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $value)) {
            return null;
        }

        $month = Carbon::createFromFormat('!Y-m', $value);

        return $month ? $month->startOfMonth() : null;
    }

    /**
     * @return list<string>
     */
    private function enquiryExportHeaders(): array
    {
        return [
            'Enquiry Date',
            'Response Date',
            'Arrival Date',
            'Departure Date',
            'Day',
            'Nights',
            'Total Room per Night',
            'Group Name',
            'Ref No',
            'Email ID',
            'Status',
            'Single',
            'Single Rate',
            'Double',
            'Double Rate',
            'Triple',
            'Triple Rate',
            'Basis',
            'Option Date',
            'CXL Policy',
            'CXL Due Date',
            'Total Revenue',
            'Remarks',
        ];
    }

    /**
     * @return list<string|int|float|null>
     */
    private function enquiryExportRow(Enquiry $enquiry): array
    {
        $status = str_replace(['_', '-'], ' ', (string) $enquiry->status);

        return [
            $enquiry->enquiry_date?->format('Y-m-d'),
            $enquiry->response_date?->format('Y-m-d'),
            $enquiry->check_in?->format('Y-m-d'),
            $enquiry->check_out?->format('Y-m-d'),
            $enquiry->day,
            $this->exportInt($enquiry->nights),
            $this->exportInt($enquiry->rooms_per_night),
            $enquiry->group_name,
            $enquiry->ref,
            $enquiry->email,
            $status !== '' ? ucwords($status) : null,
            $this->exportInt($enquiry->single_rooms),
            $this->exportMoney($enquiry->single_rate),
            $this->exportInt($enquiry->double_rooms),
            $this->exportMoney($enquiry->double_rate),
            $this->exportInt($enquiry->triple_rooms),
            $this->exportMoney($enquiry->triple_rate),
            $enquiry->basis,
            $enquiry->option_date?->format('Y-m-d'),
            $enquiry->cxl_policy,
            $enquiry->cxl_due_date?->format('Y-m-d'),
            $this->exportMoney($enquiry->total_revenue),
            $enquiry->remarks,
        ];
    }

    private function exportMoney(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return round((float) $value, 2);
    }

    private function paymentDueDate(?string $term, ?int $days, mixed $arrival, mixed $departure): ?string
    {
        if (! in_array($term, ['Pre Arrival', 'Post Departure'], true) || $days === null) {
            return null;
        }

        $base = $term === 'Pre Arrival' ? $arrival : $departure;
        if ($base === null || $base === '') {
            return null;
        }

        return Carbon::parse($base)
            ->startOfDay()
            ->addDays($term === 'Pre Arrival' ? -$days : $days)
            ->toDateString();
    }

    private function exportInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    /**
     * @return list<array{group_name: string, ref: string}>
     */
    private function existingGroupRefPairs(?int $ignoreEnquiryId = null): array
    {
        $query = Enquiry::query()
            ->select(['group_name', 'ref'])
            ->whereNotNull('ref')
            ->where('ref', '!=', '');

        if ($ignoreEnquiryId !== null) {
            $query->where('id', '!=', $ignoreEnquiryId);
        }

        return $query
            ->get()
            ->map(fn (Enquiry $enquiry) => [
                'group_name' => mb_strtolower(trim((string) $enquiry->group_name)),
                'ref' => mb_strtolower(trim((string) $enquiry->ref)),
            ])
            ->values()
            ->all();
    }
}
