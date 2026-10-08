@extends('layouts.app')

@section('title', 'Group booking')
@section('page', 'enquiries')

@push('styles')
  @include('enquiries.partials.entry-form-styles')
@endpush

@section('content')
@php
  $v = function (string $key, $fallback = '') use ($enquiry) {
      if (old($key) !== null) {
          return old($key);
      }
      $value = $enquiry->{$key} ?? null;
      if ($value instanceof \DateTimeInterface) {
          return $value->format('Y-m-d');
      }

      return $value === null || $value === '' ? $fallback : $value;
  };
  $contactValue = old('contact_name');
  if ($contactValue === null) {
      $contactValue = $enquiry->contact_name ?: ($enquiry->contact?->name ?? '');
  }
  $selectedBasis = old('basis');
  if ($selectedBasis === null) {
      $selectedBasis = in_array($enquiry->basis, ['BB', 'DBB'], true) ? $enquiry->basis : '';
  }
  $selectedDay = old('day', $enquiry->day);
@endphp

<div class="enquiry-form-layout">
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('enquiries.edit', $enquiry) }}">Edit enquiry</a></li>
        <li class="breadcrumb-item active">Group Bookings</li>
      </ol>
    </nav>
    <h1 class="page-title">Group Bookings</h1>
    <p class="page-subtitle mb-0">{{ $enquiry->group_name ?: 'Enquiry' }}. Submitting sets the status to Confirmed.</p>
  </div>

  <form method="POST" action="{{ route('enquiries.group-booking.store', $enquiry) }}" class="enquiry-form" id="group-booking-form">
    @csrf

    <div class="card mb-3">
      <div class="card-header">
        <h2 class="enquiry-section-title"><i class="bi bi-calendar3"></i> Stay</h2>
        <span class="enquiry-section-hint">Day is filled in from the date of arrival.</span>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6 col-xl-3">
            <label for="check_in" class="form-label">Date of Arrival <span class="text-danger">*</span></label>
            <div class="date-placeholder-wrap">
              <input type="date" name="check_in" id="check_in" class="form-control @error('check_in') is-invalid @enderror" value="{{ $v('check_in') }}" required>
              <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
            </div>
            @error('check_in')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6 col-xl-3">
            <label for="check_out" class="form-label">Date of Departure <span class="text-danger">*</span></label>
            <div class="date-placeholder-wrap">
              <input type="date" name="check_out" id="check_out" class="form-control @error('check_out') is-invalid @enderror" value="{{ $v('check_out') }}" required>
              <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
            </div>
            @error('check_out')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6 col-xl-3">
            <label for="day" class="form-label">Day <span class="field-auto-badge">Auto</span></label>
            <select name="day" id="day" class="form-select day-auto @error('day') is-invalid @enderror" tabindex="-1" aria-readonly="true">
              <option value="">From arrival date</option>
              @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $weekday)
                <option value="{{ $weekday }}" @selected($selectedDay === $weekday)>{{ $weekday }}</option>
              @endforeach
            </select>
            @error('day')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6 col-xl-3">
            <label for="nights" class="form-label">No. of Nights <span class="text-danger">*</span></label>
            <input type="text" name="nights" id="nights" inputmode="numeric" class="form-control js-digits gb-calc @error('nights') is-invalid @enderror" value="{{ $v('nights') }}" required>
            @error('nights')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6 col-xl-3">
            <label for="block_id" class="form-label">Block ID <span class="text-danger">*</span></label>
            <input type="text" name="block_id" id="block_id" class="form-control @error('block_id') is-invalid @enderror" value="{{ $v('block_id') }}" maxlength="255" required>
            @error('block_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-header">
        <h2 class="enquiry-section-title"><i class="bi bi-person-vcard"></i> Client</h2>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="client" class="form-label">Client</label>
            <input type="text" name="client" id="client" class="form-control @error('client') is-invalid @enderror" value="{{ $v('client') }}" maxlength="255">
            @error('client')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="agency_ref" class="form-label">Agency - Ref</label>
            <input type="text" name="agency_ref" id="agency_ref" class="form-control @error('agency_ref') is-invalid @enderror" value="{{ $v('agency_ref') }}" maxlength="255">
            @error('agency_ref')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="contact_name" class="form-label">Contact</label>
            <input type="text" name="contact_name" id="contact_name" class="form-control @error('contact_name') is-invalid @enderror" value="{{ $contactValue }}" maxlength="255">
            @error('contact_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="email" class="form-label">Email ID</label>
            <input type="text" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ $v('email') }}" maxlength="255" inputmode="email">
            @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-header">
        <h2 class="enquiry-section-title"><i class="bi bi-file-earmark-text"></i> Contract and payment</h2>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6 col-xl-4">
            <label for="contract_sent_on" class="form-label">Contract Sent On</label>
            <div class="date-placeholder-wrap">
              <input type="date" name="contract_sent_on" id="contract_sent_on" class="form-control @error('contract_sent_on') is-invalid @enderror" value="{{ $v('contract_sent_on') }}">
              <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
            </div>
            @error('contract_sent_on')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6 col-xl-4">
            <label for="contract_received_on" class="form-label">Contract Recd On</label>
            <div class="date-placeholder-wrap">
              <input type="date" name="contract_received_on" id="contract_received_on" class="form-control @error('contract_received_on') is-invalid @enderror" value="{{ $v('contract_received_on') }}">
              <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
            </div>
            @error('contract_received_on')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6 col-xl-4">
            <label for="saved_to_doc" class="form-label">Saved to Doc</label>
            <input type="text" name="saved_to_doc" id="saved_to_doc" class="form-control @error('saved_to_doc') is-invalid @enderror" value="{{ $v('saved_to_doc') }}" maxlength="255">
            @error('saved_to_doc')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6 col-xl-4">
            <label for="payment_term" class="form-label">Payment Term</label>
            <input type="text" name="payment_term" id="payment_term" class="form-control @error('payment_term') is-invalid @enderror" value="{{ $v('payment_term') }}" maxlength="255">
            @error('payment_term')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6 col-xl-4">
            <label for="payment_due_date" class="form-label">Due Date</label>
            <div class="date-placeholder-wrap">
              <input type="date" name="payment_due_date" id="payment_due_date" class="form-control @error('payment_due_date') is-invalid @enderror" value="{{ $v('payment_due_date') }}">
              <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
            </div>
            @error('payment_due_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6 col-xl-4">
            <label for="payment_status" class="form-label">Payment Status</label>
            <input type="text" name="payment_status" id="payment_status" class="form-control @error('payment_status') is-invalid @enderror" value="{{ $v('payment_status') }}" maxlength="255">
            @error('payment_status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-header">
        <h2 class="enquiry-section-title"><i class="bi bi-shield-exclamation"></i> Cancellation and commission</h2>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6 col-xl-3">
            <label for="cxl_policy" class="form-label">CXL Policy</label>
            <input type="text" name="cxl_policy" id="cxl_policy" class="form-control @error('cxl_policy') is-invalid @enderror" value="{{ $v('cxl_policy') }}" maxlength="255">
            @error('cxl_policy')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6 col-xl-3">
            <label for="cxl_due_date" class="form-label">CXL Due Date</label>
            <div class="date-placeholder-wrap">
              <input type="date" name="cxl_due_date" id="cxl_due_date" class="form-control @error('cxl_due_date') is-invalid @enderror" value="{{ $v('cxl_due_date') }}">
              <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
            </div>
            @error('cxl_due_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6 col-xl-3">
            <label for="commission" class="form-label">Commission</label>
            <input type="text" name="commission" id="commission" inputmode="decimal" class="form-control js-decimal @error('commission') is-invalid @enderror" value="{{ $v('commission') }}">
            @error('commission')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-header">
        <h2 class="enquiry-section-title"><i class="bi bi-door-open"></i> Rooms and rates</h2>
        <span class="enquiry-section-hint">Total RNs and Total Rev update from the room counts, rates, and nights.</span>
      </div>
      <div class="card-body">
        <div class="row g-3">
          @foreach ([
            'single' => ['label' => 'Single', 'icon' => 'bi-person', 'rooms' => 'Single RNs', 'rate' => 'Single Gross Rate'],
            'double' => ['label' => 'Double', 'icon' => 'bi-people', 'rooms' => 'Double RNs', 'rate' => 'Double Gross Rate'],
            'triple' => ['label' => 'Triple', 'icon' => 'bi-people-fill', 'rooms' => 'Triple RNs', 'rate' => 'Triple Gross Rate'],
          ] as $type => $meta)
            <div class="col-md-4">
              <div class="room-rate-card">
                <h3><i class="bi {{ $meta['icon'] }}"></i> {{ $meta['label'] }}</h3>
                <div class="mb-3">
                  <label for="{{ $type }}_rooms" class="form-label">{{ $meta['rooms'] }}</label>
                  <input type="text" name="{{ $type }}_rooms" id="{{ $type }}_rooms" inputmode="numeric" class="form-control js-digits gb-calc @error($type.'_rooms') is-invalid @enderror" value="{{ $v($type.'_rooms') }}">
                  @error($type.'_rooms')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div>
                  <label for="{{ $type }}_rate" class="form-label">{{ $meta['rate'] }}</label>
                  <input type="text" name="{{ $type }}_rate" id="{{ $type }}_rate" inputmode="decimal" class="form-control js-decimal gb-calc @error($type.'_rate') is-invalid @enderror" value="{{ $v($type.'_rate') }}">
                  @error($type.'_rate')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-header">
        <h2 class="enquiry-section-title"><i class="bi bi-currency-pound"></i> Revenue</h2>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6 col-xl-3">
            <div class="revenue-panel">
              <label for="total_rns_display" class="form-label">Total RNs</label>
              <input type="text" id="total_rns_display" class="form-control" value="" readonly>
              <input type="hidden" name="total_rns" id="total_rns" value="{{ $v('total_rns') }}">
              <div class="form-text">(Single + Double + Triple) × nights</div>
            </div>
          </div>
          <div class="col-md-6 col-xl-3">
            <div class="revenue-panel">
              <label for="total_revenue_display" class="form-label">Total Rev</label>
              <input type="text" id="total_revenue_display" class="form-control" value="" placeholder="£0.00" readonly>
              <input type="hidden" name="total_revenue" id="total_revenue" value="{{ $v('total_revenue') }}">
              <div class="form-text">Room totals × nights</div>
            </div>
          </div>
          <div class="col-md-6 col-xl-3">
            <label for="bb_revenue" class="form-label">BB Revenue (Nett £10)</label>
            <input type="text" name="bb_revenue" id="bb_revenue" inputmode="decimal" class="form-control js-decimal @error('bb_revenue') is-invalid @enderror" value="{{ $v('bb_revenue') }}">
            @error('bb_revenue')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6 col-xl-3">
            <label for="dinner_revenue" class="form-label">Dinner Revenue (Nett £)</label>
            <input type="text" name="dinner_revenue" id="dinner_revenue" inputmode="decimal" class="form-control js-decimal @error('dinner_revenue') is-invalid @enderror" value="{{ $v('dinner_revenue') }}">
            @error('dinner_revenue')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6 col-xl-3">
            <label for="nett_rev_ex_vat" class="form-label">Nett Rev EX VAT &amp; BF</label>
            <input type="text" name="nett_rev_ex_vat" id="nett_rev_ex_vat" inputmode="decimal" class="form-control js-decimal @error('nett_rev_ex_vat') is-invalid @enderror" value="{{ $v('nett_rev_ex_vat') }}">
            @error('nett_rev_ex_vat')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6 col-xl-3">
            <label for="basis" class="form-label">BB/DBB</label>
            <select name="basis" id="basis" class="form-select select2 @error('basis') is-invalid @enderror" data-placeholder="Select BB/DBB">
              <option value="">Select BB/DBB</option>
              @foreach (['BB', 'DBB'] as $basis)
                <option value="{{ $basis }}" @selected($selectedBasis === $basis)>{{ $basis }}</option>
              @endforeach
            </select>
            @error('basis')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-header">
        <h2 class="enquiry-section-title"><i class="bi bi-receipt"></i> Rooming and invoice</h2>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6 col-xl-4">
            <label for="booking_update" class="form-label">Update</label>
            <input type="text" name="booking_update" id="booking_update" class="form-control @error('booking_update') is-invalid @enderror" value="{{ $v('booking_update') }}" maxlength="255">
            @error('booking_update')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6 col-xl-4">
            <label for="rooming" class="form-label">Rooming</label>
            <input type="text" name="rooming" id="rooming" class="form-control @error('rooming') is-invalid @enderror" value="{{ $v('rooming') }}" maxlength="255">
            @error('rooming')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6 col-xl-4">
            <label for="invoice_status" class="form-label">Invoice Status</label>
            <input type="text" name="invoice_status" id="invoice_status" class="form-control @error('invoice_status') is-invalid @enderror" value="{{ $v('invoice_status') }}" maxlength="255">
            @error('invoice_status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6 col-xl-4">
            <label for="invoice_sent_on" class="form-label">Invoice Sent On</label>
            <div class="date-placeholder-wrap">
              <input type="date" name="invoice_sent_on" id="invoice_sent_on" class="form-control @error('invoice_sent_on') is-invalid @enderror" value="{{ $v('invoice_sent_on') }}">
              <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
            </div>
            @error('invoice_sent_on')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6 col-xl-4">
            <label for="invoice_amount" class="form-label">Invoice Amount</label>
            <input type="text" name="invoice_amount" id="invoice_amount" inputmode="decimal" class="form-control js-decimal @error('invoice_amount') is-invalid @enderror" value="{{ $v('invoice_amount') }}">
            @error('invoice_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6 col-xl-4">
            <label for="commission_payable_status" class="form-label">Commission Payable Status</label>
            <input type="text" name="commission_payable_status" id="commission_payable_status" class="form-control @error('commission_payable_status') is-invalid @enderror" value="{{ $v('commission_payable_status') }}" maxlength="255">
            @error('commission_payable_status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="enquiry-sticky-actions">
      <button type="submit" class="btn btn-accent"><i class="bi bi-check-lg"></i> Submit group booking</button>
      <a href="{{ route('enquiries.edit', $enquiry) }}" class="btn btn-outline-secondary">Back</a>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
  var weekdays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

  document.querySelectorAll('.js-digits').forEach(function (el) {
    el.addEventListener('input', function () {
      el.value = el.value.replace(/\D/g, '');
    });
  });

  document.querySelectorAll('.js-decimal').forEach(function (el) {
    el.addEventListener('input', function () {
      var cleaned = el.value.replace(/[^\d.]/g, '');
      var parts = cleaned.split('.');
      el.value = parts.length > 1 ? parts[0] + '.' + parts.slice(1).join('') : parts[0];
    });
  });

  function num(id) {
    var el = document.getElementById(id);
    if (!el || el.value === '') return 0;
    var value = parseFloat(el.value);
    return isNaN(value) ? 0 : value;
  }

  function formatPounds(amount) {
    var parts = amount.toFixed(2).split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    return '£' + parts.join('.');
  }

  function updateDay() {
    var arrival = document.getElementById('check_in');
    var dayEl = document.getElementById('day');
    if (!arrival || !dayEl || !arrival.value) {
      if (dayEl && arrival && !arrival.value) dayEl.value = '';
      return;
    }
    var date = new Date(arrival.value + 'T00:00:00');
    dayEl.value = isNaN(date.getTime()) ? '' : weekdays[date.getDay()];
  }

  function recalc() {
    var nights = num('nights');
    var single = num('single_rooms');
    var doubleRooms = num('double_rooms');
    var triple = num('triple_rooms');
    var totalRns = (single + doubleRooms + triple) * nights;
    var totalRev = (
      (single * num('single_rate'))
      + (doubleRooms * num('double_rate'))
      + (triple * num('triple_rate'))
    ) * nights;
    var rnsDisplay = document.getElementById('total_rns_display');
    var rnsInput = document.getElementById('total_rns');
    var revDisplay = document.getElementById('total_revenue_display');
    var revInput = document.getElementById('total_revenue');
    if (rnsDisplay) rnsDisplay.value = String(totalRns);
    if (rnsInput) rnsInput.value = String(totalRns);
    if (revInput) revInput.value = totalRev.toFixed(2);
    if (revDisplay) revDisplay.value = formatPounds(totalRev);
  }

  var arrivalEl = document.getElementById('check_in');
  if (arrivalEl) {
    arrivalEl.addEventListener('change', updateDay);
    arrivalEl.addEventListener('input', updateDay);
  }
  document.querySelectorAll('.gb-calc').forEach(function (el) {
    el.addEventListener('input', recalc);
    el.addEventListener('change', recalc);
  });
  updateDay();
  recalc();
})();
</script>
@endpush
