@extends('layouts.app')

@section('title', 'Edit hotel')
@section('page', 'hotels')

@push('styles')
<style>
  .hotel-form-layout .hotel-section-title {
    font-size: 0.9375rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .hotel-form-layout .hotel-section-title i { color: var(--brand-accent); }
  .hotel-form-layout .hotel-sticky-actions {
    position: sticky;
    bottom: 0;
    z-index: 5;
    display: flex;
    gap: 0.5rem;
    padding: 0.85rem 0;
    margin-top: 0.5rem;
    background: linear-gradient(180deg, transparent, var(--bg-body) 28%);
  }
  .hotel-form .select2-container--bootstrap-5 .select2-selection.is-invalid {
    border-color: var(--bs-form-invalid-border-color, #dc3545);
  }
</style>
@endpush

@section('content')
<div class="hotel-form-layout">
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('hotels.index') }}">Hotels</a></li>
        <li class="breadcrumb-item"><a href="{{ route('hotels.show', $hotel) }}">{{ $hotel->code }}</a></li>
        <li class="breadcrumb-item active">Edit</li>
      </ol>
    </nav>
    <h1 class="page-title">Edit {{ $hotel->name }}</h1>
    <p class="page-subtitle mb-0">Update hotel profile and contact details. Fields marked <span class="text-danger">*</span> are required.</p>
  </div>

  <form method="POST" action="{{ route('hotels.update', $hotel) }}" class="hotel-form" novalidate data-existing-pairs='@json($existingPairs)'>
    @csrf
    @method('PUT')

    <div class="card mb-4">
      <div class="card-header">
        <h2 class="hotel-section-title"><i class="bi bi-building"></i> Hotel details</h2>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3">
            <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
            <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $hotel->code) }}" placeholder="Enter code" maxlength="20" autocomplete="off">
            <div class="form-text">Can be reused with a different hotel name.</div>
            @error('code')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-5">
            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $hotel->name) }}" placeholder="Enter hotel name" maxlength="255">
            @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="company_id" class="form-label">Company <span class="text-danger">*</span></label>
            <select name="company_id" id="company_id" class="form-select select2 @error('company_id') is-invalid @enderror" data-placeholder="Select company">
              <option value="">Select company</option>
              @foreach ($companies as $company)
                <option value="{{ $company->id }}" @selected(old('company_id', $hotel->company_id) == $company->id)>{{ $company->name }}</option>
              @endforeach
            </select>
            @error('company_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
            <select name="status" id="status" class="form-select select2 @error('status') is-invalid @enderror" data-placeholder="Select status">
              <option value="active" @selected(old('status', $hotel->status) === 'active')>Active</option>
              <option value="inactive" @selected(old('status', $hotel->status) === 'inactive')>Inactive</option>
            </select>
            @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="rooms" class="form-label">Rooms <span class="text-danger">*</span></label>
            <input type="text" name="rooms" id="rooms" inputmode="numeric" class="form-control @error('rooms') is-invalid @enderror" value="{{ old('rooms', $hotel->rooms) }}" placeholder="Enter number of rooms" maxlength="5" autocomplete="off">
            <div class="form-text">Numbers only.</div>
            @error('rooms')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="manager_name" class="form-label">Manager</label>
            <input type="text" name="manager_name" id="manager_name" class="form-control @error('manager_name') is-invalid @enderror" value="{{ old('manager_name', $hotel->manager_name) }}" placeholder="Enter manager name" maxlength="255">
            @error('manager_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">
        <h2 class="hotel-section-title"><i class="bi bi-geo-alt"></i> Location</h2>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="city" class="form-label">City <span class="text-danger">*</span></label>
            <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $hotel->city) }}" placeholder="Enter city" maxlength="255">
            @error('city')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
            <input type="text" name="country" id="country" class="form-control @error('country') is-invalid @enderror" value="United Kingdom" readonly>
            <div class="form-text">Fixed to United Kingdom.</div>
            @error('country')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">
        <h2 class="hotel-section-title"><i class="bi bi-telephone"></i> Contact</h2>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $hotel->phone) }}" placeholder="Enter phone" maxlength="15" autocomplete="tel">
            @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
            <input type="text" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $hotel->email) }}" placeholder="Enter email" maxlength="255" inputmode="email" autocomplete="email">
            @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">
        <h2 class="hotel-section-title"><i class="bi bi-sticky"></i> Notes</h2>
      </div>
      <div class="card-body">
        <label for="notes" class="form-label">Notes</label>
        <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror" placeholder="Enter notes">{{ old('notes', $hotel->notes) }}</textarea>
        @error('notes')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
    </div>

    <div class="hotel-sticky-actions">
      <button type="submit" class="btn btn-accent"><i class="bi bi-check-lg"></i> Update hotel</button>
      <a href="{{ route('hotels.show', $hotel) }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/hotel-validation.js') }}?v={{ @filemtime(public_path('assets/js/hotel-validation.js')) }}"></script>
@endpush
