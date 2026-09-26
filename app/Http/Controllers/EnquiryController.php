<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Enums\EnquiryStatus;
use App\Http\Requests\StoreEnquiryRequest;
use App\Models\Enquiry;
use App\Models\GroupBooking;
use App\Models\Hotel;
use App\Models\TravelAgency;
use App\Models\User;
use App\Support\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class EnquiryController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Enquiry::class);

        $enquiries = Enquiry::query()
            ->with(['travelAgency', 'hotel', 'contact', 'assignedTo'])
            ->latest('enquiry_date')
            ->paginate(20);

        return view('enquiries.index', compact('enquiries'));
    }

    public function create(): View
    {
        $this->authorize('create', Enquiry::class);

        $travelAgencies = TravelAgency::query()->orderBy('name')->get(['id', 'name', 'code']);
        $hotels = Hotel::query()->orderBy('name')->get(['id', 'name', 'code']);
        $users = User::query()->where('status', 'active')->orderBy('name')->get(['id', 'name']);
        $statuses = EnquiryStatus::values();

        return view('enquiries.create', compact('travelAgencies', 'hotels', 'users', 'statuses'));
    }

    public function store(StoreEnquiryRequest $request): RedirectResponse
    {
        $this->authorize('create', Enquiry::class);

        $data = $request->validated();
        if (! empty($data['enquiry_date'])) {
            $data['day'] = Carbon::parse($data['enquiry_date'])->format('l');
        }
        if (empty($data['year']) && ! empty($data['enquiry_date'])) {
            $data['year'] = (int) Carbon::parse($data['enquiry_date'])->format('Y');
        }

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
        $hotels = Hotel::query()->orderBy('name')->get(['id', 'name', 'code']);
        $users = User::query()->where('status', 'active')->orderBy('name')->get(['id', 'name']);
        $statuses = EnquiryStatus::values();

        return view('enquiries.edit', compact('enquiry', 'travelAgencies', 'hotels', 'users', 'statuses'));
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
            'hotel_id' => ['nullable', 'integer', 'exists:hotels,id'],
            'contact_id' => ['nullable', 'integer', 'exists:contacts,id'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
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
        ]);

        if (! empty($data['enquiry_date'])) {
            $data['day'] = Carbon::parse($data['enquiry_date'])->format('l');
        }

        $enquiry->update($data);
        Audit::log('updated', 'enquiries', $enquiry->ref, $enquiry);

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

        $arrival = $enquiry->option_date
            ? Carbon::parse($enquiry->option_date)->startOfDay()
            : now()->addMonth()->startOfDay();
        $nights = max(1, (int) $enquiry->nights);
        $departure = $arrival->copy()->addDays($nights);

        $booking = GroupBooking::query()->create([
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
        ]);

        $enquiry->update([
            'converted_booking_id' => $booking->id,
            'status' => EnquiryStatus::Confirmed->value,
        ]);

        Audit::log('converted', 'enquiries', $enquiry->ref.' → '.$booking->block_id, $booking);

        return redirect()->route('group-bookings.show', $booking)->with('success', 'Enquiry converted to group booking.');
    }
}
