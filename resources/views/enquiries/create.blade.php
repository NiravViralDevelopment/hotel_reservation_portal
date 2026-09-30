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
  </div>

  <form method="POST" action="{{ route('enquiries.store') }}">
    @csrf
    <div class="card mb-4">
      <div class="card-header">Enquiry details</div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3">
            <label for="ref" class="form-label">Reference <span class="text-danger">*</span></label>
            <input type="text" name="ref" id="ref" class="form-control @error('ref') is-invalid @enderror" value="{{ old('ref') }}" required>
            @error('ref')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="enquiry_date" class="form-label">Enquiry date</label>
            <input type="date" name="enquiry_date" id="enquiry_date" class="form-control @error('enquiry_date') is-invalid @enderror" value="{{ old('enquiry_date') }}">
            @error('enquiry_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="year" class="form-label">Year</label>
            <input type="number" name="year" id="year" min="2000" max="2100" class="form-control @error('year') is-invalid @enderror" value="{{ old('year', date('Y')) }}">
            @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select select2 @error('status') is-invalid @enderror">
              <option value="">— Default —</option>
              @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected(old('status') === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
              @endforeach
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="group_name" class="form-label">Group name <span class="text-danger">*</span></label>
            <input type="text" name="group_name" id="group_name" class="form-control @error('group_name') is-invalid @enderror" value="{{ old('group_name') }}" required>
            @error('group_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="travel_agency_id" class="form-label">Travel agency</label>
            <select name="travel_agency_id" id="travel_agency_id" class="form-select select2 @error('travel_agency_id') is-invalid @enderror">
              <option value="">— None —</option>
              @foreach ($travelAgencies as $agency)
                <option value="{{ $agency->id }}" @selected(old('travel_agency_id') == $agency->id)>{{ $agency->name }}</option>
              @endforeach
            </select>
            @error('travel_agency_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="hotel_id" class="form-label">Hotel</label>
            <select name="hotel_id" id="hotel_id" class="form-select select2 @error('hotel_id') is-invalid @enderror">
              <option value="">— None —</option>
              @foreach ($hotels as $hotel)
                <option value="{{ $hotel->id }}" @selected(old('hotel_id') == $hotel->id)>{{ $hotel->name }} ({{ $hotel->code }})</option>
              @endforeach
            </select>
            @error('hotel_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="nights" class="form-label">Nights</label>
            <input type="number" name="nights" id="nights" min="1" class="form-control @error('nights') is-invalid @enderror" value="{{ old('nights', 1) }}">
            @error('nights')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="rooms_per_night" class="form-label">Rooms per night</label>
            <input type="number" name="rooms_per_night" id="rooms_per_night" min="0" class="form-control @error('rooms_per_night') is-invalid @enderror" value="{{ old('rooms_per_night') }}" readonly>
            @error('rooms_per_night')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="total_revenue" class="form-label">Total revenue (£)</label>
            <input type="number" step="0.01" name="total_revenue" id="total_revenue" min="0" class="form-control @error('total_revenue') is-invalid @enderror" value="{{ old('total_revenue', 0) }}" readonly>
            @error('total_revenue')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text">Auto-calculated from rooms × rates × nights</div>
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
              <input type="number" name="{{ $type }}_rooms" id="{{ $type }}_rooms" min="0" class="form-control enquiry-calc @error($type.'_rooms') is-invalid @enderror" value="{{ old($type.'_rooms', 0) }}">
              @error($type.'_rooms')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-2">
              <label for="{{ $type }}_rate" class="form-label">{{ ucfirst($type) }} rate (£)</label>
              <input type="number" step="0.01" name="{{ $type }}_rate" id="{{ $type }}_rate" min="0" class="form-control enquiry-calc @error($type.'_rate') is-invalid @enderror" value="{{ old($type.'_rate', 0) }}">
              @error($type.'_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          @endforeach
          <div class="col-12">
            <label for="remarks" class="form-label">Remarks</label>
            <textarea name="remarks" id="remarks" rows="3" class="form-control @error('remarks') is-invalid @enderror">{{ old('remarks') }}</textarea>
            @error('remarks')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
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
  function num(id) {
    var el = document.getElementById(id);
    if (!el) return 0;
    var v = parseFloat(el.value);
    return isNaN(v) ? 0 : v;
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
  }

  document.querySelectorAll('.enquiry-calc, #nights').forEach(function (el) {
    el.addEventListener('input', recalcEnquiryTotals);
    el.addEventListener('change', recalcEnquiryTotals);
  });

  recalcEnquiryTotals();
})();
</script>
@endpush
