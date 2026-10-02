@extends('layouts.app')

@section('title', 'Add enquiry')
@section('page', 'enquiries')

@section('content')
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('enquiries.index') }}">Enquiries</a></li>
        <li class="breadcrumb-item active">Add enquiry</li>
      </ol>
    </nav>
    <h1 class="page-title">Add enquiry</h1>
    <p class="page-subtitle mb-0">Fields marked <span class="text-danger">*</span> are required.</p>
  </div>

  <form method="POST" action="{{ route('enquiries.store') }}" class="enquiry-form">
    @csrf
    <div class="card mb-4">
      <div class="card-header">Group</div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-8">
            <label for="group_name" class="form-label">Group name <span class="text-danger">*</span></label>
            <input type="text" name="group_name" id="group_name" class="form-control @error('group_name') is-invalid @enderror" value="{{ old('group_name') }}" placeholder="e.g. Smith wedding party" required>
            <div class="form-text">Each group name can be used only once.</div>
            @error('group_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="name@company.co.uk">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <label for="remarks" class="form-label">Remarks</label>
            <textarea name="remarks" id="remarks" rows="3" class="form-control @error('remarks') is-invalid @enderror" placeholder="Notes about this enquiry">{{ old('remarks') }}</textarea>
            @error('remarks')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">Enquiry</div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label for="ref" class="form-label">Reference <span class="text-danger">*</span></label>
            <input type="text" name="ref" id="ref" class="form-control @error('ref') is-invalid @enderror" value="{{ old('ref') }}" placeholder="e.g. ENQ-2026-014" required>
            @error('ref')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select select2 @error('status') is-invalid @enderror" data-placeholder="Select status">
              <option value="">Select status</option>
              @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected(old('status') === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
              @endforeach
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="year" class="form-label">Year</label>
            <input type="text" name="year" id="year" inputmode="numeric" maxlength="4" class="form-control js-digits @error('year') is-invalid @enderror" value="{{ old('year', date('Y')) }}" placeholder="YYYY">
            @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="enquiry_date" class="form-label">Enquiry date</label>
            <div class="date-placeholder-wrap">
              <input type="date" name="enquiry_date" id="enquiry_date" class="form-control @error('enquiry_date') is-invalid @enderror" value="{{ old('enquiry_date') }}" placeholder="DD/MM/YYYY">
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
            <div class="form-text">Leave this blank. Add the client response later from the enquiries list.</div>
            @error('response_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">Stay</div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="travel_agency_id" class="form-label">Travel agency</label>
            <select name="travel_agency_id" id="travel_agency_id" class="form-select select2 @error('travel_agency_id') is-invalid @enderror" data-placeholder="Select a travel agency">
              <option value="">Select a travel agency</option>
              @foreach ($travelAgencies as $agency)
                <option value="{{ $agency->id }}" @selected(old('travel_agency_id') == $agency->id)>{{ $agency->name }}</option>
              @endforeach
            </select>
            @error('travel_agency_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="hotel_id" class="form-label">Hotel</label>
            <select name="hotel_id" id="hotel_id" class="form-select select2 @error('hotel_id') is-invalid @enderror" data-placeholder="Select a hotel">
              <option value="">Select a hotel</option>
              @foreach ($hotels as $hotel)
                <option value="{{ $hotel->id }}" @selected(old('hotel_id') == $hotel->id)>{{ $hotel->name }} ({{ $hotel->code }})</option>
              @endforeach
            </select>
            @error('hotel_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="nights" class="form-label">Nights</label>
            <input type="text" name="nights" id="nights" inputmode="numeric" class="form-control js-digits @error('nights') is-invalid @enderror" value="{{ old('nights', 1) }}" placeholder="e.g. 2">
            @error('nights')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="rooms_per_night" class="form-label">Rooms per night</label>
            <input type="text" name="rooms_per_night" id="rooms_per_night" inputmode="numeric" pattern="[0-9]*" class="form-control js-digits @error('rooms_per_night') is-invalid @enderror" value="{{ old('rooms_per_night') }}" placeholder="e.g. 10">
            @error('rooms_per_night')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="total_revenue" class="form-label">Total revenue (£)</label>
            <input type="text" name="total_revenue" id="total_revenue" inputmode="decimal" class="form-control js-decimal @error('total_revenue') is-invalid @enderror" value="{{ old('total_revenue') }}" placeholder="e.g. 1500.00">
            <div class="form-text">Filled from rooms × rates × nights when rates are entered.</div>
            @error('total_revenue')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">Rooms and rates</div>
      <div class="card-body">
        <div class="row g-3">
          @foreach (['single' => '95.00', 'double' => '120.00', 'triple' => '150.00'] as $type => $rateExample)
            <div class="col-md-6">
              <label for="{{ $type }}_rooms" class="form-label">{{ ucfirst($type) }} rooms</label>
              <input type="text" name="{{ $type }}_rooms" id="{{ $type }}_rooms" inputmode="numeric" class="form-control js-digits enquiry-calc @error($type.'_rooms') is-invalid @enderror" value="{{ old($type.'_rooms') }}" placeholder="e.g. {{ $type === 'double' ? '4' : ($type === 'triple' ? '1' : '2') }}">
              @error($type.'_rooms')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label for="{{ $type }}_rate" class="form-label">{{ ucfirst($type) }} rate (£)</label>
              <input type="text" name="{{ $type }}_rate" id="{{ $type }}_rate" inputmode="decimal" class="form-control js-decimal enquiry-calc @error($type.'_rate') is-invalid @enderror" value="{{ old($type.'_rate') }}" placeholder="e.g. {{ $rateExample }}">
              @error($type.'_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          @endforeach
        </div>
      </div>
    </div>
    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-accent">Save enquiry</button>
      <a href="{{ route('enquiries.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
@endsection

@push('scripts')
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
  }

  document.querySelectorAll('.enquiry-calc, #nights').forEach(function (el) {
    el.addEventListener('input', recalcEnquiryTotals);
    el.addEventListener('change', recalcEnquiryTotals);
  });
})();
</script>
@endpush
