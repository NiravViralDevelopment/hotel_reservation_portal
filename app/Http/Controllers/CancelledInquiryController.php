<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\Hotel;
use App\Support\Audit;
use App\Support\EnquiryIndexFilters;
use App\Support\HotelAccess;
use App\Support\QuerySort;
use App\Support\SimpleXlsxWriter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CancelledInquiryController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Enquiry::class);

        $enquiries = $this->filteredQuery($request)->paginate(10)->withQueryString();
        $hotels = Hotel::optionsForSelect();
        [$dateFrom, $dateTo] = EnquiryIndexFilters::dateBounds($request);

        return view('cancelled-inquiries.index', compact('enquiries', 'hotels', 'dateFrom', 'dateTo'));
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Enquiry::class);

        $rows = $this->filteredQuery($request)->get()->map(function (Enquiry $enquiry) {
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
                $enquiry->cancellation_reason,
            ];
        })->all();

        Audit::log('exported', 'cancelled_inquiries', 'rows='.count($rows));

        return SimpleXlsxWriter::download(
            'cancelled-inquiries-'.now()->format('Y-m-d').'.xlsx',
            [
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
            ->cancelledInquiries();

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
                    ->orWhere('service_person', 'like', "%{$search}%")
                    ->orWhere('cancellation_reason', 'like', "%{$search}%");
            });
        }

        if ($request->filled('hotel_id')) {
            HotelAccess::ensure(null, $request->integer('hotel_id'));
            $query->where('hotel_id', $request->integer('hotel_id'));
        }

        EnquiryIndexFilters::applyDateRange($query, $request, 'enquiry_date');

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

    private function exportInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }
}
