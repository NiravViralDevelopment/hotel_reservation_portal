<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Http\Requests\StoreGroupBookingRequest;
use App\Http\Requests\UpdateGroupBookingRequest;
use App\Models\Company;
use App\Models\Contact;
use App\Models\GroupBooking;
use App\Models\Hotel;
use App\Models\TravelAgency;
use App\Support\Audit;
use App\Support\QuerySort;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class GroupBookingController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', GroupBooking::class);

        $query = GroupBooking::query()
            ->accessibleBy()
            ->with(['hotel', 'travelAgency', 'company'])
            ->active();

        if ($request->filled('hotel_id')) {
            \App\Support\HotelAccess::ensure(null, $request->integer('hotel_id'));
            $query->where('hotel_id', $request->integer('hotel_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        QuerySort::apply($query, $request, [
            'block_id' => 'block_id',
            'group_name' => 'group_name',
            'arrival' => 'arrival',
            'departure' => 'departure',
            'status' => 'status',
            'revenue' => 'revenue',
            'rooms' => 'rooms',
            'nights' => 'nights',
        ], 'arrival');

        $bookings = $query->paginate(25)->withQueryString();
        $hotels = Hotel::query()->accessibleBy()->orderBy('name')->get(['id', 'name', 'code']);

        return view('group-bookings.index', compact('bookings', 'hotels'));
    }

    public function create(): View
    {
        $this->authorize('create', GroupBooking::class);

        $companies = Company::query()->orderBy('name')->get(['id', 'name']);
        $hotels = Hotel::query()->accessibleBy()->orderBy('name')->get(['id', 'name', 'code']);
        $travelAgencies = TravelAgency::query()->orderBy('name')->get(['id', 'name', 'code']);
        $contacts = Contact::query()->orderBy('name')->get(['id', 'name']);
        $statuses = BookingStatus::values();

        return view('group-bookings.create', compact('companies', 'hotels', 'travelAgencies', 'contacts', 'statuses'));
    }

    public function store(StoreGroupBookingRequest $request): RedirectResponse
    {
        $this->authorize('create', GroupBooking::class);

        $data = $request->validated();
        \App\Support\HotelAccess::ensure(null, $data['hotel_id'] ?? null);
        $data['created_by'] = auth()->id();
        $data = $this->applyDateMeta($data);

        $booking = GroupBooking::query()->create($data);
        Audit::log('created', 'group_bookings', $booking->block_id, $booking);

        return redirect()->route('group-bookings.show', $booking)->with('success', 'Group booking created.');
    }

    public function show(GroupBooking $groupBooking): View
    {
        $this->authorize('view', $groupBooking);

        $groupBooking->load([
            'hotel',
            'company',
            'travelAgency',
            'contact',
            'enquiry',
            'createdBy',
            'dailyRows',
            'documents.uploadedBy',
        ]);

        return view('group-bookings.show', compact('groupBooking'));
    }

    public function edit(GroupBooking $groupBooking): View
    {
        $this->authorize('update', $groupBooking);

        $companies = Company::query()->orderBy('name')->get(['id', 'name']);
        $hotels = Hotel::query()->accessibleBy()->orderBy('name')->get(['id', 'name', 'code']);
        $travelAgencies = TravelAgency::query()->orderBy('name')->get(['id', 'name', 'code']);
        $contacts = Contact::query()->orderBy('name')->get(['id', 'name']);
        $statuses = BookingStatus::values();

        return view('group-bookings.edit', compact('groupBooking', 'companies', 'hotels', 'travelAgencies', 'contacts', 'statuses'));
    }

    public function update(UpdateGroupBookingRequest $request, GroupBooking $groupBooking): RedirectResponse
    {
        $this->authorize('update', $groupBooking);

        $data = $this->applyDateMeta($request->validated());
        \App\Support\HotelAccess::ensure(null, $data['hotel_id'] ?? $groupBooking->hotel_id);
        $groupBooking->update($data);
        Audit::log('updated', 'group_bookings', $groupBooking->block_id, $groupBooking);

        return redirect()->route('group-bookings.show', $groupBooking)->with('success', 'Group booking updated.');
    }

    public function destroy(GroupBooking $groupBooking): RedirectResponse
    {
        $this->authorize('delete', $groupBooking);

        $blockId = $groupBooking->block_id;
        $groupBooking->delete();
        Audit::log('deleted', 'group_bookings', $blockId);

        return redirect()->route('group-bookings.index')->with('success', 'Group booking deleted.');
    }

    public function cancel(Request $request, GroupBooking $groupBooking): RedirectResponse
    {
        $this->authorize('cancel', $groupBooking);

        $validated = $request->validate([
            'cancellation_reason' => ['nullable', 'string', 'max:500'],
            'revenue_lost' => ['nullable', 'numeric', 'min:0'],
            'cxl_date' => ['nullable', 'date'],
        ]);

        $groupBooking->update([
            ...$validated,
            'status' => BookingStatus::Cancelled->value,
            'cancelled_at' => now(),
        ]);

        Audit::log('cancelled', 'group_bookings', $groupBooking->block_id, $groupBooking);

        return redirect()->route('group-bookings.show', $groupBooking)->with('success', 'Group booking cancelled.');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function applyDateMeta(array $data): array
    {
        if (! empty($data['arrival']) && ! empty($data['departure'])) {
            $arrival = Carbon::parse($data['arrival']);
            $departure = Carbon::parse($data['departure']);
            $data['arrival_day'] = $arrival->format('l');
            $data['nights'] = $data['nights'] ?? max(1, $arrival->diffInDays($departure));
        }

        return $data;
    }
}
