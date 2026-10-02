@extends('layouts.app')

@section('title', 'Cancelled bookings')
@section('page', 'cancelled-bookings')

@section('content')
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Cancelled bookings</li>
      </ol>
    </nav>
    <h1 class="page-title">Cancelled bookings</h1>
    <p class="page-subtitle">Historical cancellations and revenue lost.</p>
  </div>

  <div class="card">
    <div class="table-toolbar">
      <form method="GET" action="{{ route('cancelled-bookings.index') }}" class="d-flex flex-wrap gap-2 align-items-center w-100">
        <select name="hotel_id" class="form-select form-select-sm select2" style="width:auto; min-width: 140px;">
          <option value="">All hotels</option>
          @foreach ($hotels as $hotel)
            <option value="{{ $hotel->id }}" @selected((string) request('hotel_id') === (string) $hotel->id)>{{ $hotel->name }}</option>
          @endforeach
        </select>
        <button type="submit" class="btn btn-outline-secondary btn-sm flex-shrink-0"><i class="bi bi-funnel"></i> Filter</button>
        @if (request()->filled('hotel_id'))
          <a href="{{ route('cancelled-bookings.index') }}" class="btn btn-outline-danger btn-sm flex-shrink-0">
            <i class="bi bi-x-circle"></i> Clear
          </a>
        @endif
        @if (request('sort'))
          <input type="hidden" name="sort" value="{{ request('sort') }}">
        @endif
        @if (request('dir'))
          <input type="hidden" name="dir" value="{{ request('dir') }}">
        @endif
      </form>
    </div>

    <div class="table-scroll-hint"><i class="bi bi-arrows-expand"></i> Scroll horizontally to see all columns</div>
    <div class="table-wrapper table-scroll-wide table-scroll-enquiries">
      <table class="table table-hover table-sm mb-0" style="font-size:0.78rem">
        <thead>
          <tr>
            <x-sortable-th column="block_id" label="Block ID" default="cancelled_at" default-dir="desc" />
            <x-sortable-th column="group_name" label="Group" default="cancelled_at" default-dir="desc" />
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
            <x-sortable-th column="cancelled_at" label="Cancelled" default="cancelled_at" default-dir="desc" />
            <x-sortable-th column="revenue" label="Revenue lost" default="cancelled_at" default-dir="desc" />
            <x-sortable-th column="reason" label="Reason" default="cancelled_at" default-dir="desc" />
            <th>Status</th>
            <th>Created by</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($bookings as $booking)
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
              <td class="text-end text-nowrap">
                @can('view', $booking)
                  <a href="{{ route('group-bookings.show', $booking) }}" class="btn btn-sm btn-outline-secondary" title="View">
                    <i class="bi bi-eye"></i>
                  </a>
                @endcan
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="50" class="text-center text-secondary py-4">No cancelled bookings.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @include('partials.pagination-footer', ['paginator' => $bookings])
  </div>
@endsection
