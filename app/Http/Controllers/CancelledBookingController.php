<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\Hotel;
use App\Models\TravelAgency;
use App\Support\Audit;
use App\Support\HotelAccess;
use App\Support\QuerySort;
use App\Support\SimpleXlsxWriter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CancelledBookingController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(auth()->user()?->can('bookings.view'), 403);

        $bookings = $this->filteredQuery($request)->paginate(10)->withQueryString();
        $hotels = Hotel::optionsForSelect();
        $travelAgencies = TravelAgency::query()->orderBy('name')->get(['id', 'name', 'code']);

        return view('cancelled-bookings.index', compact('bookings', 'hotels', 'travelAgencies'));
    }

    public function show(Enquiry $enquiry): View
    {
        abort_unless(auth()->user()?->can('bookings.view'), 403);
        abort_unless($enquiry->is_confirm && $enquiry->is_cancel, 404);
        HotelAccess::ensure(null, $enquiry->hotel_id);

        return view('group-bookings.show', [
            'enquiry' => $enquiry,
            'cancelledContext' => true,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        abort_unless(auth()->user()?->can('bookings.view'), 403);

        $rows = $this->filteredQuery($request)->get()->map(function (Enquiry $enquiry) {
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
                $enquiry->cancellation_reason,
            ];
        })->all();

        Audit::log('exported', 'cancelled_bookings', 'rows='.count($rows));

        return SimpleXlsxWriter::download(
            'cancelled-bookings-'.now()->format('Y-m-d').'.xlsx',
            [
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
                'Cancellation Reason',
            ],
            $rows
        );
    }

    /**
     * @return Builder<Enquiry>
     */
    private function filteredQuery(Request $request): Builder
    {
        $query = Enquiry::query()
            ->accessibleBy()
            ->with(['hotel', 'travelAgency'])
            ->cancelledBookings();

        if ($request->filled('q')) {
            $search = $request->string('q')->trim()->toString();
            $query->where(function ($builder) use ($search) {
                $builder->where('ref', 'like', "%{$search}%")
                    ->orWhere('block_id', 'like', "%{$search}%")
                    ->orWhere('group_name', 'like', "%{$search}%")
                    ->orWhere('client', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('cancellation_reason', 'like', "%{$search}%");
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
            'updated_at' => 'updated_at',
        ], 'updated_at', 'desc');

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
