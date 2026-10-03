@extends('layouts.app')

@section('title', 'Edit company')
@section('page', 'companies')

@push('styles')
<style>
  .company-form-layout .company-section-title {
    font-size: 0.9375rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .company-form-layout .company-section-title i { color: var(--brand-accent); }
  .company-form-layout .company-sticky-actions {
    position: sticky;
    bottom: 0;
    z-index: 5;
    display: flex;
    gap: 0.5rem;
    padding: 0.85rem 0;
    margin-top: 0.5rem;
    background: linear-gradient(180deg, transparent, var(--bg-body) 28%);
  }
  .company-form .select2-container--bootstrap-5 .select2-selection.is-invalid {
    border-color: var(--bs-form-invalid-border-color, #dc3545);
  }
</style>
@endpush

@section('content')
<div class="company-form-layout">
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('companies.index') }}">Companies</a></li>
        <li class="breadcrumb-item"><a href="{{ route('companies.show', $company) }}">{{ $company->name }}</a></li>
        <li class="breadcrumb-item active">Edit</li>
      </ol>
    </nav>
    <h1 class="page-title">Edit {{ $company->name }}</h1>
    <p class="page-subtitle mb-0">Update company profile and addresses. Fields marked <span class="text-danger">*</span> are required.</p>
  </div>

  <form method="POST" action="{{ route('companies.update', $company) }}" class="company-form" novalidate>
    @csrf
    @method('PUT')

    <div class="card mb-4">
      <div class="card-header">
        <h2 class="company-section-title"><i class="bi bi-building"></i> Company details</h2>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-8">
            <label for="name" class="form-label">Company name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $company->name) }}" placeholder="Enter company name" maxlength="255">
            @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
            <select name="status" id="status" class="form-select select2 @error('status') is-invalid @enderror" data-placeholder="Select status">
              <option value="active" @selected(old('status', $company->status) === 'active')>Active</option>
              <option value="inactive" @selected(old('status', $company->status) === 'inactive')>Inactive</option>
            </select>
            @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="reg_number" class="form-label">Registration number <span class="text-danger">*</span></label>
            <input type="text" name="reg_number" id="reg_number" class="form-control @error('reg_number') is-invalid @enderror" value="{{ old('reg_number', $company->reg_number) }}" placeholder="Enter registration number" maxlength="50">
            @error('reg_number')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="vat_number" class="form-label">VAT number <span class="text-danger">*</span></label>
            <input type="text" name="vat_number" id="vat_number" class="form-control @error('vat_number') is-invalid @enderror" value="{{ old('vat_number', $company->vat_number) }}" placeholder="Enter VAT number" maxlength="50">
            @error('vat_number')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="city" class="form-label">City</label>
            <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $company->city) }}" placeholder="Enter city" maxlength="255">
            @error('city')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="country" class="form-label">Country</label>
            <input type="text" name="country" id="country" class="form-control @error('country') is-invalid @enderror" value="{{ old('country', $company->country ?: 'United Kingdom') }}" readonly>
            @error('country')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">
        <h2 class="company-section-title"><i class="bi bi-geo-alt"></i> Addresses</h2>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="registered_address" class="form-label">Registered address <span class="text-danger">*</span></label>
            <textarea name="registered_address" id="registered_address" rows="3" class="form-control @error('registered_address') is-invalid @enderror" placeholder="Enter registered address">{{ old('registered_address', $company->registered_address ?: $company->address) }}</textarea>
            @error('registered_address')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="trading_address" class="form-label">Trading address <span class="text-danger">*</span></label>
            <textarea name="trading_address" id="trading_address" rows="3" class="form-control @error('trading_address') is-invalid @enderror" placeholder="Enter trading address">{{ old('trading_address', $company->trading_address) }}</textarea>
            @error('trading_address')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">
        <h2 class="company-section-title"><i class="bi bi-sticky"></i> Notes</h2>
      </div>
      <div class="card-body">
        <label for="notes" class="form-label">Notes</label>
        <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror" placeholder="Enter notes">{{ old('notes', $company->notes) }}</textarea>
        @error('notes')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
    </div>

    <div class="company-sticky-actions">
      <button type="submit" class="btn btn-accent"><i class="bi bi-check-lg"></i> Update company</button>
      <a href="{{ route('companies.show', $company) }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/company-validation.js') }}?v={{ @filemtime(public_path('assets/js/company-validation.js')) }}"></script>
@endpush
