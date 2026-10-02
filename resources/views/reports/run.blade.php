@extends('layouts.app')

@section('title', 'Report results')
@section('page', 'reports')

@section('content')
  @php
    $reportLabels = [
      'group_bookings' => 'Group bookings',
      'enquiries' => 'Enquiries',
      'cancelled_bookings' => 'Cancelled bookings',
      'bookings_by_hotel' => 'Bookings by hotel',
      'arrivals_summary' => 'Arrivals summary',
      'revenue_by_agency' => 'Revenue by agency',
    ];
    $title = $reportLabels[$validated['report']] ?? 'Report';
  @endphp
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
          <li class="breadcrumb-item active">Results</li>
        </ol>
      </nav>
      <h1 class="page-title">{{ $title }}</h1>
      <p class="page-subtitle">
        @if (!empty($validated['date_from']) && !empty($validated['date_to']))
          {{ \Illuminate\Support\Carbon::parse($validated['date_from'])->format('d M Y') }}
          —
          {{ \Illuminate\Support\Carbon::parse($validated['date_to'])->format('d M Y') }}
          · {{ $results->count() }} {{ \Illuminate\Support\Str::plural('record', $results->count()) }}
        @elseif (!empty($validated['date_from']) || !empty($validated['date_to']))
          {{ $validated['date_from'] ?? '…' }} — {{ $validated['date_to'] ?? '…' }}
        @else
          All dates
        @endif
      </p>
    </div>
    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">All reports</a>
  </div>

  @php
    $reportSlugs = [
      'group_bookings' => 'group-bookings',
      'enquiries' => 'enquiries',
      'cancelled_bookings' => 'cancelled-bookings',
    ];
  @endphp
  @if (isset($reportSlugs[$validated['report']]))
    <div class="card mb-4">
      <div class="card-body">
        <form method="GET" action="{{ route('reports.module', $reportSlugs[$validated['report']]) }}" class="row g-3 align-items-end">
          <div class="col-md-4">
            <label for="date_from" class="form-label">Start date</label>
            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $validated['date_from'] }}" required>
          </div>
          <div class="col-md-4">
            <label for="date_to" class="form-label">End date</label>
            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $validated['date_to'] }}" required>
          </div>
          <div class="col-md-4">
            <button type="submit" class="btn btn-accent btn-sm">Update report</button>
          </div>
        </form>
      </div>
    </div>
  @endif

  <div class="card">
    <div class="table-scroll-hint"><i class="bi bi-arrows-expand"></i> Scroll horizontally to see all columns</div>
    <div class="table-wrapper table-scroll-wide table-scroll-enquiries">
      @if ($validated['report'] === 'group_bookings')
        <table class="table table-hover table-sm mb-0" style="font-size:0.78rem">
          <thead>
            <tr>
              <th>Block ID</th>
              <th>Group</th>
              <th>Client</th>
              <th>Email</th>
              <th>Hotel</th>
              <th>Company</th>
              <th>Agency</th>
              <th>Contact</th>
              <th>Arrival</th>
              <th>Arrival day</th>
              <th>Departure</th>
              <th>Nights</th>
              <th>Rooms</th>
              <th>Pax</th>
              <th>Single RNs</th>
              <th>Single rate</th>
              <th>Double RNs</th>
              <th>Double rate</th>
              <th>Triple RNs</th>
              <th>Triple rate</th>
              <th>Total RNs</th>
              <th>Revenue</th>
              <th>BB revenue</th>
              <th>Dinner revenue</th>
              <th>Nett rev</th>
              <th>Commission</th>
              <th>City tax</th>
              <th>Meal plan</th>
              <th>Payment term</th>
              <th>Due date</th>
              <th>Payment status</th>
              <th>CXL policy</th>
              <th>CXL due</th>
              <th>CXL date</th>
              <th>Contract sent</th>
              <th>Contract recd</th>
              <th>Saved doc</th>
              <th>Rooming</th>
              <th>Invoice status</th>
              <th>Invoice date</th>
              <th>Invoice amount</th>
              <th>Commission payable</th>
              <th>Opera check</th>
              <th>Update notes</th>
              <th>Internal notes</th>
              <th>Status</th>
              <th>Created by</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($results as $booking)
              <tr>
                <td class="fw-semibold text-nowrap"><a href="{{ route('group-bookings.show', $booking) }}">{{ $booking->block_id }}</a></td>
                <td class="text-nowrap">{{ $booking->group_name }}</td>
                <td class="text-nowrap">{{ $booking->client ?: '—' }}</td>
                <td class="text-nowrap">{{ $booking->email ?: '—' }}</td>
                <td class="text-nowrap">{{ $booking->hotel?->code ?? '—' }}</td>
                <td class="text-nowrap">{{ $booking->company?->name ?? '—' }}</td>
                <td class="text-nowrap">{{ $booking->travelAgency?->name ?? ($booking->agency_name ?: '—') }}</td>
                <td class="text-nowrap">{{ $booking->contact?->name ?? ($booking->contact_name ?: '—') }}</td>
                <td class="text-nowrap">{{ $booking->arrival?->format('d M Y') ?? '—' }}</td>
                <td class="text-nowrap">{{ $booking->arrival_day ?: '—' }}</td>
                <td class="text-nowrap">{{ $booking->departure?->format('d M Y') ?? '—' }}</td>
                <td>{{ $booking->nights ?? '—' }}</td>
                <td>{{ $booking->rooms ?? '—' }}</td>
                <td>{{ $booking->pax ?? '—' }}</td>
                <td>{{ $booking->single_rns ?? 0 }}</td>
                <td>£{{ number_format((float) ($booking->single_rate ?? 0), 2) }}</td>
                <td>{{ $booking->double_rns ?? 0 }}</td>
                <td>£{{ number_format((float) ($booking->double_rate ?? 0), 2) }}</td>
                <td>{{ $booking->triple_rns ?? 0 }}</td>
                <td>£{{ number_format((float) ($booking->triple_rate ?? 0), 2) }}</td>
                <td>{{ $booking->total_rns ?? 0 }}</td>
                <td class="text-nowrap">£{{ number_format((float) ($booking->revenue ?? 0), 2) }}</td>
                <td class="text-nowrap">£{{ number_format((float) ($booking->bb_revenue ?? 0), 2) }}</td>
                <td class="text-nowrap">£{{ number_format((float) ($booking->dinner_revenue ?? 0), 2) }}</td>
                <td class="text-nowrap">£{{ number_format((float) ($booking->nett_rev ?? 0), 2) }}</td>
                <td class="text-nowrap">£{{ number_format((float) ($booking->commission ?? 0), 2) }}</td>
                <td class="text-nowrap">£{{ number_format((float) ($booking->city_tax ?? 0), 2) }}</td>
                <td class="text-nowrap">{{ $booking->meal_plan ?: '—' }}</td>
                <td class="text-nowrap">{{ $booking->payment_term ?: '—' }}</td>
                <td class="text-nowrap">{{ $booking->due_date?->format('d M Y') ?? '—' }}</td>
                <td class="text-nowrap">{{ $booking->payment_status ?: ($booking->payment_status_display?->value ?? '—') }}</td>
                <td class="text-nowrap">{{ $booking->cxl_policy ?: '—' }}</td>
                <td class="text-nowrap">{{ $booking->cxl_due_date?->format('d M Y') ?? '—' }}</td>
                <td class="text-nowrap">{{ $booking->cxl_date?->format('d M Y') ?? '—' }}</td>
                <td class="text-nowrap">{{ $booking->contract_sent?->format('d M Y') ?? '—' }}</td>
                <td class="text-nowrap">{{ $booking->contract_recd?->format('d M Y') ?? '—' }}</td>
                <td class="text-nowrap">{{ $booking->saved_doc ?: '—' }}</td>
                <td class="text-nowrap">{{ $booking->rooming_status ?: '—' }}</td>
                <td class="text-nowrap">{{ $booking->invoice_status ?: '—' }}</td>
                <td class="text-nowrap">{{ $booking->invoice_date?->format('d M Y') ?? '—' }}</td>
                <td class="text-nowrap">£{{ number_format((float) ($booking->invoice_amount ?? 0), 2) }}</td>
                <td class="text-nowrap">£{{ number_format((float) ($booking->commission_payable ?? 0), 2) }}</td>
                <td class="text-nowrap">{{ $booking->opera_cross_check ?: '—' }}</td>
                <td style="max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $booking->update_notes }}">{{ $booking->update_notes ?: '—' }}</td>
                <td style="max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $booking->internal_notes }}">{{ $booking->internal_notes ?: '—' }}</td>
                <td><x-badge-status :status="$booking->status" /></td>
                <td class="text-nowrap">{{ $booking->createdBy?->name ?? '—' }}</td>
              </tr>
            @empty
              <tr><td colspan="47" class="text-center text-secondary py-4">No group bookings in these dates.</td></tr>
            @endforelse
          </tbody>
        </table>
      @elseif ($validated['report'] === 'enquiries')
        <table class="table table-hover table-sm mb-0" style="font-size:0.78rem">
          <thead>
            <tr>
              <th>Ref</th>
              <th>Year</th>
              <th>Date</th>
              <th>Day</th>
              <th>Response date</th>
              <th>Group name</th>
              <th>Email</th>
              <th>Agency</th>
              <th>Hotel</th>
              <th>Check-in</th>
              <th>Check-in day</th>
              <th>Check-out</th>
              <th>Nights</th>
              <th>Rooms / night</th>
              <th>Single</th>
              <th>Single rate</th>
              <th>Double</th>
              <th>Double rate</th>
              <th>Triple</th>
              <th>Triple rate</th>
              <th>Basis</th>
              <th>Total revenue</th>
              <th>Tax</th>
              <th>Tax %</th>
              <th>Tax revenue</th>
              <th>CXL policy</th>
              <th>Option date</th>
              <th>Remarks</th>
              <th>Status</th>
              <th>Assigned to</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($results as $enquiry)
              <tr>
                <td class="fw-semibold text-nowrap"><a href="{{ route('enquiries.show', $enquiry) }}">{{ $enquiry->ref }}</a></td>
                <td>{{ $enquiry->year ?? '—' }}</td>
                <td class="text-nowrap">{{ $enquiry->enquiry_date?->format('d M Y') ?? '—' }}</td>
                <td>{{ $enquiry->day ?? '—' }}</td>
                <td class="text-nowrap">
                  @if ($enquiry->response_date)
                    {{ $enquiry->response_date->format('d M Y') }}
                  @else
                    <span class="text-secondary">Awaiting</span>
                  @endif
                </td>
                <td class="text-nowrap">{{ $enquiry->group_name }}</td>
                <td class="text-nowrap">{{ $enquiry->email ?: '—' }}</td>
                <td class="text-nowrap">{{ $enquiry->travelAgency?->name ?? '—' }}</td>
                <td class="text-nowrap">{{ $enquiry->hotel?->code ?? '—' }}</td>
                <td class="text-nowrap">{{ $enquiry->check_in?->format('d M Y') ?? '—' }}</td>
                <td class="text-nowrap">{{ $enquiry->check_in_day ?: '—' }}</td>
                <td class="text-nowrap">{{ $enquiry->check_out?->format('d M Y') ?? '—' }}</td>
                <td>{{ $enquiry->nights ?? '—' }}</td>
                <td>{{ $enquiry->rooms_per_night ?? '—' }}</td>
                <td>{{ $enquiry->single_rooms ?? 0 }}</td>
                <td>£{{ number_format((float) ($enquiry->single_rate ?? 0), 2) }}</td>
                <td>{{ $enquiry->double_rooms ?? 0 }}</td>
                <td>£{{ number_format((float) ($enquiry->double_rate ?? 0), 2) }}</td>
                <td>{{ $enquiry->triple_rooms ?? 0 }}</td>
                <td>£{{ number_format((float) ($enquiry->triple_rate ?? 0), 2) }}</td>
                <td>{{ $enquiry->basis ?: '—' }}</td>
                <td class="text-nowrap">£{{ number_format((float) ($enquiry->total_revenue ?? 0), 2) }}</td>
                <td>{{ $enquiry->has_tax ? 'Yes' : 'No' }}</td>
                <td>{{ $enquiry->has_tax ? number_format((float) ($enquiry->tax_percentage ?? 0), 2).'%' : '—' }}</td>
                <td class="text-nowrap">£{{ number_format((float) ($enquiry->tax_revenue ?? 0), 2) }}</td>
                <td class="text-nowrap">{{ $enquiry->cxl_policy ?: '—' }}</td>
                <td class="text-nowrap">{{ $enquiry->option_date?->format('d M Y') ?? '—' }}</td>
                <td style="max-width:220px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $enquiry->remarks }}">{{ $enquiry->remarks ?: '—' }}</td>
                <td><x-badge-status :status="$enquiry->status" /></td>
                <td class="text-nowrap">{{ $enquiry->assignedTo?->name ?? '—' }}</td>
              </tr>
            @empty
              <tr><td colspan="30" class="text-center text-secondary py-4">No enquiries in these dates.</td></tr>
            @endforelse
          </tbody>
        </table>
      @elseif ($validated['report'] === 'cancelled_bookings')
        <table class="table table-hover table-sm mb-0" style="font-size:0.78rem">
          <thead>
            <tr>
              <th>Block ID</th>
              <th>Group</th>
              <th>Client</th>
              <th>Email</th>
              <th>Hotel</th>
              <th>Company</th>
              <th>Agency</th>
              <th>Contact</th>
              <th>Arrival</th>
              <th>Arrival day</th>
              <th>Departure</th>
              <th>Nights</th>
              <th>Rooms</th>
              <th>Pax</th>
              <th>Single RNs</th>
              <th>Single rate</th>
              <th>Double RNs</th>
              <th>Double rate</th>
              <th>Triple RNs</th>
              <th>Triple rate</th>
              <th>Total RNs</th>
              <th>Revenue</th>
              <th>BB revenue</th>
              <th>Dinner revenue</th>
              <th>Nett rev</th>
              <th>Commission</th>
              <th>City tax</th>
              <th>Meal plan</th>
              <th>Payment term</th>
              <th>Due date</th>
              <th>Payment status</th>
              <th>CXL policy</th>
              <th>CXL due</th>
              <th>CXL date</th>
              <th>Contract sent</th>
              <th>Contract recd</th>
              <th>Saved doc</th>
              <th>Rooming</th>
              <th>Invoice status</th>
              <th>Invoice date</th>
              <th>Invoice amount</th>
              <th>Commission payable</th>
              <th>Opera check</th>
              <th>Update notes</th>
              <th>Internal notes</th>
              <th>Cancelled</th>
              <th>Revenue lost</th>
              <th>Reason</th>
              <th>Status</th>
              <th>Created by</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($results as $booking)
              <tr>
                <td class="fw-semibold text-nowrap"><a href="{{ route('group-bookings.show', $booking) }}">{{ $booking->block_id }}</a></td>
                <td class="text-nowrap">{{ $booking->group_name }}</td>
                <td class="text-nowrap">{{ $booking->client ?: '—' }}</td>
                <td class="text-nowrap">{{ $booking->email ?: '—' }}</td>
                <td class="text-nowrap">{{ $booking->hotel?->code ?? '—' }}</td>
                <td class="text-nowrap">{{ $booking->company?->name ?? '—' }}</td>
                <td class="text-nowrap">{{ $booking->travelAgency?->name ?? ($booking->agency_name ?: '—') }}</td>
                <td class="text-nowrap">{{ $booking->contact?->name ?? ($booking->contact_name ?: '—') }}</td>
                <td class="text-nowrap">{{ $booking->arrival?->format('d M Y') ?? '—' }}</td>
                <td class="text-nowrap">{{ $booking->arrival_day ?: '—' }}</td>
                <td class="text-nowrap">{{ $booking->departure?->format('d M Y') ?? '—' }}</td>
                <td>{{ $booking->nights ?? '—' }}</td>
                <td>{{ $booking->rooms ?? '—' }}</td>
                <td>{{ $booking->pax ?? '—' }}</td>
                <td>{{ $booking->single_rns ?? 0 }}</td>
                <td>£{{ number_format((float) ($booking->single_rate ?? 0), 2) }}</td>
                <td>{{ $booking->double_rns ?? 0 }}</td>
                <td>£{{ number_format((float) ($booking->double_rate ?? 0), 2) }}</td>
                <td>{{ $booking->triple_rns ?? 0 }}</td>
                <td>£{{ number_format((float) ($booking->triple_rate ?? 0), 2) }}</td>
                <td>{{ $booking->total_rns ?? 0 }}</td>
                <td class="text-nowrap">£{{ number_format((float) ($booking->revenue ?? 0), 2) }}</td>
                <td class="text-nowrap">£{{ number_format((float) ($booking->bb_revenue ?? 0), 2) }}</td>
                <td class="text-nowrap">£{{ number_format((float) ($booking->dinner_revenue ?? 0), 2) }}</td>
                <td class="text-nowrap">£{{ number_format((float) ($booking->nett_rev ?? 0), 2) }}</td>
                <td class="text-nowrap">£{{ number_format((float) ($booking->commission ?? 0), 2) }}</td>
                <td class="text-nowrap">£{{ number_format((float) ($booking->city_tax ?? 0), 2) }}</td>
                <td class="text-nowrap">{{ $booking->meal_plan ?: '—' }}</td>
                <td class="text-nowrap">{{ $booking->payment_term ?: '—' }}</td>
                <td class="text-nowrap">{{ $booking->due_date?->format('d M Y') ?? '—' }}</td>
                <td class="text-nowrap">{{ $booking->payment_status ?: ($booking->payment_status_display?->value ?? '—') }}</td>
                <td class="text-nowrap">{{ $booking->cxl_policy ?: '—' }}</td>
                <td class="text-nowrap">{{ $booking->cxl_due_date?->format('d M Y') ?? '—' }}</td>
                <td class="text-nowrap">{{ $booking->cxl_date?->format('d M Y') ?? '—' }}</td>
                <td class="text-nowrap">{{ $booking->contract_sent?->format('d M Y') ?? '—' }}</td>
                <td class="text-nowrap">{{ $booking->contract_recd?->format('d M Y') ?? '—' }}</td>
                <td class="text-nowrap">{{ $booking->saved_doc ?: '—' }}</td>
                <td class="text-nowrap">{{ $booking->rooming_status ?: '—' }}</td>
                <td class="text-nowrap">{{ $booking->invoice_status ?: '—' }}</td>
                <td class="text-nowrap">{{ $booking->invoice_date?->format('d M Y') ?? '—' }}</td>
                <td class="text-nowrap">£{{ number_format((float) ($booking->invoice_amount ?? 0), 2) }}</td>
                <td class="text-nowrap">£{{ number_format((float) ($booking->commission_payable ?? 0), 2) }}</td>
                <td class="text-nowrap">{{ $booking->opera_cross_check ?: '—' }}</td>
                <td style="max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $booking->update_notes }}">{{ $booking->update_notes ?: '—' }}</td>
                <td style="max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $booking->internal_notes }}">{{ $booking->internal_notes ?: '—' }}</td>
                <td class="text-nowrap">{{ $booking->cancelled_at?->format('d M Y') ?? $booking->cxl_date?->format('d M Y') ?? '—' }}</td>
                <td class="text-nowrap">£{{ number_format((float) ($booking->revenue_lost ?? 0), 2) }}</td>
                <td style="max-width:220px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $booking->cancellation_reason }}">{{ $booking->cancellation_reason ?: '—' }}</td>
                <td><x-badge-status :status="$booking->status" /></td>
                <td class="text-nowrap">{{ $booking->createdBy?->name ?? '—' }}</td>
              </tr>
            @empty
              <tr><td colspan="50" class="text-center text-secondary py-4">No cancelled bookings in these dates.</td></tr>
            @endforelse
          </tbody>
        </table>
      @elseif ($validated['report'] === 'bookings_by_hotel')
        <table class="table table-hover mb-0">
          <thead><tr><th>Hotel</th><th class="text-end">Bookings</th><th class="text-end">Revenue</th></tr></thead>
          <tbody>
            @forelse ($results as $row)
              @php $hotel = $hotels->firstWhere('id', $row->hotel_id); @endphp
              <tr>
                <td>{{ $hotel?->name ?? 'Hotel #'.$row->hotel_id }}</td>
                <td class="text-end">{{ $row->bookings }}</td>
                <td class="text-end">£{{ number_format((float) ($row->revenue ?? 0), 2) }}</td>
              </tr>
            @empty
              <tr><td colspan="3" class="text-center text-secondary py-4">No data.</td></tr>
            @endforelse
          </tbody>
        </table>
      @elseif ($validated['report'] === 'revenue_by_agency')
        <table class="table table-hover mb-0">
          <thead><tr><th>Agency ID</th><th class="text-end">Bookings</th><th class="text-end">Revenue</th></tr></thead>
          <tbody>
            @forelse ($results as $row)
              <tr>
                <td>{{ $row->travel_agency_id ?? '—' }}</td>
                <td class="text-end">{{ $row->bookings }}</td>
                <td class="text-end">£{{ number_format((float) ($row->revenue ?? 0), 2) }}</td>
              </tr>
            @empty
              <tr><td colspan="3" class="text-center text-secondary py-4">No data.</td></tr>
            @endforelse
          </tbody>
        </table>
      @else
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>Block ID</th>
              <th>Group</th>
              <th>Hotel</th>
              <th>Agency</th>
              <th>Arrival</th>
              <th class="text-end">Revenue</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($results as $booking)
              <tr>
                <td><a href="{{ route('group-bookings.show', $booking) }}">{{ $booking->block_id }}</a></td>
                <td>{{ $booking->group_name }}</td>
                <td>{{ $booking->hotel?->code ?? '—' }}</td>
                <td>{{ $booking->travelAgency?->name ?? '—' }}</td>
                <td>{{ $booking->arrival?->format('d M Y') ?? '—' }}</td>
                <td class="text-end">£{{ number_format((float) ($booking->revenue ?? 0), 2) }}</td>
                <td><x-badge-status :status="$booking->status" /></td>
              </tr>
            @empty
              <tr><td colspan="7" class="text-center text-secondary py-4">No data.</td></tr>
            @endforelse
          </tbody>
        </table>
      @endif
    </div>
  </div>
@endsection
