@extends('layouts.app')

@section('title', 'Add enquiry')
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
<div class="enquiry-form-layout">
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('enquiries.index') }}">Enquiries</a></li>
        <li class="breadcrumb-item active">Add enquiry</li>
      </ol>
    </nav>
    <h1 class="page-title">Add enquiry</h1>
    <p class="page-subtitle mb-0">Capture the group request, stay dates, and room rates. Fields marked <span class="text-danger">*</span> are required.</p>
  </div>

  <form method="POST" action="{{ route('enquiries.store') }}" class="enquiry-form" novalidate>
    @csrf

    <div class="card mb-4">
      <div class="card-header">
        <h2 class="enquiry-section-title"><i class="bi bi-people"></i> Group</h2>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-8">
            <label for="group_name" class="form-label">Group name <span class="text-danger">*</span></label>
            <input type="text" name="group_name" id="group_name" class="form-control @error('group_name') is-invalid @enderror" value="{{ old('group_name') }}" placeholder="Enter group name" maxlength="255">
            <div class="form-text">Each group name can be used only once.</div>
            @error('group_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="email" class="form-label">Contact email</label>
            <input type="text" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Enter email" maxlength="255" inputmode="email">
            @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <label for="remarks" class="form-label">Remarks</label>
            <textarea name="remarks" id="remarks" rows="3" class="form-control @error('remarks') is-invalid @enderror" placeholder="Enter remarks / special requests">{{ old('remarks') }}</textarea>
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
            <label for="ref" class="form-label">Reference</label>
            <input type="text" name="ref" id="ref" class="form-control @error('ref') is-invalid @enderror" value="{{ old('ref') }}" placeholder="Enter reference" maxlength="255">
            @error('ref')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select select2 @error('status') is-invalid @enderror" data-placeholder="Select status">
              <option value="">Select status</option>
              @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected(old('status') === $status)>{{ $status }}</option>
              @endforeach
            </select>
            @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="year" class="form-label">Year</label>
            <input type="text" name="year" id="year" inputmode="numeric" maxlength="4" class="form-control js-digits @error('year') is-invalid @enderror" value="{{ old('year', date('Y')) }}" placeholder="Enter year">
            @error('year')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="enquiry_date" class="form-label">Enquiry date</label>
            <div class="date-placeholder-wrap">
              <input type="date" name="enquiry_date" id="enquiry_date" class="form-control @error('enquiry_date') is-invalid @enderror" value="{{ old('enquiry_date', date('Y-m-d')) }}" placeholder="DD/MM/YYYY">
              <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
            </div>
            @error('enquiry_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="response_date" class="form-label">Response date</label>
            <div class="date-placeholder-wrap">
              <input type="date" name="response_date" id="response_date" class="form-control @error('response_date') is-invalid @enderror" value="{{ old('response_date') }}" placeholder="DD/MM/YYYY">
              <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
            </div>
            <div class="form-text">Usually left blank. Add a client remark later from the enquiry view.</div>
            @error('response_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
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
            <div class="d-flex gap-2 align-items-start">
              <div class="flex-grow-1">
                <select name="travel_agency_id" id="travel_agency_id" class="form-select select2 @error('travel_agency_id') is-invalid @enderror" data-placeholder="Select a travel agency">
                  <option value="">Select a travel agency</option>
                  @foreach ($travelAgencies as $agency)
                    <option value="{{ $agency->id }}" @selected(old('travel_agency_id') == $agency->id)>{{ $agency->name }}</option>
                  @endforeach
                </select>
                @error('travel_agency_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>
              @can('create', App\Models\TravelAgency::class)
                <button type="button" class="btn btn-outline-secondary flex-shrink-0" data-bs-toggle="modal" data-bs-target="#quickTravelAgencyModal" title="Add travel agency">
                  <i class="bi bi-plus-lg"></i>
                </button>
              @endcan
            </div>
          </div>
          <div class="col-md-6">
            <label for="hotel_id" class="form-label">Hotel</label>
            <select name="hotel_id" id="hotel_id" class="form-select select2 @error('hotel_id') is-invalid @enderror" data-placeholder="Select a hotel">
              <option value="">Select a hotel</option>
              @foreach ($hotels as $hotel)
                <option value="{{ $hotel->id }}" @selected(old('hotel_id') == $hotel->id)>{{ $hotel->name }} ({{ $hotel->code }})</option>
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
              <input type="date" name="check_in" id="check_in" class="form-control @error('check_in') is-invalid @enderror" value="{{ old('check_in') }}" placeholder="DD/MM/YYYY">
              <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
            </div>
            @error('check_in')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="check_in_day" class="form-label">Check-in day</label>
            <input type="text" name="check_in_day" id="check_in_day" class="form-control @error('check_in_day') is-invalid @enderror" value="{{ old('check_in_day') }}" placeholder="Auto from check-in" readonly>
            @error('check_in_day')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="check_out" class="form-label">Check-out</label>
            <div class="date-placeholder-wrap">
              <input type="date" name="check_out" id="check_out" class="form-control @error('check_out') is-invalid @enderror" value="{{ old('check_out') }}" placeholder="DD/MM/YYYY">
              <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
            </div>
            @error('check_out')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="nights" class="form-label">Nights</label>
            <input type="text" name="nights" id="nights" inputmode="numeric" class="form-control js-digits @error('nights') is-invalid @enderror" value="{{ old('nights', 1) }}" placeholder="Auto from dates" readonly>
            <div class="form-text">Auto from check-in / check-out.</div>
            @error('nights')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="rooms_per_night" class="form-label">Rooms per night</label>
            <input type="text" name="rooms_per_night" id="rooms_per_night" inputmode="numeric" class="form-control js-digits @error('rooms_per_night') is-invalid @enderror" value="{{ old('rooms_per_night') }}" placeholder="Enter rooms per night">
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
                  <input type="text" name="{{ $type }}_rooms" id="{{ $type }}_rooms" inputmode="numeric" class="form-control js-digits enquiry-calc @error($type.'_rooms') is-invalid @enderror" value="{{ old($type.'_rooms') }}" placeholder="Enter {{ strtolower($meta['label']) }} rooms">
                  @error($type.'_rooms')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div>
                  <label for="{{ $type }}_rate" class="form-label">Rate (£)</label>
                  <input type="text" name="{{ $type }}_rate" id="{{ $type }}_rate" inputmode="decimal" class="form-control js-decimal enquiry-calc @error($type.'_rate') is-invalid @enderror" value="{{ old($type.'_rate') }}" placeholder="Enter nightly rate">
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
            <input type="text" name="total_revenue" id="total_revenue" inputmode="decimal" class="form-control js-decimal @error('total_revenue') is-invalid @enderror" value="{{ old('total_revenue') }}" placeholder="Enter total revenue (£)">
            <div class="form-text">Auto from rooms × rates × nights.</div>
            @error('total_revenue')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" name="has_tax" id="has_tax" value="1" @checked(old('has_tax'))>
              <label class="form-check-label" for="has_tax">Include tax revenue</label>
            </div>
            <div class="form-text">Turn on to enter a tax percentage.</div>
          </div>
          <div class="col-md-4" id="taxPercentageWrap" style="{{ old('has_tax') ? '' : 'display:none;' }}">
            <label for="tax_percentage" class="form-label">Tax percentage (%) <span class="text-danger">*</span></label>
            <input type="text" name="tax_percentage" id="tax_percentage" inputmode="decimal" class="form-control js-decimal @error('tax_percentage') is-invalid @enderror" value="{{ old('tax_percentage') }}" placeholder="Enter tax percentage">
            @error('tax_percentage')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4" id="taxRevenueWrap" style="{{ old('has_tax') ? '' : 'display:none;' }}">
            <label for="tax_revenue" class="form-label">Tax revenue (£)</label>
            <input type="text" name="tax_revenue" id="tax_revenue" inputmode="decimal" class="form-control @error('tax_revenue') is-invalid @enderror" value="{{ old('tax_revenue') }}" placeholder="Auto from %" readonly>
            <div class="form-text">Auto from total revenue × tax %.</div>
            @error('tax_revenue')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="enquiry-sticky-actions">
      <button type="submit" class="btn btn-accent"><i class="bi bi-check-lg"></i> Save enquiry</button>
      <a href="{{ route('enquiries.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>

  @can('create', App\Models\TravelAgency::class)
  <div class="modal fade" id="quickTravelAgencyModal" tabindex="-1" aria-labelledby="quickTravelAgencyLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="quickTravelAgencyLabel">Add travel agency</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="quickTravelAgencyForm" novalidate>
          <div class="modal-body">
            <div id="quickTravelAgencyErrors" class="alert alert-danger d-none" role="alert"></div>
            <div class="row g-3">
              <div class="col-md-3">
                <label for="qa_code" class="form-label">Code <span class="text-danger">*</span></label>
                <input type="text" name="code" id="qa_code" class="form-control" maxlength="20" placeholder="Enter code">
              </div>
              <div class="col-md-9">
                <label for="qa_name" class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="qa_name" class="form-control" maxlength="255" placeholder="Enter name">
              </div>
              <div class="col-md-4">
                <label for="qa_contact_name" class="form-label">Contact name <span class="text-danger">*</span></label>
                <input type="text" name="contact_name" id="qa_contact_name" class="form-control" maxlength="255" placeholder="Enter contact name">
              </div>
              <div class="col-md-4">
                <label for="qa_email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="text" name="email" id="qa_email" class="form-control" maxlength="255" placeholder="Enter email" inputmode="email">
              </div>
              <div class="col-md-4">
                <label for="qa_phone" class="form-label">Phone <span class="text-danger">*</span></label>
                <input type="text" name="phone" id="qa_phone" class="form-control" maxlength="15" placeholder="Enter phone">
              </div>
              <div class="col-md-4">
                <label for="qa_city" class="form-label">City</label>
                <input type="text" name="city" id="qa_city" class="form-control" maxlength="255" placeholder="Enter city">
              </div>
              <div class="col-md-4">
                <label for="qa_country" class="form-label">Country</label>
                <input type="text" name="country" id="qa_country" class="form-control" maxlength="255" value="United Kingdom" readonly>
              </div>
              <div class="col-md-4">
                <label for="qa_status" class="form-label">Status <span class="text-danger">*</span></label>
                <select name="status" id="qa_status" class="form-select">
                  <option value="active" selected>Active</option>
                  <option value="inactive">Inactive</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-accent" id="quickTravelAgencySubmit">Save agency</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  @endcan
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/enquiry-validation.js') }}?v={{ @filemtime(public_path('assets/js/enquiry-validation.js')) }}"></script>
<script>
(function () {
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
      checkOutEl.classList.add('is-invalid');
      return;
    }

    checkOutEl.classList.remove('is-invalid');
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
    var nightly = (singleRooms * num('single_rate'))
      + (doubleRooms * num('double_rate'))
      + (tripleRooms * num('triple_rate'));
    var roomsEl = document.getElementById('rooms_per_night');
    var totalEl = document.getElementById('total_revenue');
    var anyRooms = ['single_rooms', 'double_rooms', 'triple_rooms'].some(function (id) {
      var el = document.getElementById(id);
      return el && el.value !== '';
    });

    if (anyRooms && roomsEl) {
      roomsEl.value = String(singleRooms + doubleRooms + tripleRooms);
    }
    if (nightly > 0 && totalEl) {
      totalEl.value = (nightly * nights).toFixed(2);
    }
    recalcTaxRevenue();
  }

  ['check_in', 'check_out'].forEach(function (id) {
    var el = document.getElementById(id);
    if (!el) return;
    el.addEventListener('change', nightsFromDates);
    el.addEventListener('input', nightsFromDates);
  });

  document.querySelectorAll('.enquiry-calc, #nights, #total_revenue, #tax_percentage').forEach(function (el) {
    el.addEventListener('input', recalcEnquiryTotals);
    el.addEventListener('change', recalcEnquiryTotals);
  });

  var hasTaxEl = document.getElementById('has_tax');
  if (hasTaxEl) {
    hasTaxEl.addEventListener('change', recalcTaxRevenue);
  }

  nightsFromDates();
  recalcEnquiryTotals();
  recalcTaxRevenue();

  var agencyForm = document.getElementById('quickTravelAgencyForm');
  if (agencyForm) {
    var agencyModalEl = document.getElementById('quickTravelAgencyModal');
    var errorBox = document.getElementById('quickTravelAgencyErrors');
    var submitBtn = document.getElementById('quickTravelAgencySubmit');
    var csrf = document.querySelector('meta[name="csrf-token"]');

    function setAgencyFieldError(el, message) {
      el.classList.add('is-invalid');
      var feedback = el.parentElement.querySelector('.js-client-error');
      if (!feedback) {
        feedback = document.createElement('div');
        feedback.className = 'invalid-feedback js-client-error d-block';
        el.insertAdjacentElement('afterend', feedback);
      }
      feedback.textContent = message;
      feedback.style.display = 'block';
    }

    function clearAgencyFieldError(el) {
      el.classList.remove('is-invalid');
      var feedback = el.parentElement.querySelector('.js-client-error');
      if (feedback) {
        feedback.textContent = '';
        feedback.style.display = 'none';
      }
    }

    function validateAgencyField(el) {
      var name = el.getAttribute('name');
      var value = (el.value || '').trim();
      var requiredMessages = {
        code: 'Code is required.',
        name: 'Name is required.',
        status: 'Status is required.',
        contact_name: 'Contact name is required.',
        email: 'Email is required.',
        phone: 'Phone is required.'
      };
      if (requiredMessages[name] && !value) {
        setAgencyFieldError(el, requiredMessages[name]);
        return false;
      }
      if (name === 'email' && value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
        setAgencyFieldError(el, 'Enter a valid email address.');
        return false;
      }
      if (name === 'phone' && value && value.length > 15) {
        setAgencyFieldError(el, 'Must be 15 characters or fewer.');
        return false;
      }
      clearAgencyFieldError(el);
      return true;
    }

    agencyModalEl.addEventListener('hidden.bs.modal', function () {
      agencyForm.reset();
      document.getElementById('qa_status').value = 'active';
      document.getElementById('qa_country').value = 'United Kingdom';
      errorBox.classList.add('d-none');
      errorBox.innerHTML = '';
      agencyForm.querySelectorAll('.is-invalid').forEach(function (el) { el.classList.remove('is-invalid'); });
      agencyForm.querySelectorAll('.js-client-error').forEach(function (el) { el.remove(); });
    });

    agencyForm.querySelectorAll('input, select').forEach(function (el) {
      ['keyup', 'input', 'change', 'blur'].forEach(function (evt) {
        el.addEventListener(evt, function () { validateAgencyField(el); });
      });
    });

    agencyForm.addEventListener('submit', function (e) {
      e.preventDefault();
      errorBox.classList.add('d-none');
      errorBox.innerHTML = '';

      var firstInvalid = null;
      agencyForm.querySelectorAll('input, select').forEach(function (el) {
        if (!validateAgencyField(el) && !firstInvalid) firstInvalid = el;
      });
      if (firstInvalid) {
        firstInvalid.focus();
        return;
      }

      submitBtn.disabled = true;
      var formData = new FormData(agencyForm);

      fetch(@json(route('travel-agencies.store')), {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrf ? csrf.content : ''
        },
        body: formData
      }).then(function (res) {
        return res.json().then(function (data) {
          return { ok: res.ok, status: res.status, data: data };
        });
      }).then(function (result) {
        if (!result.ok) {
          var messages = [];
          if (result.data && result.data.errors) {
            Object.keys(result.data.errors).forEach(function (key) {
              messages = messages.concat(result.data.errors[key]);
              var field = agencyForm.querySelector('[name="' + key + '"]');
              if (field) setAgencyFieldError(field, result.data.errors[key][0]);
            });
          } else if (result.data && result.data.message) {
            messages.push(result.data.message);
          } else {
            messages.push('Could not save travel agency.');
          }
          errorBox.innerHTML = '<ul class="mb-0">' + messages.map(function (m) {
            return '<li>' + m + '</li>';
          }).join('') + '</ul>';
          errorBox.classList.remove('d-none');
          return;
        }

        var select = document.getElementById('travel_agency_id');
        var label = result.data.name;
        if (window.jQuery && jQuery.fn.select2) {
          var $select = jQuery(select);
          var option = new Option(label, result.data.id, true, true);
          $select.append(option).trigger('change');
        } else {
          var opt = document.createElement('option');
          opt.value = result.data.id;
          opt.textContent = label;
          opt.selected = true;
          select.appendChild(opt);
        }

        var modal = bootstrap.Modal.getInstance(agencyModalEl);
        if (modal) modal.hide();
      }).catch(function () {
        errorBox.innerHTML = '<ul class="mb-0"><li>Could not save travel agency.</li></ul>';
        errorBox.classList.remove('d-none');
      }).finally(function () {
        submitBtn.disabled = false;
      });
    });
  }
})();
</script>
@endpush
