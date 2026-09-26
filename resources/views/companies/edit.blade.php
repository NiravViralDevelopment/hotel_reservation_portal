@extends('layouts.app')

@section('title', 'Edit company')
@section('page', 'companies')

@section('content')
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('companies.index') }}">Companies</a></li>
        <li class="breadcrumb-item active">{{ $company->name }}</li>
      </ol>
    </nav>
    <h1 class="page-title">Edit company</h1>
  </div>

  <form method="POST" action="{{ route('companies.update', $company) }}">
    @csrf
    @method('PUT')
    <div class="card">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-8">
            <label for="name" class="form-label">Company name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $company->name) }}" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="reg_number" class="form-label">Registration number</label>
            <input type="text" name="reg_number" id="reg_number" class="form-control @error('reg_number') is-invalid @enderror" value="{{ old('reg_number', $company->reg_number) }}">
            @error('reg_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="city" class="form-label">City</label>
            <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $company->city) }}">
            @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="country" class="form-label">Country</label>
            <input type="text" name="country" id="country" class="form-control @error('country') is-invalid @enderror" value="{{ old('country', $company->country) }}">
            @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
              <option value="active" @selected(old('status', $company->status) === 'active')>Active</option>
              <option value="inactive" @selected(old('status', $company->status) === 'inactive')>Inactive</option>
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <label for="address" class="form-label">Address</label>
            <textarea name="address" id="address" rows="2" class="form-control @error('address') is-invalid @enderror">{{ old('address', $company->address) }}</textarea>
            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <label for="notes" class="form-label">Notes</label>
            <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $company->notes) }}</textarea>
            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>
    <div class="d-flex gap-2 mt-4">
      <button type="submit" class="btn btn-accent">Update company</button>
      <a href="{{ route('companies.show', $company) }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
@endsection
