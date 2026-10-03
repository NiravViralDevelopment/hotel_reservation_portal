@extends('layouts.app')

@section('title', 'Add travel agency')
@section('page', 'travel-agencies')

@push('styles')
<style>
  .agency-form-layout .agency-section-title {
    font-size: 0.9375rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .agency-form-layout .agency-section-title i { color: var(--brand-accent); }
  .agency-form-layout .agency-sticky-actions {
    position: sticky;
    bottom: 0;
    z-index: 5;
    display: flex;
    gap: 0.5rem;
    padding: 0.85rem 0;
    margin-top: 0.5rem;
    background: linear-gradient(180deg, transparent, var(--bg-body) 28%);
  }
  .travel-agency-form .select2-container--bootstrap-5 .select2-selection.is-invalid {
    border-color: var(--bs-form-invalid-border-color, #dc3545);
  }
</style>
@endpush

@section('content')
<div class="agency-form-layout">
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('travel-agencies.index') }}">Travel agencies</a></li>
        <li class="breadcrumb-item active">Add agency</li>
      </ol>
    </nav>
    <h1 class="page-title">Add travel agency</h1>
    <p class="page-subtitle mb-0">Create an agency partner profile. Fields marked <span class="text-danger">*</span> are required.</p>
  </div>

  <form method="POST" action="{{ route('travel-agencies.store') }}" class="travel-agency-form" novalidate>
    @csrf

    <div class="card mb-4">
      <div class="card-header">
        <h2 class="agency-section-title"><i class="bi bi-briefcase"></i> Agency details</h2>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3">
            <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
            <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="Enter code" maxlength="20" autocomplete="off">
            @error('code')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-5">
            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Enter agency name" maxlength="255">
            @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
            <select name="status" id="status" class="form-select select2 @error('status') is-invalid @enderror" data-placeholder="Select status">
              <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
              <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
            </select>
            @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="city" class="form-label">City</label>
            <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}" placeholder="Enter city" maxlength="255">
            @error('city')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="country" class="form-label">Country</label>
            <input type="text" name="country" id="country" class="form-control @error('country') is-invalid @enderror" value="{{ old('country') }}" placeholder="Enter country" maxlength="255">
            @error('country')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">
        <h2 class="agency-section-title"><i class="bi bi-person-lines-fill"></i> Contact</h2>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label for="contact_name" class="form-label">Contact name <span class="text-danger">*</span></label>
            <input type="text" name="contact_name" id="contact_name" class="form-control @error('contact_name') is-invalid @enderror" value="{{ old('contact_name') }}" placeholder="Enter contact name" maxlength="255">
            @error('contact_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
            <input type="text" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Enter email" maxlength="255" inputmode="email">
            @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="Enter phone" maxlength="15">
            @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="agency-sticky-actions">
      <button type="submit" class="btn btn-accent"><i class="bi bi-check-lg"></i> Save agency</button>
      <a href="{{ route('travel-agencies.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/travel-agency-validation.js') }}?v={{ @filemtime(public_path('assets/js/travel-agency-validation.js')) }}"></script>
@endpush
