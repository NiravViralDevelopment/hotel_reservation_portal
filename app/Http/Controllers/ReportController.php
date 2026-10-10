<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\Hotel;
use App\Support\Audit;
use App\Support\HotelAccess;
use App\Support\QuerySort;
use App\Support\SimpleXlsxWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(): View
    {
        $this->authorize('reports.view');

        $hotels = Hotel::optionsForSelect();

        $defaultFrom = now()->subMonth()->toDateString();
        $defaultTo = now()->toDateString();

        return view('reports.index', compact('hotels', 'defaultFrom', 'defaultTo'));
    }

    public function module(Request $request, string $report): View
    {
        $this->authorize('reports.view');

        $map = [
            'group-bookings' => 'group_bookings',
            'enquiries' => 'enquiries',
            'cancelled-bookings' => 'cancelled_bookings',
        ];

        abort_unless(isset($map[$report]), 404);

        $validated = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $validated['report'] = $map[$report];
        $validated['date_from'] = $validated['date_from'] ?? now()->subMonth()->toDateString();
        $validated['date_to'] = $validated['date_to'] ?? now()->toDateString();

        if (Carbon::parse($validated['date_to'])->lt(Carbon::parse($validated['date_from']))) {
            $validated['date_to'] = $validated['date_from'];
        }

        $results = $this->datedReport($validated, $request);
        $hotels = collect();

        return view('reports.run', compact('results', 'validated', 'hotels'));
    }

    public function export(Request $request, string $report): StreamedResponse
    {
        $this->authorize('reports.view');

        $map = [
            'group-bookings' => 'group_bookings',
            'enquiries' => 'enquiries',
            'cancelled-bookings' => 'cancelled_bookings',
        ];

        abort_unless(isset($map[$report]), 404);

        $validated = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $validated['report'] = $map[$report];
        $validated['date_from'] = $validated['date_from'] ?? now()->subMonth()->toDateString();
        $validated['date_to'] = $validated['date_to'] ?? now()->toDateString();

        if (Carbon::parse($validated['date_to'])->lt(Carbon::parse($validated['date_from']))) {
            $validated['date_to'] = $validated['date_from'];
        }

        $results = $this->datedReport($validated, $request);

        [$headers, $rows, $filename] = match ($validated['report']) {
            'enquiries' => $this->enquiriesExport($results),
            'group_bookings' => $this->bookingsExport($results, false),
            'cancelled_bookings' => $this->bookingsExport($results, true),
        };

        Audit::log('exported', 'reports-'.$validated['report'], 'rows='.count($rows));

        return SimpleXlsxWriter::download($filename, $headers, $rows);
    }

    public function run(Request $request): View|RedirectResponse
    {
        $this->authorize('reports.generate');

        $datedReports = ['group_bookings', 'enquiries', 'cancelled_bookings'];

        $validated = $request->validate([
            'report' => ['required', 'in:group_bookings,enquiries,cancelled_bookings,bookings_by_hotel,arrivals_summary,revenue_by_agency'],
            'date_from' => [
                'nullable',
                'date',
                Rule::requiredIf(fn () => in_array($request->input('report'), $datedReports, true)),
            ],
            'date_to' => [
                'nullable',
                'date',
                'after_or_equal:date_from',
                Rule::requiredIf(fn () => in_array($request->input('report'), $datedReports, true)),
            ],
            'hotel_id' => ['nullable', 'integer', Rule::in(HotelAccess::selectableHotelIds())],
        ]);

        if (! empty($validated['hotel_id'])) {
            HotelAccess::ensure(null, $validated['hotel_id']);
        }

        $moduleMap = [
            'enquiries' => 'enquiries',
            'group_bookings' => 'group-bookings',
            'cancelled_bookings' => 'cancelled-bookings',
        ];

        if (isset($moduleMap[$validated['report']])) {
            return redirect()->route('reports.module', [
                'report' => $moduleMap[$validated['report']],
                'date_from' => $validated['date_from'],
                'date_to' => $validated['date_to'],
            ]);
        }

        $query = Enquiry::query()
            ->accessibleBy()
            ->with(['hotel', 'travelAgency'])
            ->groupBookings();

        if ($validated['date_from'] ?? null) {
            $query->whereDate('check_in', '>=', $validated['date_from']);
        }

        if ($validated['date_to'] ?? null) {
            $query->whereDate('check_in', '<=', $validated['date_to']);
        }

        if ($validated['hotel_id'] ?? null) {
            $query->where('hotel_id', $validated['hotel_id']);
        }

        $results = match ($validated['report']) {
            'bookings_by_hotel' => (clone $query)
                ->select('hotel_id', DB::raw('COUNT(*) as bookings'), DB::raw('SUM(grand_total) as revenue'))
                ->groupBy('hotel_id')
                ->get(),
            'arrivals_summary' => (clone $query)
                ->orderBy('check_in')
                ->get(),
            'revenue_by_agency' => (clone $query)
                ->select('travel_agency_id', DB::raw('COUNT(*) as bookings'), DB::raw('SUM(grand_total) as revenue'))
                ->groupBy('travel_agency_id')
                ->get(),
        };

        $hotels = Hotel::optionsForSelect();

        return view('reports.run', compact('results', 'validated', 'hotels'));
    }

    /**
     * @param  array{report: string, date_from: string, date_to: string}  $validated
     */
    private function datedReport(array $validated, ?Request $request = null): mixed
    {
        $from = $validated['date_from'];
        $to = $validated['date_to'];

        return match ($validated['report']) {
            'group_bookings' => $this->groupBookingsReportQuery($from, $to, $request)->get(),
            'enquiries' => $this->enquiriesReportQuery($from, $to, $request)->get(),
            'cancelled_bookings' => $this->cancelledBookingsReportQuery($from, $to, $request)->get(),
        };
    }

    private function enquiriesReportQuery(string $from, string $to, ?Request $request = null)
    {
        $query = Enquiry::query()
            ->accessibleBy()
            ->with(['hotel', 'travelAgency', 'assignedTo'])
            ->openPipeline()
            ->whereDate('enquiry_date', '>=', $from)
            ->whereDate('enquiry_date', '<=', $to);

        QuerySort::apply($query, $request ?? request(), [
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

    private function groupBookingsReportQuery(string $from, string $to, ?Request $request = null)
    {
        $query = Enquiry::query()
            ->accessibleBy()
            ->with(['hotel', 'travelAgency', 'assignedTo'])
            ->groupBookings()
            ->whereDate('check_in', '>=', $from)
            ->whereDate('check_in', '<=', $to);

        QuerySort::apply($query, $request ?? request(), [
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

    private function cancelledBookingsReportQuery(string $from, string $to, ?Request $request = null)
    {
        $query = Enquiry::query()
            ->accessibleBy()
            ->with(['hotel', 'travelAgency', 'assignedTo'])
            ->cancelledBookings()
            ->whereDate('updated_at', '>=', $from)
            ->whereDate('updated_at', '<=', $to);

        QuerySort::apply($query, $request ?? request(), [
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

    /**
     * @return array{0: list<string>, 1: list<list<mixed>>, 2: string}
     */
    private function enquiriesExport($results): array
    {
        $headers = [
            'Hotel',
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
            'Created by',
        ];

        $rows = $results->map(function (Enquiry $enquiry) {
            $status = str_replace(['_', '-'], ' ', (string) $enquiry->status);

            return [
                $enquiry->hotel?->name,
                $enquiry->enquiry_date?->format('Y-m-d'),
                $enquiry->response_date?->format('Y-m-d'),
                $enquiry->check_in?->format('Y-m-d'),
                $enquiry->check_out?->format('Y-m-d'),
                $enquiry->day,
                $enquiry->nights,
                $enquiry->rooms_per_night,
                $enquiry->group_name,
                $enquiry->ref,
                $enquiry->email,
                $status !== '' ? ucwords($status) : null,
                $enquiry->single_rooms,
                $this->exportMoney($enquiry->single_rate),
                $enquiry->double_rooms,
                $this->exportMoney($enquiry->double_rate),
                $enquiry->triple_rooms,
                $this->exportMoney($enquiry->triple_rate),
                $enquiry->basis,
                $enquiry->option_date?->format('Y-m-d'),
                $enquiry->cxl_policy,
                $enquiry->cxl_due_date?->format('Y-m-d'),
                $this->exportMoney($enquiry->total_revenue),
                $enquiry->remarks,
                $enquiry->assignedTo?->name,
            ];
        })->all();

        return [$headers, $rows, 'enquiries-report-'.now()->format('Y-m-d').'.xlsx'];
    }

    /**
     * @return array{0: list<string>, 1: list<list<mixed>>, 2: string}
     */
    private function bookingsExport($results, bool $cancelled): array
    {
        $headers = [
            'Hotel',
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

        if ($cancelled) {
            $headers[] = 'Cancellation Reason';
        }

        $headers[] = 'Created by';

        $rows = $results->map(function (Enquiry $enquiry) use ($cancelled) {
            $status = str_replace(['_', '-'], ' ', (string) $enquiry->status);

            $row = [
                $enquiry->hotel?->name,
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

            if ($cancelled) {
                $row[] = $enquiry->cancellation_reason;
            }

            $row[] = $enquiry->assignedTo?->name;

            return $row;
        })->all();

        $filename = ($cancelled ? 'cancelled-bookings' : 'group-bookings').'-report-'.now()->format('Y-m-d').'.xlsx';

        return [$headers, $rows, $filename];
    }

    private function exportMoney(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return round((float) $value, 2);
    }
}
