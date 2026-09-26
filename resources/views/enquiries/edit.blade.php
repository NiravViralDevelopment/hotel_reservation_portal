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

  <form method="POST" action="{{ route('enquiries.update', $e) }}">
    @csrf
    @method('PUT')
    <div class="card mb-4">
      <div class="card-header">Enquiry details</div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3">
            <label for="ref" class="form-label">Reference <span class="text-danger">*</span></label>
            <input type="text" name="ref" id="ref" class="form-control @error('ref') is-invalid @enderror" value="{{ old('ref', $e->ref) }}" required>
            @error('ref')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="enquiry_date" class="form-label">Enquiry date</label>
            <input type="date" name="enquiry_date" id="enquiry_date" class="form-control @error('enquiry_date') is-invalid @enderror" value="{{ old('enquiry_date', $e->enquiry_date?->format('Y-m-d')) }}">
            @error('enquiry_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="year" class="form-label">Year</label>
            <input type="number" name="year" id="year" class="form-control @error('year') is-invalid @enderror" value="{{ old('year', $e->year) }}">
            @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
              @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected(old('status', $e->status) === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
              @endforeach
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="group_name" class="form-label">Group name <span class="text-danger">*</span></label>
            <input type="text" name="group_name" id="group_name" class="form-control @error('group_name') is-invalid @enderror" value="{{ old('group_name', $e->group_name) }}" required>
            @error('group_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $e->email) }}">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="travel_agency_id" class="form-label">Travel agency</label>
            <select name="travel_agency_id" id="travel_agency_id" class="form-select @error('travel_agency_id') is-invalid @enderror">
              <option value="">— None —</option>
              @foreach ($travelAgencies as $agency)
                <option value="{{ $agency->id }}" @selected(old('travel_agency_id', $e->travel_agency_id) == $agency->id)>{{ $agency->name }}</option>
              @endforeach
            </select>
            @error('travel_agency_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="hotel_id" class="form-label">Hotel</label>
            <select name="hotel_id" id="hotel_id" class="form-select @error('hotel_id') is-invalid @enderror">
              <option value="">— None —</option>
              @foreach ($hotels as $hotel)
                <option value="{{ $hotel->id }}" @selected(old('hotel_id', $e->hotel_id) == $hotel->id)>{{ $hotel->name }}</option>
              @endforeach
            </select>
            @error('hotel_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="assigned_to" class="form-label">Assigned to</label>
            <select name="assigned_to" id="assigned_to" class="form-select @error('assigned_to') is-invalid @enderror">
              <option value="">— None —</option>
              @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected(old('assigned_to', $e->assigned_to) == $user->id)>{{ $user->name }}</option>
              @endforeach
            </select>
            @error('assigned_to')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="contact_id" class="form-label">Contact ID</label>
            <input type="number" name="contact_id" id="contact_id" class="form-control @error('contact_id') is-invalid @enderror" value="{{ old('contact_id', $e->contact_id) }}">
            @error('contact_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="option_date" class="form-label">Option date</label>
            <input type="date" name="option_date" id="option_date" class="form-control @error('option_date') is-invalid @enderror" value="{{ old('option_date', $e->option_date?->format('Y-m-d')) }}">
            @error('option_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="cxl_policy" class="form-label">Cancellation policy</label>
            <input type="text" name="cxl_policy" id="cxl_policy" class="form-control @error('cxl_policy') is-invalid @enderror" value="{{ old('cxl_policy', $e->cxl_policy) }}">
            @error('cxl_policy')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="nights" class="form-label">Nights</label>
            <input type="number" name="nights" id="nights" min="1" class="form-control @error('nights') is-invalid @enderror" value="{{ old('nights', $e->nights) }}">
            @error('nights')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="rooms_per_night" class="form-label">Rooms per night</label>
            <input type="number" name="rooms_per_night" id="rooms_per_night" class="form-control @error('rooms_per_night') is-invalid @enderror" value="{{ old('rooms_per_night', $e->rooms_per_night) }}">
            @error('rooms_per_night')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="total_revenue" class="form-label">Total revenue (£)</label>
            <input type="number" step="0.01" name="total_revenue" id="total_revenue" class="form-control @error('total_revenue') is-invalid @enderror" value="{{ old('total_revenue', $e->total_revenue) }}">
            @error('total_revenue')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
              <label class="form-label">{{ ucfirst($type) }} rooms</label>
              <input type="number" name="{{ $type }}_rooms" min="0" class="form-control @error($type.'_rooms') is-invalid @enderror" value="{{ old($type.'_rooms', $e->{$type.'_rooms'}) }}">
            </div>
            <div class="col-md-2">
              <label class="form-label">{{ ucfirst($type) }} rate (£)</label>
              <input type="number" step="0.01" name="{{ $type }}_rate" min="0" class="form-control @error($type.'_rate') is-invalid @enderror" value="{{ old($type.'_rate', $e->{$type.'_rate'}) }}">
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
