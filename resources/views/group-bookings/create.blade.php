@extends('layouts.app')

@section('title', 'Add group booking')
@section('page', 'group-bookings')

@section('content')
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('group-bookings.index') }}">Group bookings</a></li>
        <li class="breadcrumb-item active">Add booking</li>
      </ol>
    </nav>
    <h1 class="page-title">Add group booking</h1>
  </div>

  <form method="POST" action="{{ route('group-bookings.store') }}">
    @csrf
    <div class="card mb-4">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label for="block_id" class="form-label">Block ID <span class="text-danger">*</span></label>
            <input type="text" name="block_id" id="block_id" class="form-control @error('block_id') is-invalid @enderror" value="{{ old('block_id') }}" required>
            @error('block_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-8">
            <label for="group_name" class="form-label">Group name <span class="text-danger">*</span></label>
            <input type="text" name="group_name" id="group_name" class="form-control @error('group_name') is-invalid @enderror" value="{{ old('group_name') }}" required>
            @error('group_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="hotel_id" class="form-label">Hotel</label>
            <select name="hotel_id" id="hotel_id" class="form-select select2 @error('hotel_id') is-invalid @enderror">
              <option value="">— None —</option>
              @foreach ($hotels as $hotel)
                <option value="{{ $hotel->id }}" @selected(old('hotel_id') == $hotel->id)>{{ $hotel->name }}</option>
              @endforeach
            </select>
            @error('hotel_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="company_id" class="form-label">Company</label>
            <select name="company_id" id="company_id" class="form-select select2 @error('company_id') is-invalid @enderror">
              <option value="">— None —</option>
              @foreach ($companies as $company)
                <option value="{{ $company->id }}" @selected(old('company_id') == $company->id)>{{ $company->name }}</option>
              @endforeach
            </select>
            @error('company_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="travel_agency_id" class="form-label">Travel agency</label>
            <select name="travel_agency_id" id="travel_agency_id" class="form-select select2 @error('travel_agency_id') is-invalid @enderror">
              <option value="">— None —</option>
              @foreach ($travelAgencies as $agency)
                <option value="{{ $agency->id }}" @selected(old('travel_agency_id') == $agency->id)>{{ $agency->name }}</option>
              @endforeach
            </select>
            @error('travel_agency_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="contact_id" class="form-label">Contact</label>
            <select name="contact_id" id="contact_id" class="form-select select2 @error('contact_id') is-invalid @enderror">
              <option value="">— None —</option>
              @foreach ($contacts as $contact)
                <option value="{{ $contact->id }}" @selected(old('contact_id') == $contact->id)>{{ $contact->name }}</option>
              @endforeach
            </select>
            @error('contact_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="arrival" class="form-label">Arrival <span class="text-danger">*</span></label>
            <input type="date" name="arrival" id="arrival" class="form-control @error('arrival') is-invalid @enderror" value="{{ old('arrival') }}" required>
            @error('arrival')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="departure" class="form-label">Departure <span class="text-danger">*</span></label>
            <input type="date" name="departure" id="departure" class="form-control @error('departure') is-invalid @enderror" value="{{ old('departure') }}" required>
            @error('departure')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="nights" class="form-label">Nights</label>
            <input type="number" name="nights" id="nights" min="1" class="form-control @error('nights') is-invalid @enderror" value="{{ old('nights') }}">
            @error('nights')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select select2 @error('status') is-invalid @enderror">
              @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected(old('status', 'Provisional') === $status)>{{ $status }}</option>
              @endforeach
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="payment_status_display" class="form-label">Payment display</label>
            <select name="payment_status_display" id="payment_status_display" class="form-select select2 @error('payment_status_display') is-invalid @enderror">
              <option value="">— None —</option>
              @foreach (\App\Enums\PaymentDisplayStatus::values() as $ps)
                <option value="{{ $ps }}" @selected(old('payment_status_display') === $ps)>{{ $ps }}</option>
              @endforeach
            </select>
            @error('payment_status_display')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="revenue" class="form-label">Revenue (£)</label>
            <input type="number" step="0.01" name="revenue" id="revenue" min="0" class="form-control @error('revenue') is-invalid @enderror" value="{{ old('revenue') }}">
            @error('revenue')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="rooms" class="form-label">Rooms</label>
            <input type="number" name="rooms" id="rooms" min="0" class="form-control @error('rooms') is-invalid @enderror" value="{{ old('rooms') }}">
            @error('rooms')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="pax" class="form-label">Pax</label>
            <input type="number" name="pax" id="pax" min="0" class="form-control @error('pax') is-invalid @enderror" value="{{ old('pax') }}">
            @error('pax')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="client" class="form-label">Client</label>
            <input type="text" name="client" id="client" class="form-control @error('client') is-invalid @enderror" value="{{ old('client') }}">
            @error('client')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>
    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-accent">Save booking</button>
      <a href="{{ route('group-bookings.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
@endsection
