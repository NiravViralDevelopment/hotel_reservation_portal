@extends('layouts.app')

@section('title', 'Edit travel agency')
@section('page', 'travel-agencies')

@section('content')
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('travel-agencies.index') }}">Travel agencies</a></li>
        <li class="breadcrumb-item active">{{ $travelAgency->code }}</li>
      </ol>
    </nav>
    <h1 class="page-title">Edit {{ $travelAgency->name }}</h1>
  </div>

  <form method="POST" action="{{ route('travel-agencies.update', $travelAgency) }}">
    @csrf
    @method('PUT')
    <div class="card">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3">
            <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
            <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $travelAgency->code) }}" placeholder="Enter code" required>
            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-9">
            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $travelAgency->name) }}" placeholder="Enter name" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="contact_name" class="form-label">Contact name</label>
            <input type="text" name="contact_name" id="contact_name" class="form-control @error('contact_name') is-invalid @enderror" value="{{ old('contact_name', $travelAgency->contact_name) }}" placeholder="Enter contact name">
            @error('contact_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $travelAgency->email) }}" placeholder="Enter email">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $travelAgency->phone) }}" placeholder="Enter phone">
            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="city" class="form-label">City</label>
            <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $travelAgency->city) }}" placeholder="Enter city">
            @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="country" class="form-label">Country</label>
            <input type="text" name="country" id="country" class="form-control @error('country') is-invalid @enderror" value="{{ old('country', $travelAgency->country) }}" placeholder="Enter country">
            @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
            <select name="status" id="status" class="form-select select2 @error('status') is-invalid @enderror" required>
              <option value="active" @selected(old('status', $travelAgency->status) === 'active')>Active</option>
              <option value="inactive" @selected(old('status', $travelAgency->status) === 'inactive')>Inactive</option>
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>
    <div class="d-flex gap-2 mt-4">
      <button type="submit" class="btn btn-accent">Update agency</button>
      <a href="{{ route('travel-agencies.show', $travelAgency) }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
@endsection
