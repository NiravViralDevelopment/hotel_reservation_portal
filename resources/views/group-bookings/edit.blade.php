@extends('layouts.app')

@section('title', 'Edit booking')
@section('page', 'group-bookings')

@section('content')
  @php $b = $groupBooking; @endphp
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('group-bookings.index') }}">Group bookings</a></li>
        <li class="breadcrumb-item active">{{ $b->block_id }}</li>
      </ol>
    </nav>
    <h1 class="page-title">Edit {{ $b->block_id }}</h1>
  </div>

  <form method="POST" action="{{ route('group-bookings.update', $b) }}">
    @csrf
    @method('PUT')
    <div class="card mb-4">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label for="block_id" class="form-label">Block ID <span class="text-danger">*</span></label>
            <input type="text" name="block_id" id="block_id" class="form-control @error('block_id') is-invalid @enderror" value="{{ old('block_id', $b->block_id) }}" required>
            @error('block_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-8">
            <label for="group_name" class="form-label">Group name <span class="text-danger">*</span></label>
            <input type="text" name="group_name" id="group_name" class="form-control @error('group_name') is-invalid @enderror" value="{{ old('group_name', $b->group_name) }}" required>
            @error('group_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="hotel_id" class="form-label">Hotel</label>
            <select name="hotel_id" id="hotel_id" class="form-select select2 @error('hotel_id') is-invalid @enderror">
              <option value="">— None —</option>
              @foreach ($hotels as $hotel)
                <option value="{{ $hotel->id }}" @selected(old('hotel_id', $b->hotel_id) == $hotel->id)>{{ $hotel->name }}</option>
              @endforeach
            </select>
            @error('hotel_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="company_id" class="form-label">Company</label>
            <select name="company_id" id="company_id" class="form-select select2 @error('company_id') is-invalid @enderror">
              <option value="">— None —</option>
              @foreach ($companies as $company)
                <option value="{{ $company->id }}" @selected(old('company_id', $b->company_id) == $company->id)>{{ $company->name }}</option>
              @endforeach
            </select>
            @error('company_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="travel_agency_id" class="form-label">Travel agency</label>
            <select name="travel_agency_id" id="travel_agency_id" class="form-select select2 @error('travel_agency_id') is-invalid @enderror">
              <option value="">— None —</option>
              @foreach ($travelAgencies as $agency)
                <option value="{{ $agency->id }}" @selected(old('travel_agency_id', $b->travel_agency_id) == $agency->id)>{{ $agency->name }}</option>
              @endforeach
            </select>
            @error('travel_agency_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="contact_id" class="form-label">Contact</label>
            <select name="contact_id" id="contact_id" class="form-select select2 @error('contact_id') is-invalid @enderror">
              <option value="">— None —</option>
              @foreach ($contacts as $contact)
                <option value="{{ $contact->id }}" @selected(old('contact_id', $b->contact_id) == $contact->id)>{{ $contact->name }}</option>
              @endforeach
            </select>
            @error('contact_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="arrival" class="form-label">Arrival <span class="text-danger">*</span></label>
            <input type="date" name="arrival" id="arrival" class="form-control @error('arrival') is-invalid @enderror" value="{{ old('arrival', $b->arrival?->format('Y-m-d')) }}" required>
            @error('arrival')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="departure" class="form-label">Departure <span class="text-danger">*</span></label>
            <input type="date" name="departure" id="departure" class="form-control @error('departure') is-invalid @enderror" value="{{ old('departure', $b->departure?->format('Y-m-d')) }}" required>
            @error('departure')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="nights" class="form-label">Nights</label>
            <input type="number" name="nights" id="nights" class="form-control @error('nights') is-invalid @enderror" value="{{ old('nights', $b->nights) }}">
            @error('nights')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select select2 @error('status') is-invalid @enderror">
              @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected(old('status', $b->status?->value ?? $b->status) === $status)>{{ $status }}</option>
              @endforeach
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="payment_status_display" class="form-label">Payment display</label>
            <select name="payment_status_display" id="payment_status_display" class="form-select select2 @error('payment_status_display') is-invalid @enderror">
              <option value="">— None —</option>
              @foreach (\App\Enums\PaymentDisplayStatus::values() as $ps)
                <option value="{{ $ps }}" @selected(old('payment_status_display', $b->payment_status_display?->value ?? $b->payment_status_display) === $ps)>{{ $ps }}</option>
              @endforeach
            </select>
            @error('payment_status_display')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="revenue" class="form-label">Revenue (£)</label>
            <input type="number" step="0.01" name="revenue" id="revenue" class="form-control @error('revenue') is-invalid @enderror" value="{{ old('revenue', $b->revenue) }}">
            @error('revenue')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="rooms" class="form-label">Rooms</label>
            <input type="number" name="rooms" id="rooms" class="form-control @error('rooms') is-invalid @enderror" value="{{ old('rooms', $b->rooms) }}">
            @error('rooms')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="pax" class="form-label">Pax</label>
            <input type="number" name="pax" id="pax" class="form-control @error('pax') is-invalid @enderror" value="{{ old('pax', $b->pax) }}">
            @error('pax')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="client" class="form-label">Client</label>
            <input type="text" name="client" id="client" class="form-control @error('client') is-invalid @enderror" value="{{ old('client', $b->client) }}">
            @error('client')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $b->email) }}">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <label for="internal_notes" class="form-label">Internal notes</label>
            <textarea name="internal_notes" id="internal_notes" rows="2" class="form-control @error('internal_notes') is-invalid @enderror">{{ old('internal_notes', $b->internal_notes) }}</textarea>
            @error('internal_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <label for="update_notes" class="form-label">Update notes</label>
            <textarea name="update_notes" id="update_notes" rows="2" class="form-control @error('update_notes') is-invalid @enderror">{{ old('update_notes', $b->update_notes) }}</textarea>
            @error('update_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>
    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-accent">Update booking</button>
      <a href="{{ route('group-bookings.show', $b) }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
@endsection
