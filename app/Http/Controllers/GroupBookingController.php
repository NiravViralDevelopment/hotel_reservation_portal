<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\Hotel;
use App\Models\TravelAgency;
use App\Support\Audit;
use App\Support\EnquiryIndexFilters;
use App\Support\BookingContractHtml;
use App\Support\GroupBookingExcelImporter;
use App\Support\HotelAccess;
use App\Support\QuerySort;
use App\Support\SimpleXlsxWriter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GroupBookingController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(auth()->user()?->can('bookings.view'), 403);

        $bookings = $this->filteredQuery($request)->paginate(10)->withQueryString();
        $hotels = Hotel::query()
            ->accessibleBy()
            ->active()
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'code',
                'status',
                'document_disk',
                'document_path',
                'document_original_name',
                'document_mime_type',
                'document_size',
            ]);
        $travelAgencies = TravelAgency::query()->orderBy('name')->get(['id', 'name', 'code']);
        $monthValue = EnquiryIndexFilters::monthValue($request);
        $reminders = $this->reminders();

        return view('group-bookings.index', compact('bookings', 'hotels', 'travelAgencies', 'monthValue', 'reminders'));
    }

    public function show(Enquiry $enquiry): View
    {
        abort_unless(auth()->user()?->can('bookings.view'), 403);
        abort_unless($enquiry->is_confirm, 404);
        HotelAccess::ensure(null, $enquiry->hotel_id);

        return view('group-bookings.show', compact('enquiry'));
    }

    public function export(Request $request): StreamedResponse
    {
        abort_unless(auth()->user()?->can('bookings.view'), 403);

        $bookings = $this->filteredQuery($request)->get();

        $headers = [
            'Date of Arrival',
            'Date of Departure',
            'Day',
            'No. of Nights',
            'Block ID',
            'Client',
            'Agency - Ref',
            'Contact',
            'Email ID',
            'Status',
            'Contract Sent On',
            'Contract Recd On',
            'Saved to Doc',
            'Payment Term',
            'Due Date',
            'Payment Status',
            'CXL Policy',
            'CXL Due Date',
            'CXL Date',
            'Commission',
            'Single RNs',
            'Single Gross Rate',
            'Double RNs',
            'Double Gross Rate',
            'Triple RNs',
            'Triple Gross Rate',
            'Total RNs',
            'Total Rev',
            'BB Revenue (Nett £10)',
            'Dinner Revenue (Nett £)',
            'Nett Rev EX VAT & BF',
            'BB/DBB',
            'Update',
            'Rooming',
            'Invoice Status',
            'Invoice Number',
            'Invoice Sent On',
            'Invoice Amount',
            'Commission Payable Status',
        ];

        $rows = $bookings->map(function (Enquiry $enquiry) {
            $status = str_replace(['_', '-'], ' ', (string) $enquiry->status);

            return [
                $enquiry->check_in?->format('Y-m-d'),
                $enquiry->check_out?->format('Y-m-d'),
                $enquiry->day,
                $enquiry->nights,
                $enquiry->block_id,
                $enquiry->client,
                $enquiry->agency_ref,
                $enquiry->contact_name,
                $enquiry->email,
                $status !== '' ? ucwords($status) : null,
                $enquiry->contract_sent_on?->format('Y-m-d'),
                $enquiry->contract_received_on?->format('Y-m-d'),
                $enquiry->saved_to_doc,
                $enquiry->payment_term,
                $enquiry->payment_due_date?->format('Y-m-d'),
                $enquiry->payment_status,
                $enquiry->cxl_policy,
                $enquiry->cxl_due_date?->format('Y-m-d'),
                $enquiry->cxl_date?->format('Y-m-d'),
                $enquiry->has_commission === null ? null : ($enquiry->has_commission ? 'Yes' : 'No'),
                $enquiry->single_rooms,
                $this->exportMoney($enquiry->single_rate),
                $enquiry->double_rooms,
                $this->exportMoney($enquiry->double_rate),
                $enquiry->triple_rooms,
                $this->exportMoney($enquiry->triple_rate),
                $enquiry->total_rns,
                $this->exportMoney($enquiry->total_revenue),
                $this->exportMoney($enquiry->bb_revenue),
                $this->exportMoney($enquiry->dinner_revenue),
                $this->exportMoney($enquiry->nett_rev_ex_vat),
                $enquiry->basis,
                $enquiry->booking_update,
                $enquiry->rooming,
                $enquiry->invoice_status,
                $enquiry->invoice_number,
                $enquiry->invoice_sent_on?->format('Y-m-d'),
                $this->exportMoney($enquiry->invoice_amount),
                $enquiry->commission_payable_status,
            ];
        })->all();

        Audit::log('exported', 'group_bookings', 'rows='.count($rows));

        return SimpleXlsxWriter::download(
            'group-bookings-'.now()->format('Y-m-d').'.xlsx',
            $headers,
            $rows
        );
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

    public function edit(Enquiry $enquiry): View
    {
        $this->authorize('update', $enquiry);
        abort_unless($enquiry->is_confirm && ! $enquiry->is_cancel, 404);
        HotelAccess::ensure(null, $enquiry->hotel_id);

        return view('group-bookings.edit', compact('enquiry'));
    }

    public function contract(Request $request, Enquiry $enquiry): View
    {
        abort_unless(auth()->user()?->can('bookings.view'), 403);
        abort_unless($enquiry->is_confirm, 404);
        HotelAccess::ensure(null, $enquiry->hotel_id);

        $enquiry->load(['hotel.company', 'hotel.managerUser', 'travelAgency', 'contact', 'bookingContractHotel']);

        $selectedHotel = $enquiry->hotel;

        $reloadHtml = $request->boolean('reload_html');
        $savedHtml = $enquiry->booking_contract_html;
        $contractHtml = old('booking_contract_html');

        if ($contractHtml === null) {
            $savedIsTemplate = BookingContractHtml::isTemplateDocument($savedHtml);
            if (! $reloadHtml && $savedIsTemplate) {
                $contractHtml = $savedHtml;
            } else {
                $contractHtml = BookingContractHtml::build($enquiry, $selectedHotel);
            }
        }

        return view('group-bookings.contract', compact(
            'enquiry',
            'selectedHotel',
            'contractHtml'
        ));
    }

    public function updateContract(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $this->authorize('update', $enquiry);
        abort_unless($enquiry->is_confirm && ! $enquiry->is_cancel, 404);
        HotelAccess::ensure(null, $enquiry->hotel_id);

        $validated = $request->validate([
            'booking_contract_html' => ['nullable', 'string', 'max:200000'],
        ]);

        $enquiry->fill([
            'booking_contract_html' => BookingContractHtml::sanitize($validated['booking_contract_html'] ?? null),
        ]);

        if (blank($enquiry->saved_to_doc)) {
            $enquiry->saved_to_doc = 'Yes';
        }

        $enquiry->save();

        Audit::log('updated', 'group-booking-contracts', $enquiry->block_id ?: $enquiry->ref, $enquiry);

        return redirect()
            ->route('group-bookings.contract', $enquiry)
            ->with('success', 'Booking contract saved.');
    }

    public function previewHotelContract(Request $request, Enquiry $enquiry): StreamedResponse
    {
        abort_unless(auth()->user()?->can('bookings.view'), 403);
        abort_unless($enquiry->is_confirm, 404);
        HotelAccess::ensure(null, $enquiry->hotel_id);

        $hotelId = $request->integer('hotel_id') ?: (int) ($enquiry->hotel_id ?? 0);
        abort_unless($hotelId > 0, 404);
        HotelAccess::ensure(null, $hotelId);

        $hotel = Hotel::query()->accessibleBy()->findOrFail($hotelId);
        abort_unless($hotel->hasDocument(), 404);

        return $this->streamStoredFile(
            $hotel->document_disk ?: 'local',
            (string) $hotel->document_path,
            $hotel->document_original_name ?: ($hotel->code.'-contract'),
            $hotel->document_mime_type,
            inline: true
        );
    }

    public function downloadBookingContract(Enquiry $enquiry): StreamedResponse
    {
        abort_unless(auth()->user()?->can('bookings.view'), 403);
        abort_unless($enquiry->is_confirm, 404);
        HotelAccess::ensure(null, $enquiry->hotel_id);
        abort_unless($enquiry->hasBookingContract(), 404);

        return $this->streamStoredFile(
            $enquiry->booking_contract_disk ?: 'local',
            (string) $enquiry->booking_contract_path,
            $enquiry->booking_contract_original_name ?: (($enquiry->block_id ?: 'booking').'-contract'),
            $enquiry->booking_contract_mime_type,
            inline: false
        );
    }

    public function viewHotelContract(Hotel $hotel): StreamedResponse
    {
        abort_unless(auth()->user()?->can('bookings.view'), 403);
        HotelAccess::ensure(null, $hotel->id);
        abort_unless($hotel->hasDocument(), 404);

        return $this->streamStoredFile(
            $hotel->document_disk ?: 'local',
            (string) $hotel->document_path,
            $hotel->document_original_name ?: ($hotel->code.'-contract'),
            $hotel->document_mime_type,
            inline: true
        );
    }

    public function update(Enquiry $enquiry): RedirectResponse
    {
        return redirect()->route('enquiries.edit', $enquiry);
    }

    public function destroy(Enquiry $enquiry): RedirectResponse
    {
        return redirect()->route('enquiries.show', $enquiry);
    }

    private function streamStoredFile(
        string $disk,
        string $path,
        string $filename,
        ?string $mimeType,
        bool $inline = false
    ): StreamedResponse {
        abort_unless($path !== '' && ! str_contains($path, '..') && Storage::disk($disk)->exists($path), 404);

        $disposition = $inline ? 'inline' : 'attachment';

        return Storage::disk($disk)->response($path, $filename, [
            'Content-Type' => $mimeType ?: 'application/octet-stream',
            'Content-Disposition' => $disposition.'; filename="'.$filename.'"',
        ]);
    }

    /**
     * @return array{arrivals: int, payments: int, payments_overdue: int, cxl: int, cxl_overdue: int}
     */
    private function reminders(): array
    {
        $base = Enquiry::query()->accessibleBy()->groupBookings();
        $today = now()->toDateString();
        $in7 = now()->addDays(7)->toDateString();

        return [
            'arrivals' => (clone $base)
                ->whereNotNull('check_in')
                ->whereDate('check_in', '>=', $today)
                ->whereDate('check_in', '<=', $in7)
                ->count(),
            'payments' => (clone $base)
                ->whereNotNull('payment_due_date')
                ->whereDate('payment_due_date', '<=', $in7)
                ->count(),
            'payments_overdue' => (clone $base)
                ->whereNotNull('payment_due_date')
                ->whereDate('payment_due_date', '<', $today)
                ->count(),
            'cxl' => (clone $base)
                ->whereNotNull('cxl_due_date')
                ->whereDate('cxl_due_date', '<=', $in7)
                ->count(),
            'cxl_overdue' => (clone $base)
                ->whereNotNull('cxl_due_date')
                ->whereDate('cxl_due_date', '<', $today)
                ->count(),
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
            ->groupBookings();

        if ($request->filled('q')) {
            $search = $request->string('q')->trim()->toString();
            $query->where(function ($builder) use ($search) {
                $builder->where('ref', 'like', "%{$search}%")
                    ->orWhere('block_id', 'like', "%{$search}%")
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

        EnquiryIndexFilters::applyMonth($query, $request, 'enquiry_date');

        QuerySort::apply($query, $request, [
            'check_in' => 'check_in',
            'check_out' => 'check_out',
            'day' => 'day',
            'nights' => 'nights',
            'block_id' => 'block_id',
            'client' => 'client',
            'email' => 'email',
            'status' => 'status',
            'total_rns' => 'total_rns',
            'total_revenue' => 'total_revenue',
        ], 'check_in', 'asc');

        return $query;
    }

    private function exportMoney(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return round((float) $value, 2);
    }
}
