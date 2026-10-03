@extends('layouts.app')

@section('title', 'Edit enquiry')
@section('page', 'enquiries')

@push('styles')
<style>
  .enquiry-form-layout .enquiry-section-title {
    font-size: 0.9375rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .enquiry-form-layout .enquiry-section-title i { color: var(--brand-accent); }
  .enquiry-form-layout .enquiry-sticky-actions {
    position: sticky;
    bottom: 0;
    z-index: 5;
    display: flex;
    gap: 0.5rem;
    padding: 0.85rem 0;
    margin-top: 0.5rem;
    background: linear-gradient(180deg, transparent, var(--bg-body) 28%);
  }
  .enquiry-form-layout .room-rate-card {
    border: 1px solid var(--border-color);
    border-radius: 0.75rem;
    padding: 1rem;
    height: 100%;
    background: var(--bg-card, #fff);
  }
  .enquiry-form-layout .room-rate-card h3 {
    font-size: 0.875rem;
    font-weight: 600;
    margin: 0 0 0.85rem;
  }
</style>
@endpush

@section('content')
@php $e = $enquiry; @endphp
<div class="enquiry-form-layout">
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('enquiries.index') }}">Enquiries</a></li>
        <li class="breadcrumb-item"><a href="{{ route('enquiries.show', $e) }}">{{ $e->ref }}</a></li>
        <li class="breadcrumb-item active">Edit</li>
      </ol>
    </nav>
    <h1 class="page-title">Edit enquiry</h1>
    <p class="page-subtitle mb-0">Update group details, stay dates, and room rates. Fields marked <span class="text-danger">*</span> are required.</p>
  </div>

  <form method="POST" action="{{ route('enquiries.update', $e) }}" class="enquiry-form" novalidate>
    @csrf
    @method('PUT')

    <div class="card mb-4">
      <div class="card-header">
        <h2 class="enquiry-section-title"><i class="bi bi-people"></i> Group</h2>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-8">
            <label for="group_name" class="form-label">Group name <span class="text-danger">*</span></label>
            <input type="text" name="group_name" id="group_name" class="form-control @error('group_name') is-invalid @enderror" value="{{ old('group_name', $e->group_name) }}" placeholder="Enter group name" maxlength="255">
            <div class="form-text">Each group name can be used only once.</div>
            @error('group_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="email" class="form-label">Contact email</label>
            <input type="text" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $e->email) }}" placeholder="Enter email" maxlength="255" inputmode="email">
            @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <label for="remarks" class="form-label">Remarks</label>
            <textarea name="remarks" id="remarks" rows="3" class="form-control @error('remarks') is-invalid @enderror" placeholder="Enter remarks / special requests">{{ old('remarks', $e->remarks) }}</textarea>
            @error('remarks')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">
        <h2 class="enquiry-section-title"><i class="bi bi-file-earmark-text"></i> Enquiry details</h2>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label for="ref" class="form-label">Reference <span class="text-danger">*</span></label>
            <input type="text" name="ref" id="ref" class="form-control @error('ref') is-invalid @enderror" value="{{ old('ref', $e->ref) }}" placeholder="Enter reference" maxlength="255">
            @error('ref')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select select2 @error('status') is-invalid @enderror" data-placeholder="Select status">
              <option value="">Select status</option>
              @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected(old('status', $e->status) === $status)>{{ $status }}</option>
              @endforeach
            </select>
            @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="year" class="form-label">Year</label>
            <input type="text" name="year" id="year" inputmode="numeric" maxlength="4" class="form-control js-digits @error('year') is-invalid @enderror" value="{{ old('year', $e->year) }}" placeholder="Enter year">
            @error('year')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="enquiry_date" class="form-label">Enquiry date</label>
            <div class="date-placeholder-wrap">
              <input type="date" name="enquiry_date" id="enquiry_date" class="form-control @error('enquiry_date') is-invalid @enderror" value="{{ old('enquiry_date', $e->enquiry_date?->format('Y-m-d')) }}" placeholder="DD/MM/YYYY">
              <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
            </div>
            @error('enquiry_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="response_date" class="form-label">Response date</label>
            <div class="date-placeholder-wrap">
              <input type="date" name="response_date" id="response_date" class="form-control @error('response_date') is-invalid @enderror" value="{{ old('response_date', $e->response_date?->format('Y-m-d')) }}" placeholder="DD/MM/YYYY">
              <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
            </div>
            @error('response_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="option_date" class="form-label">Option date</label>
            <div class="date-placeholder-wrap">
              <input type="date" name="option_date" id="option_date" class="form-control @error('option_date') is-invalid @enderror" value="{{ old('option_date', $e->option_date?->format('Y-m-d')) }}" placeholder="DD/MM/YYYY">
              <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
            </div>
            @error('option_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="cxl_policy" class="form-label">Cancellation policy</label>
            <input type="text" name="cxl_policy" id="cxl_policy" class="form-control @error('cxl_policy') is-invalid @enderror" value="{{ old('cxl_policy', $e->cxl_policy) }}" placeholder="Enter cancellation policy" maxlength="255">
            @error('cxl_policy')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">
        <h2 class="enquiry-section-title"><i class="bi bi-arrow-left-right"></i> Booking action</h2>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-12">
            <div class="d-flex flex-wrap gap-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="confirm_booking" id="confirm_booking" value="1" @checked(old('confirm_booking'))>
                <label class="form-check-label" for="confirm_booking">Confirm booking</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="cancel_booking" id="cancel_booking" value="1" @checked(old('cancel_booking'))>
                <label class="form-check-label" for="cancel_booking">Cancel</label>
              </div>
            </div>
            <div class="form-text">Select one action. Confirm moves to Group Bookings; Cancel moves to Cancelled Bookings.</div>
            @error('confirm_booking')<div class="text-danger small d-block">{{ $message }}</div>@enderror
            @error('cancel_booking')<div class="text-danger small d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-12" id="cancellationReasonWrap" style="{{ old('cancel_booking') ? '' : 'display:none;' }}">
            <label for="cancellation_reason" class="form-label">Cancellation reason <span class="text-danger">*</span></label>
            <textarea name="cancellation_reason" id="cancellation_reason" rows="2" class="form-control @error('cancellation_reason') is-invalid @enderror" placeholder="Enter cancellation reason">{{ old('cancellation_reason') }}</textarea>
            @error('cancellation_reason')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">
        <h2 class="enquiry-section-title"><i class="bi bi-building"></i> Partners</h2>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="travel_agency_id" class="form-label">Travel agency</label>
            <select name="travel_agency_id" id="travel_agency_id" class="form-select select2 @error('travel_agency_id') is-invalid @enderror" data-placeholder="Select a travel agency">
              <option value="">Select a travel agency</option>
              @foreach ($travelAgencies as $agency)
                <option value="{{ $agency->id }}" @selected(old('travel_agency_id', $e->travel_agency_id) == $agency->id)>{{ $agency->name }}</option>
              @endforeach
            </select>
            @error('travel_agency_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="hotel_id" class="form-label">Hotel</label>
            <select name="hotel_id" id="hotel_id" class="form-select select2 @error('hotel_id') is-invalid @enderror" data-placeholder="Select a hotel">
              <option value="">Select a hotel</option>
              @foreach ($hotels as $hotel)
                <option value="{{ $hotel->id }}" @selected(old('hotel_id', $e->hotel_id) == $hotel->id)>{{ $hotel->name }} ({{ $hotel->code }})</option>
              @endforeach
            </select>
            @error('hotel_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">
        <h2 class="enquiry-section-title"><i class="bi bi-calendar2-week"></i> Stay</h2>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3">
            <label for="check_in" class="form-label">Check-in</label>
            <div class="date-placeholder-wrap">
              <input type="date" name="check_in" id="check_in" class="form-control @error('check_in') is-invalid @enderror" value="{{ old('check_in', $e->check_in?->format('Y-m-d')) }}" placeholder="DD/MM/YYYY">
              <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
            </div>
            @error('check_in')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="check_in_day" class="form-label">Check-in day</label>
            <input type="text" name="check_in_day" id="check_in_day" class="form-control @error('check_in_day') is-invalid @enderror" value="{{ old('check_in_day', $e->check_in_day) }}" placeholder="Auto from check-in" readonly>
            @error('check_in_day')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="check_out" class="form-label">Check-out</label>
            <div class="date-placeholder-wrap">
              <input type="date" name="check_out" id="check_out" class="form-control @error('check_out') is-invalid @enderror" value="{{ old('check_out', $e->check_out?->format('Y-m-d')) }}" placeholder="DD/MM/YYYY">
              <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
            </div>
            @error('check_out')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="nights" class="form-label">Nights</label>
            <input type="text" name="nights" id="nights" inputmode="numeric" class="form-control js-digits @error('nights') is-invalid @enderror" value="{{ old('nights', $e->nights ?? 1) }}" placeholder="Auto from dates" readonly>
            <div class="form-text">Auto from check-in / check-out.</div>
            @error('nights')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="rooms_per_night" class="form-label">Rooms per night</label>
            <input type="text" name="rooms_per_night" id="rooms_per_night" inputmode="numeric" class="form-control js-digits @error('rooms_per_night') is-invalid @enderror" value="{{ old('rooms_per_night', $e->rooms_per_night) }}" placeholder="Enter rooms per night" readonly>
            <div class="form-text">Filled automatically when room counts are entered.</div>
            @error('rooms_per_night')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">
        <h2 class="enquiry-section-title"><i class="bi bi-door-open"></i> Rooms and rates</h2>
      </div>
      <div class="card-body">
        <p class="small text-secondary mb-3">Enter room counts and nightly rates. Total revenue updates automatically.</p>
        <div class="row g-3">
          @foreach ([
            'single' => ['label' => 'Single', 'icon' => 'bi-person'],
            'double' => ['label' => 'Double', 'icon' => 'bi-people'],
            'triple' => ['label' => 'Triple', 'icon' => 'bi-people-fill'],
          ] as $type => $meta)
            <div class="col-md-4">
              <div class="room-rate-card">
                <h3><i class="bi {{ $meta['icon'] }} me-1"></i> {{ $meta['label'] }}</h3>
                <div class="mb-3">
                  <label for="{{ $type }}_rooms" class="form-label">Rooms</label>
                  <input type="text" name="{{ $type }}_rooms" id="{{ $type }}_rooms" inputmode="numeric" class="form-control js-digits enquiry-calc @error($type.'_rooms') is-invalid @enderror" value="{{ old($type.'_rooms', $e->{$type.'_rooms'}) }}" placeholder="Enter {{ strtolower($meta['label']) }} rooms">
                  @error($type.'_rooms')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div>
                  <label for="{{ $type }}_rate" class="form-label">Rate (£)</label>
                  <input type="text" name="{{ $type }}_rate" id="{{ $type }}_rate" inputmode="decimal" class="form-control js-decimal enquiry-calc @error($type.'_rate') is-invalid @enderror" value="{{ old($type.'_rate', $e->{$type.'_rate'}) }}" placeholder="Enter nightly rate">
                  @error($type.'_rate')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">
        <h2 class="enquiry-section-title"><i class="bi bi-currency-pound"></i> Revenue</h2>
      </div>
      <div class="card-body">
        <div class="row g-3 align-items-end">
          <div class="col-md-4">
            <label for="total_revenue" class="form-label">Total revenue (£)</label>
            <input type="text" name="total_revenue" id="total_revenue" inputmode="decimal" class="form-control js-decimal @error('total_revenue') is-invalid @enderror" value="{{ old('total_revenue', $e->total_revenue) }}" placeholder="Enter total revenue (£)" readonly>
            <div class="form-text">Auto from rooms × rates × nights.</div>
            @error('total_revenue')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" name="has_tax" id="has_tax" value="1" @checked(old('has_tax', $e->has_tax))>
              <label class="form-check-label" for="has_tax">Include tax revenue</label>
            </div>
            <div class="form-text">Turn on to enter a tax percentage.</div>
          </div>
          <div class="col-md-4" id="taxPercentageWrap" style="{{ old('has_tax', $e->has_tax) ? '' : 'display:none;' }}">
            <label for="tax_percentage" class="form-label">Tax percentage (%) <span class="text-danger">*</span></label>
            <input type="text" name="tax_percentage" id="tax_percentage" inputmode="decimal" class="form-control js-decimal @error('tax_percentage') is-invalid @enderror" value="{{ old('tax_percentage', $e->tax_percentage) }}" placeholder="Enter tax percentage">
            @error('tax_percentage')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4" id="taxRevenueWrap" style="{{ old('has_tax', $e->has_tax) ? '' : 'display:none;' }}">
            <label for="tax_revenue" class="form-label">Tax revenue (£)</label>
            <input type="text" name="tax_revenue" id="tax_revenue" inputmode="decimal" class="form-control @error('tax_revenue') is-invalid @enderror" value="{{ old('tax_revenue', $e->tax_revenue) }}" placeholder="Auto from %" readonly>
            <div class="form-text">Auto from total revenue × tax %.</div>
            @error('tax_revenue')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="enquiry-sticky-actions">
      <button type="submit" class="btn btn-accent"><i class="bi bi-check-lg"></i> Update enquiry</button>
      <a href="{{ route('enquiries.show', $e) }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/enquiry-validation.js') }}?v={{ @filemtime(public_path('assets/js/enquiry-validation.js')) }}"></script>
<script>
(function () {
  document.querySelectorAll('.js-digits').forEach(function (el) {
    el.addEventListener('input', function () {
      if (el.readOnly) return;
      el.value = el.value.replace(/\D/g, '');
    });
  });

  document.querySelectorAll('.js-decimal').forEach(function (el) {
    el.addEventListener('input', function () {
      if (el.readOnly) return;
      var cleaned = el.value.replace(/[^\d.]/g, '');
      var parts = cleaned.split('.');
      el.value = parts.length > 1 ? parts[0] + '.' + parts.slice(1).join('') : parts[0];
    });
  });

  function num(id) {
    var el = document.getElementById(id);
    if (!el || el.value === '') return 0;
    var v = parseFloat(el.value);
    return isNaN(v) ? 0 : v;
  }

  function dayNameFromDate(value) {
    if (!value) return '';
    var date = new Date(value + 'T00:00:00');
    if (isNaN(date.getTime())) return '';
    return date.toLocaleDateString('en-GB', { weekday: 'long' });
  }

  function updateCheckInDay() {
    var checkInEl = document.getElementById('check_in');
    var dayEl = document.getElementById('check_in_day');
    if (!checkInEl || !dayEl) return;
    dayEl.value = dayNameFromDate(checkInEl.value);
  }

  function nightsFromDates() {
    var checkInEl = document.getElementById('check_in');
    var checkOutEl = document.getElementById('check_out');
    var nightsEl = document.getElementById('nights');
    updateCheckInDay();
    if (!checkInEl || !checkOutEl || !nightsEl) return;

    var checkIn = checkInEl.value;
    var checkOut = checkOutEl.value;
    if (!checkIn || !checkOut) return;

    var start = new Date(checkIn + 'T00:00:00');
    var end = new Date(checkOut + 'T00:00:00');
    if (isNaN(start.getTime()) || isNaN(end.getTime())) return;

    var diff = Math.round((end - start) / 86400000);
    if (diff < 1) {
      nightsEl.value = '';
      return;
    }

    nightsEl.value = String(diff);
    recalcEnquiryTotals();
  }

  function recalcTaxRevenue() {
    var hasTax = document.getElementById('has_tax');
    var taxPctEl = document.getElementById('tax_percentage');
    var taxRevEl = document.getElementById('tax_revenue');
    var pctWrap = document.getElementById('taxPercentageWrap');
    var revWrap = document.getElementById('taxRevenueWrap');
    var enabled = !!(hasTax && hasTax.checked);

    if (pctWrap) pctWrap.style.display = enabled ? '' : 'none';
    if (revWrap) revWrap.style.display = enabled ? '' : 'none';

    if (!enabled) {
      if (taxPctEl) taxPctEl.value = '';
      if (taxRevEl) taxRevEl.value = '';
      return;
    }

    var total = num('total_revenue');
    var pct = num('tax_percentage');
    if (taxRevEl) {
      taxRevEl.value = (total > 0 && pct > 0) ? (total * pct / 100).toFixed(2) : '';
    }
  }

  function recalcEnquiryTotals() {
    var singleRooms = num('single_rooms');
    var doubleRooms = num('double_rooms');
    var tripleRooms = num('triple_rooms');
    var nights = num('nights') || 1;

    var roomsPerNight = singleRooms + doubleRooms + tripleRooms;
    var nightly = (singleRooms * num('single_rate'))
      + (doubleRooms * num('double_rate'))
      + (tripleRooms * num('triple_rate'));
    var total = nightly * nights;

    var roomsEl = document.getElementById('rooms_per_night');
    var totalEl = document.getElementById('total_revenue');
    if (roomsEl) roomsEl.value = roomsPerNight;
    if (totalEl) totalEl.value = total.toFixed(2);
    recalcTaxRevenue();
  }

  function toggleActionPanels() {
    var cancelEl = document.getElementById('cancel_booking');
    var cancelWrap = document.getElementById('cancellationReasonWrap');
    var isCancel = !!(cancelEl && cancelEl.checked);
    if (cancelWrap) cancelWrap.style.display = isCancel ? '' : 'none';
  }

  ['check_in', 'check_out'].forEach(function (id) {
    var el = document.getElementById(id);
    if (!el) return;
    el.addEventListener('change', nightsFromDates);
    el.addEventListener('input', nightsFromDates);
  });

  document.querySelectorAll('.enquiry-calc, #nights, #tax_percentage').forEach(function (el) {
    el.addEventListener('input', recalcEnquiryTotals);
    el.addEventListener('change', recalcEnquiryTotals);
  });

  var hasTaxEl = document.getElementById('has_tax');
  if (hasTaxEl) {
    hasTaxEl.addEventListener('change', recalcTaxRevenue);
  }

  var confirmEl = document.getElementById('confirm_booking');
  var cancelEl = document.getElementById('cancel_booking');

  if (confirmEl) {
    confirmEl.addEventListener('change', function () {
      if (confirmEl.checked && cancelEl) cancelEl.checked = false;
      toggleActionPanels();
    });
  }

  if (cancelEl) {
    cancelEl.addEventListener('change', function () {
      if (cancelEl.checked && confirmEl) confirmEl.checked = false;
      toggleActionPanels();
    });
  }

  nightsFromDates();
  recalcEnquiryTotals();
  recalcTaxRevenue();
  toggleActionPanels();
})();
</script>
@endpush
