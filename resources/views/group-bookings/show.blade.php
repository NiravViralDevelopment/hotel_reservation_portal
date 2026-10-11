@extends('layouts.app')

@section('title', $enquiry->block_id ?: 'Group booking')
@section('page', ! empty($cancelledContext) ? 'cancelled-bookings' : 'group-bookings')

@push('styles')
<style>
  .gb-show .gb-section-title {
    font-size: 0.9375rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .gb-show .gb-section-title i { color: var(--brand-accent); }
  .gb-show .gb-dl {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.85rem 1.25rem;
  }
  @media (max-width: 575.98px) {
    .gb-show .gb-dl { grid-template-columns: 1fr; }
  }
  .gb-show .gb-label {
    font-size: 0.6875rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--text-muted);
    margin-bottom: 0.2rem;
  }
  .gb-show .gb-value { font-weight: 600; word-break: break-word; }
</style>
@endpush

@section('content')
@php
  $text = fn ($value) => filled($value) ? $value : '—';
  $money = function ($value) {
      if ($value === null || $value === '') {
          return '—';
      }

      return '£'.number_format((float) $value, 2);
  };
  $date = fn ($value) => $value?->format('d M Y') ?? '—';
@endphp

<div class="gb-show">
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ ! empty($cancelledContext) ? route('cancelled-bookings.index') : route('group-bookings.index') }}">{{ ! empty($cancelledContext) ? 'Cancelled Bookings' : 'Confirm Group Bookings' }}</a></li>
          <li class="breadcrumb-item active">{{ $enquiry->block_id ?: ($enquiry->group_name ?: 'Booking') }}</li>
        </ol>
      </nav>
      <h1 class="page-title mb-2">{{ $enquiry->group_name ?: 'Group booking' }}</h1>
      <div class="d-flex flex-wrap align-items-center gap-2">
        <span class="text-secondary">{{ $enquiry->block_id ?: 'No block ID' }}</span>
        <x-badge-status :status="$enquiry->status" />
      </div>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ ! empty($cancelledContext) ? route('cancelled-bookings.index') : route('group-bookings.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
      @if (empty($cancelledContext))
        <a href="{{ route('group-bookings.contract', $enquiry) }}" class="btn btn-outline-primary btn-sm">
          <i class="bi bi-file-earmark-pdf"></i> Contract
        </a>
      @endif
      @can('update', $enquiry)
        @if (! $enquiry->is_cancel)
          <a href="{{ route('group-bookings.edit', $enquiry) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil"></i> Edit</a>
          <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelGroupBookingModal">
            <i class="bi bi-x-circle"></i> Cancel
          </button>
        @endif
      @endcan
    </div>
  </div>

  @if ($enquiry->is_cancel)
    <div class="alert alert-warning mb-3">
      <div class="fw-semibold mb-1">Cancellation reason</div>
      <div class="mb-0" style="white-space: pre-wrap;">{{ $enquiry->cancellation_reason ?: '—' }}</div>
    </div>
  @endif

  <div class="card mb-3">
    <div class="card-header"><h2 class="gb-section-title"><i class="bi bi-calendar3"></i> Stay</h2></div>
    <div class="card-body">
      <div class="gb-dl">
        <div><div class="gb-label">Date of Arrival</div><div class="gb-value">{{ $date($enquiry->check_in) }}</div></div>
        <div><div class="gb-label">Date of Departure</div><div class="gb-value">{{ $date($enquiry->check_out) }}</div></div>
        <div><div class="gb-label">Day</div><div class="gb-value">{{ $text($enquiry->day) }}</div></div>
        <div><div class="gb-label">No. of Nights</div><div class="gb-value">{{ $enquiry->nights ?? '—' }}</div></div>
        <div><div class="gb-label">Block ID</div><div class="gb-value">{{ $text($enquiry->block_id) }}</div></div>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header"><h2 class="gb-section-title"><i class="bi bi-person-vcard"></i> Client</h2></div>
    <div class="card-body">
      <div class="gb-dl">
        <div><div class="gb-label">Client</div><div class="gb-value">{{ $text($enquiry->client) }}</div></div>
        <div><div class="gb-label">Contact</div><div class="gb-value">{{ $text($enquiry->contact_name) }}</div></div>
        <div><div class="gb-label">Email ID</div><div class="gb-value">{{ $text($enquiry->email) }}</div></div>
        <div><div class="gb-label">Status</div><div class="gb-value"><x-badge-status :status="$enquiry->status" /></div></div>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header"><h2 class="gb-section-title"><i class="bi bi-file-earmark-text"></i> Contract and payment</h2></div>
    <div class="card-body">
      <div class="gb-dl">
        <div><div class="gb-label">Contract Sent On</div><div class="gb-value">{{ $date($enquiry->contract_sent_on) }}</div></div>
        <div><div class="gb-label">Contract Recd On</div><div class="gb-value">{{ $date($enquiry->contract_received_on) }}</div></div>
        <div><div class="gb-label">Saved to Doc</div><div class="gb-value">{{ $text($enquiry->saved_to_doc) }}</div></div>
        <div>
          <div class="gb-label">Hotel / booking contract</div>
          <div class="gb-value">
            <a href="{{ route('group-bookings.contract', $enquiry) }}">Open contract workspace</a>
            @if ($enquiry->hasBookingContract())
              <div class="small text-secondary mt-1">
                Booking copy:
                <a href="{{ route('group-bookings.contract.download', $enquiry) }}">{{ $enquiry->booking_contract_original_name }}</a>
              </div>
            @endif
          </div>
        </div>
        <div><div class="gb-label">Payment Term</div><div class="gb-value">@if ($enquiry->payment_term){{ $enquiry->payment_term }}@if ($enquiry->payment_term_days !== null) ({{ $enquiry->payment_term_days }} {{ (int) $enquiry->payment_term_days === 1 ? 'day' : 'days' }})@endif@else—@endif</div></div>
        <div><div class="gb-label">Due Date</div><div class="gb-value">{{ $date($enquiry->payment_due_date) }}</div></div>
        <div><div class="gb-label">Payment Status</div><div class="gb-value">{{ $text($enquiry->payment_status) }}</div></div>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header"><h2 class="gb-section-title"><i class="bi bi-shield-exclamation"></i> Cancellation and commission</h2></div>
    <div class="card-body">
      <div class="gb-dl">
        <div><div class="gb-label">CXL Policy</div><div class="gb-value">{{ $text($enquiry->cxl_policy) }}</div></div>
        <div><div class="gb-label">CXL Due Date</div><div class="gb-value">{{ $date($enquiry->cxl_due_date) }}</div></div>
        @if ($enquiry->is_cancel)
          <div><div class="gb-label">CXL Date</div><div class="gb-value">{{ $date($enquiry->cxl_date) }}</div></div>
        @endif
        <div><div class="gb-label">Commission</div><div class="gb-value">{{ $enquiry->has_commission === null ? '—' : ($enquiry->has_commission ? 'Yes' : 'No') }}</div></div>
        @if ($enquiry->has_commission)
          <div><div class="gb-label">Commission %</div><div class="gb-value">{{ $enquiry->commission_percentage === null ? '—' : rtrim(rtrim(number_format((float) $enquiry->commission_percentage, 2), '0'), '.').'%' }}</div></div>
        @endif
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header"><h2 class="gb-section-title"><i class="bi bi-door-open"></i> Rooms and revenue</h2></div>
    <div class="card-body">
      <div class="gb-dl">
        <div><div class="gb-label">Single RNs</div><div class="gb-value">{{ $enquiry->single_rooms ?? '—' }}</div></div>
        <div><div class="gb-label">Single Gross Rate</div><div class="gb-value">{{ $money($enquiry->single_rate) }}</div></div>
        <div><div class="gb-label">Double RNs</div><div class="gb-value">{{ $enquiry->double_rooms ?? '—' }}</div></div>
        <div><div class="gb-label">Double Gross Rate</div><div class="gb-value">{{ $money($enquiry->double_rate) }}</div></div>
        <div><div class="gb-label">Triple RNs</div><div class="gb-value">{{ $enquiry->triple_rooms ?? '—' }}</div></div>
        <div><div class="gb-label">Triple Gross Rate</div><div class="gb-value">{{ $money($enquiry->triple_rate) }}</div></div>
        <div><div class="gb-label">Total RNs</div><div class="gb-value">{{ $enquiry->total_rns ?? '—' }}</div></div>
        <div><div class="gb-label">Total Rev</div><div class="gb-value">{{ $money($enquiry->total_revenue) }}</div></div>
        <div><div class="gb-label">BB Revenue (Nett £10)</div><div class="gb-value">{{ $money($enquiry->bb_revenue) }}</div></div>
        <div><div class="gb-label">Dinner Revenue (Nett £)</div><div class="gb-value">{{ $money($enquiry->dinner_revenue) }}</div></div>
        <div><div class="gb-label">Nett Rev EX VAT &amp; BF</div><div class="gb-value">{{ $money($enquiry->nett_rev_ex_vat) }}</div></div>
        <div><div class="gb-label">BB/DBB</div><div class="gb-value">{{ $text($enquiry->basis) }}</div></div>
      </div>
      @php
        $staySegments = is_array($enquiry->stay_segments) ? array_values($enquiry->stay_segments) : [];
        $showSegmentBreakfast = in_array($enquiry->basis, ['BB', 'DBB'], true);
        $showSegmentDinner = $enquiry->basis === 'DBB';
        $segmentBreakfast = (float) ($enquiry->breakfast_rate ?? 0);
        $segmentDinner = (float) ($enquiry->dinner_rate ?? 0);
      @endphp
      @if ($staySegments !== [])
        <div class="table-responsive mt-3">
          <table class="table table-sm table-hover mb-0">
            <thead>
              <tr>
                <th>Arrival</th>
                <th>Departure</th>
                <th>Day</th>
                <th class="text-end">Nights</th>
                <th class="text-end">Single</th>
                <th class="text-end">Rate</th>
                <th class="text-end">Double</th>
                <th class="text-end">Rate</th>
                <th class="text-end">Triple</th>
                <th class="text-end">Rate</th>
                <th class="text-end">Revenue</th>
                @if ($showSegmentBreakfast)
                  <th class="text-end">BB</th>
                @endif
                @if ($showSegmentDinner)
                  <th class="text-end">Dinner</th>
                @endif
              </tr>
            </thead>
            <tbody>
              @php
                $segmentNightsTotal = 0;
                $segmentRevenueTotal = 0.0;
                $segmentBbTotal = 0.0;
                $segmentDinnerTotal = 0.0;
              @endphp
              @foreach ($staySegments as $segment)
                @php
                  $segmentNights = (int) ($segment['nights'] ?? 0);
                  $segmentSingle = (int) ($segment['single_rooms'] ?? 0);
                  $segmentSingleRate = (float) ($segment['single_rate'] ?? 0);
                  $segmentDouble = (int) ($segment['double_rooms'] ?? 0);
                  $segmentDoubleRate = (float) ($segment['double_rate'] ?? 0);
                  $segmentTriple = (int) ($segment['triple_rooms'] ?? 0);
                  $segmentTripleRate = (float) ($segment['triple_rate'] ?? 0);
                  $segmentRevenue = $segmentNights * (($segmentSingle * $segmentSingleRate) + ($segmentDouble * $segmentDoubleRate) + ($segmentTriple * $segmentTripleRate));
                  $segmentCovers = $segmentNights * (($segmentSingle * 1) + ($segmentDouble * 2) + ($segmentTriple * 3));
                  $segmentBb = $segmentCovers * $segmentBreakfast;
                  $segmentDinnerAmount = $segmentCovers * $segmentDinner;
                  $segmentNightsTotal += $segmentNights;
                  $segmentRevenueTotal += $segmentRevenue;
                  $segmentBbTotal += $segmentBb;
                  $segmentDinnerTotal += $segmentDinnerAmount;
                @endphp
                <tr>
                  <td class="text-nowrap">{{ \Illuminate\Support\Carbon::parse($segment['check_in'])->format('d-m-Y') }}</td>
                  <td class="text-nowrap">{{ \Illuminate\Support\Carbon::parse($segment['check_out'])->format('d-m-Y') }}</td>
                  <td>{{ $segment['day'] ?? '—' }}</td>
                  <td class="text-end">{{ $segmentNights }}</td>
                  <td class="text-end">{{ $segmentSingle }}</td>
                  <td class="text-end">{{ $money($segmentSingleRate) }}</td>
                  <td class="text-end">{{ $segmentDouble }}</td>
                  <td class="text-end">{{ $money($segmentDoubleRate) }}</td>
                  <td class="text-end">{{ $segmentTriple }}</td>
                  <td class="text-end">{{ $money($segmentTripleRate) }}</td>
                  <td class="text-end fw-semibold">{{ $money($segmentRevenue) }}</td>
                  @if ($showSegmentBreakfast)
                    <td class="text-end">{{ $money($segmentBb) }}</td>
                  @endif
                  @if ($showSegmentDinner)
                    <td class="text-end">{{ $money($segmentDinnerAmount) }}</td>
                  @endif
                </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <th scope="row" colspan="3">Total</th>
                <td class="text-end fw-semibold">{{ $segmentNightsTotal }}</td>
                <td colspan="6"></td>
                <td class="text-end fw-semibold">{{ $money($segmentRevenueTotal) }}</td>
                @if ($showSegmentBreakfast)
                  <td class="text-end fw-semibold">{{ $money($segmentBbTotal) }}</td>
                @endif
                @if ($showSegmentDinner)
                  <td class="text-end fw-semibold">{{ $money($segmentDinnerTotal) }}</td>
                @endif
              </tr>
            </tfoot>
          </table>
        </div>
      @endif
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header"><h2 class="gb-section-title"><i class="bi bi-receipt"></i> Rooming and invoice</h2></div>
    <div class="card-body">
      <div class="gb-dl">
        <div><div class="gb-label">Update</div><div class="gb-value">{{ $text($enquiry->booking_update) }}</div></div>
        <div><div class="gb-label">Rooming</div><div class="gb-value">{{ $text($enquiry->rooming) }}</div></div>
        <div><div class="gb-label">Invoice Status</div><div class="gb-value">{{ $text($enquiry->invoice_status) }}</div></div>
        <div><div class="gb-label">Invoice Number</div><div class="gb-value">{{ $text($enquiry->invoice_number) }}</div></div>
        <div><div class="gb-label">Invoice Sent On</div><div class="gb-value">{{ $date($enquiry->invoice_sent_on) }}</div></div>
        <div><div class="gb-label">Invoice Amount</div><div class="gb-value">{{ $money($enquiry->invoice_amount) }}</div></div>
        @if ($enquiry->has_commission)
          <div><div class="gb-label">Commission Payable Status</div><div class="gb-value">{{ $text($enquiry->commission_payable_status) }}</div></div>
        @endif
      </div>
    </div>
  </div>
</div>

@if (! $enquiry->is_cancel)
  @can('update', $enquiry)
    <div class="modal fade" id="cancelGroupBookingModal" tabindex="-1" aria-labelledby="cancelGroupBookingModalTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form method="POST" action="{{ route('enquiries.cancel-booking', $enquiry) }}">
            @csrf
            <input type="hidden" name="cancel_scope" value="group">
            <div class="modal-header">
              <h5 class="modal-title" id="cancelGroupBookingModalTitle">Cancel group booking</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <p class="text-secondary">Cancel this group booking? The status will be set to Cancelled and it will move to Cancelled Bookings.</p>
              <label for="booking_cancellation_reason" class="form-label">Cancellation reason <span class="text-danger">*</span></label>
              <textarea name="cancellation_reason" id="booking_cancellation_reason" rows="4" class="form-control @error('cancellation_reason') is-invalid @enderror" required maxlength="2000" placeholder="Enter the cancellation reason">{{ old('cancellation_reason') }}</textarea>
              @error('cancellation_reason')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Back</button>
              <button type="submit" class="btn btn-danger">Cancel group booking</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endcan
@endif
@endsection

@if ($errors->has('cancellation_reason'))
  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var modalEl = document.getElementById('cancelGroupBookingModal');
        if (modalEl && window.bootstrap) {
          window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
      });
    </script>
  @endpush
@endif
