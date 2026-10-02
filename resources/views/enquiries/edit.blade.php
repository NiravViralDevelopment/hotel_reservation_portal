@extends('layouts.app')

@section('title', 'Edit enquiry')
@section('page', 'enquiries')

@section('content')
  @php $e = $enquiry; @endphp
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('enquiries.index') }}">Enquiries</a></li>
        <li class="breadcrumb-item active">{{ $e->ref }}</li>
      </ol>
    </nav>
    <h1 class="page-title">Edit enquiry</h1>
  </div>

  <form method="POST" action="{{ route('enquiries.update', $e) }}" class="enquiry-form" novalidate>
    @csrf
    @method('PUT')
    <div class="card mb-4">
      <div class="card-header">Enquiry details</div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3">
            <label for="ref" class="form-label">Reference <span class="text-danger">*</span></label>
            <input type="text" name="ref" id="ref" class="form-control @error('ref') is-invalid @enderror" value="{{ old('ref', $e->ref) }}">
            @error('ref')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="enquiry_date" class="form-label">Enquiry date</label>
            <input type="date" name="enquiry_date" id="enquiry_date" class="form-control @error('enquiry_date') is-invalid @enderror" value="{{ old('enquiry_date', $e->enquiry_date?->format('Y-m-d')) }}">
            @error('enquiry_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="response_date" class="form-label">Response date</label>
            <div class="date-placeholder-wrap">
              <input type="date" name="response_date" id="response_date" class="form-control @error('response_date') is-invalid @enderror" value="{{ old('response_date', $e->response_date?->format('Y-m-d')) }}" placeholder="DD/MM/YYYY">
              <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
            </div>
            @error('response_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="year" class="form-label">Year</label>
            <input type="number" name="year" id="year" class="form-control @error('year') is-invalid @enderror" value="{{ old('year', $e->year) }}">
            @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select select2 @error('status') is-invalid @enderror">
              @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected(old('status', $e->status?->value ?? $e->status) === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
              @endforeach
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <label class="form-label d-block">Booking action</label>
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
            @error('confirm_booking')<div class="text-danger small">{{ $message }}</div>@enderror
            @error('cancel_booking')<div class="text-danger small">{{ $message }}</div>@enderror
          </div>
          <div class="col-12" id="cancellationReasonWrap" style="{{ old('cancel_booking') ? '' : 'display:none;' }}">
            <label for="cancellation_reason" class="form-label">Cancellation reason <span class="text-danger">*</span></label>
            <textarea name="cancellation_reason" id="cancellation_reason" rows="2" class="form-control @error('cancellation_reason') is-invalid @enderror" placeholder="Required when Cancel is checked">{{ old('cancellation_reason') }}</textarea>
            @error('cancellation_reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="group_name" class="form-label">Group name <span class="text-danger">*</span></label>
            <input type="text" name="group_name" id="group_name" class="form-control @error('group_name') is-invalid @enderror" value="{{ old('group_name', $e->group_name) }}" placeholder="e.g. Smith wedding party">
            <div class="form-text">Each group name can be used only once.</div>
            @error('group_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $e->email) }}">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="travel_agency_id" class="form-label">Travel agency</label>
            <select name="travel_agency_id" id="travel_agency_id" class="form-select select2 @error('travel_agency_id') is-invalid @enderror">
              <option value="">— None —</option>
              @foreach ($travelAgencies as $agency)
                <option value="{{ $agency->id }}" @selected(old('travel_agency_id', $e->travel_agency_id) == $agency->id)>{{ $agency->name }}</option>
              @endforeach
            </select>
            @error('travel_agency_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="hotel_id" class="form-label">Hotel</label>
            <select name="hotel_id" id="hotel_id" class="form-select select2 @error('hotel_id') is-invalid @enderror">
              <option value="">— None —</option>
              @foreach ($hotels as $hotel)
                <option value="{{ $hotel->id }}" @selected(old('hotel_id', $e->hotel_id) == $hotel->id)>{{ $hotel->name }}</option>
              @endforeach
            </select>
            @error('hotel_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="check_in" class="form-label">Check-in</label>
            <div class="date-placeholder-wrap">
              <input type="date" name="check_in" id="check_in" class="form-control @error('check_in') is-invalid @enderror" value="{{ old('check_in', $e->check_in?->format('Y-m-d')) }}" placeholder="DD/MM/YYYY">
              <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
            </div>
            @error('check_in')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="check_out" class="form-label">Check-out</label>
            <div class="date-placeholder-wrap">
              <input type="date" name="check_out" id="check_out" class="form-control @error('check_out') is-invalid @enderror" value="{{ old('check_out', $e->check_out?->format('Y-m-d')) }}" placeholder="DD/MM/YYYY">
              <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
            </div>
            @error('check_out')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="option_date" class="form-label">Option date</label>
            <input type="date" name="option_date" id="option_date" class="form-control @error('option_date') is-invalid @enderror" value="{{ old('option_date', $e->option_date?->format('Y-m-d')) }}">
            @error('option_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="cxl_policy" class="form-label">Cancellation policy</label>
            <input type="text" name="cxl_policy" id="cxl_policy" class="form-control @error('cxl_policy') is-invalid @enderror" value="{{ old('cxl_policy', $e->cxl_policy) }}">
            @error('cxl_policy')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="nights" class="form-label">Nights</label>
            <input type="number" name="nights" id="nights" min="1" class="form-control @error('nights') is-invalid @enderror" value="{{ old('nights', $e->nights ?? 1) }}" readonly>
            <div class="form-text">Auto-calculated from check-in and check-out.</div>
            @error('nights')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="rooms_per_night" class="form-label">Rooms per night</label>
            <input type="number" name="rooms_per_night" id="rooms_per_night" class="form-control @error('rooms_per_night') is-invalid @enderror" value="{{ old('rooms_per_night', $e->rooms_per_night) }}" readonly>
            @error('rooms_per_night')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="total_revenue" class="form-label">Total revenue (£)</label>
            <input type="number" step="0.01" name="total_revenue" id="total_revenue" class="form-control @error('total_revenue') is-invalid @enderror" value="{{ old('total_revenue', $e->total_revenue) }}" readonly>
            @error('total_revenue')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text">Auto-calculated from rooms × rates × nights</div>
          </div>
          <div class="col-md-4 d-flex align-items-end">
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" name="has_tax" id="has_tax" value="1" @checked(old('has_tax', $e->has_tax))>
              <label class="form-check-label" for="has_tax">Tax revenue</label>
            </div>
          </div>
          <div class="col-md-4" id="taxPercentageWrap" style="{{ old('has_tax', $e->has_tax) ? '' : 'display:none;' }}">
            <label for="tax_percentage" class="form-label">Tax percentage (%) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0" max="100" name="tax_percentage" id="tax_percentage" class="form-control @error('tax_percentage') is-invalid @enderror" value="{{ old('tax_percentage', $e->tax_percentage) }}" placeholder="e.g. 20">
            @error('tax_percentage')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4" id="taxRevenueWrap" style="{{ old('has_tax', $e->has_tax) ? '' : 'display:none;' }}">
            <label for="tax_revenue" class="form-label">Tax revenue (£)</label>
            <input type="number" step="0.01" name="tax_revenue" id="tax_revenue" class="form-control @error('tax_revenue') is-invalid @enderror" value="{{ old('tax_revenue', $e->tax_revenue) }}" readonly>
            <div class="form-text">Auto-calculated from total revenue × tax %.</div>
            @error('tax_revenue')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>
    <div class="card mb-4">
      <div class="card-header">Room breakdown</div>
      <div class="card-body">
        <div class="row g-3">
          @foreach (['single', 'double', 'triple'] as $type)
            <div class="col-md-2">
              <label for="{{ $type }}_rooms" class="form-label">{{ ucfirst($type) }} rooms</label>
              <input type="number" name="{{ $type }}_rooms" id="{{ $type }}_rooms" min="0" class="form-control enquiry-calc @error($type.'_rooms') is-invalid @enderror" value="{{ old($type.'_rooms', $e->{$type.'_rooms'}) }}">
            </div>
            <div class="col-md-2">
              <label for="{{ $type }}_rate" class="form-label">{{ ucfirst($type) }} rate (£)</label>
              <input type="number" step="0.01" name="{{ $type }}_rate" id="{{ $type }}_rate" min="0" class="form-control enquiry-calc @error($type.'_rate') is-invalid @enderror" value="{{ old($type.'_rate', $e->{$type.'_rate'}) }}">
            </div>
          @endforeach
          <div class="col-12">
            <label for="remarks" class="form-label">Remarks</label>
            <textarea name="remarks" id="remarks" rows="3" class="form-control @error('remarks') is-invalid @enderror">{{ old('remarks', $e->remarks) }}</textarea>
            @error('remarks')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>
    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-accent">Update enquiry</button>
      <a href="{{ route('enquiries.show', $e) }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/enquiry-validation.js') }}?v={{ @filemtime(public_path('assets/js/enquiry-validation.js')) }}"></script>
<script>
(function () {
  function num(id) {
    var el = document.getElementById(id);
    if (!el) return 0;
    var v = parseFloat(el.value);
    return isNaN(v) ? 0 : v;
  }

  function nightsFromDates() {
    var checkInEl = document.getElementById('check_in');
    var checkOutEl = document.getElementById('check_out');
    var nightsEl = document.getElementById('nights');
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
    var confirmEl = document.getElementById('confirm_booking');
    var cancelEl = document.getElementById('cancel_booking');
    var cancelWrap = document.getElementById('cancellationReasonWrap');
    var reasonEl = document.getElementById('cancellation_reason');
    var isCancel = !!(cancelEl && cancelEl.checked);

    if (cancelWrap) cancelWrap.style.display = isCancel ? '' : 'none';
    if (reasonEl) {
      if (!isCancel) reasonEl.value = reasonEl.value;
    }
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
