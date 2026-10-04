<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Support\QuerySort;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CancelledInquiryController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Enquiry::class);

        $query = Enquiry::query()
            ->accessibleBy()
            ->cancelledInquiries();

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
            'updated_at' => 'updated_at',
        ], 'updated_at', 'desc');

        $enquiries = $query->paginate(10)->withQueryString();

        return view('cancelled-inquiries.index', compact('enquiries'));
    }
}
