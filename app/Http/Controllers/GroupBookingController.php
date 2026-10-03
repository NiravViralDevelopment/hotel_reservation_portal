<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Http\Requests\StoreGroupBookingRequest;
use App\Http\Requests\UpdateGroupBookingRequest;
use App\Models\Company;
use App\Models\Document;
use App\Models\GroupBooking;
use App\Models\Hotel;
use App\Models\TravelAgency;
use App\Support\Audit;
use App\Support\QuerySort;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GroupBookingController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', GroupBooking::class);

        $query = GroupBooking::query()
            ->accessibleBy()
            ->with(['hotel', 'travelAgency', 'company', 'contact', 'createdBy'])
            ->active();

        if ($request->filled('q')) {
            $search = $request->string('q')->trim()->toString();
            $query->where(function ($builder) use ($search) {
                $builder->where('block_id', 'like', "%{$search}%")
                    ->orWhere('group_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('client', 'like', "%{$search}%")
                    ->orWhere('agency_name', 'like', "%{$search}%");
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

        if ($request->filled('arrival_from')) {
            $query->whereDate('arrival', '>=', $request->string('arrival_from'));
        }

        if ($request->filled('arrival_to')) {
            $query->whereDate('arrival', '<=', $request->string('arrival_to'));
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

        $bookings = $query->paginate(10)->withQueryString();
        $hotels = Hotel::optionsForSelect();
        $travelAgencies = TravelAgency::query()->orderBy('name')->get(['id', 'name', 'code']);
        $statuses = BookingStatus::values();

        return view('group-bookings.index', compact('bookings', 'hotels', 'travelAgencies', 'statuses'));
    }

    public function create(): View
    {
        $this->authorize('create', GroupBooking::class);

        $companies = Company::query()->orderBy('name')->get(['id', 'name']);
        $hotels = Hotel::optionsForSelect();
        $travelAgencies = TravelAgency::query()->orderBy('name')->get(['id', 'name', 'code']);
        $statuses = BookingStatus::values();

        return view('group-bookings.create', compact('companies', 'hotels', 'travelAgencies', 'statuses'));
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

        $groupBooking->load(['documents.uploadedBy']);
        $companies = Company::query()->orderBy('name')->get(['id', 'name']);
        $hotels = Hotel::optionsForSelect($groupBooking->hotel_id);
        $travelAgencies = TravelAgency::query()->orderBy('name')->get(['id', 'name', 'code']);
        $statuses = BookingStatus::values();

        return view('group-bookings.edit', compact('groupBooking', 'companies', 'hotels', 'travelAgencies', 'statuses'));
    }

    public function update(UpdateGroupBookingRequest $request, GroupBooking $groupBooking): RedirectResponse
    {
        $this->authorize('update', $groupBooking);

        $data = $this->applyDateMeta($request->validated());
        unset($data['document_name'], $data['document_file']);

        \App\Support\HotelAccess::ensure(null, $data['hotel_id'] ?? $groupBooking->hotel_id);
        $groupBooking->update($data);
        Audit::log('updated', 'group_bookings', $groupBooking->block_id, $groupBooking);

        if ($request->hasFile('document_file')) {
            $this->storeBookingDocument($request, $groupBooking);
        }

        return redirect()->route('group-bookings.edit', $groupBooking)->with('success', 'Group booking updated.');
    }

    public function downloadDocument(GroupBooking $groupBooking, Document $document): StreamedResponse
    {
        $this->authorize('view', $groupBooking);

        abort_unless((int) $document->group_booking_id === (int) $groupBooking->id, 404);

        return Storage::disk($document->disk ?: 'local')->download($document->path, $document->name);
    }

    public function destroyDocument(GroupBooking $groupBooking, Document $document): RedirectResponse
    {
        $this->authorize('update', $groupBooking);

        abort_unless((int) $document->group_booking_id === (int) $groupBooking->id, 404);

        Storage::disk($document->disk ?: 'local')->delete($document->path);
        $name = $document->name;
        $document->delete();
        Audit::log('deleted', 'documents', $name);

        return redirect()->route('group-bookings.edit', $groupBooking)->with('success', 'Document removed.');
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

    private function storeBookingDocument(Request $request, GroupBooking $groupBooking): void
    {
        $file = $request->file('document_file');
        $name = $request->string('document_name')->toString() ?: $file->getClientOriginalName();
        $path = $file->store('documents/general', 'local');

        $document = Document::query()->create([
            'name' => $name,
            'category' => 'general',
            'group_booking_id' => $groupBooking->id,
            'uploaded_by' => auth()->id(),
            'disk' => 'local',
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        Audit::log('uploaded', 'documents', $document->name, $document);
    }
}
