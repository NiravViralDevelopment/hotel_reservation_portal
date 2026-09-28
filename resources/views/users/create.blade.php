@extends('layouts.app')

@section('title', 'Add user')
@section('page', 'users')

@section('content')
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
          <li class="breadcrumb-item active">Add user</li>
        </ol>
      </nav>
      <h1 class="page-title">Add user</h1>
      <p class="page-subtitle">Create a system user and assign roles and hotels.</p>
    </div>
  </div>

  <form method="POST" action="{{ route('users.store') }}">
    @csrf
    <div class="card mb-4">
      <div class="card-header">Account details</div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="job_title" class="form-label">Job title</label>
            <input type="text" name="job_title" id="job_title" class="form-control @error('job_title') is-invalid @enderror" value="{{ old('job_title') }}">
            @error('job_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
            <select name="status" id="status" class="form-select select2 @error('status') is-invalid @enderror" required>
              <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
              <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
            <div class="password-field">
              <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
              <button type="button" class="password-toggle" data-password-toggle="password" aria-label="Show password" title="Show password">
                <i class="bi bi-eye"></i>
              </button>
            </div>
            @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="password_confirmation" class="form-label">Confirm password <span class="text-danger">*</span></label>
            <div class="password-field">
              <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required autocomplete="new-password">
              <button type="button" class="password-toggle" data-password-toggle="password_confirmation" aria-label="Show password" title="Show password">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4 mb-4">
      <div class="col-lg-6">
        <div class="card h-100">
          <div class="card-header">Roles</div>
          <div class="card-body">
            @forelse ($roles as $role)
              <div class="form-check">
                <input class="form-check-input @error('roles') is-invalid @enderror" type="checkbox" name="roles[]" value="{{ $role->name }}" id="role_{{ $role->id }}"
                  @checked(in_array($role->name, old('roles', []), true))>
                <label class="form-check-label" for="role_{{ $role->id }}">{{ $role->name }}</label>
              </div>
            @empty
              <p class="text-secondary mb-0">No roles defined.</p>
            @endforelse
            @error('roles')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card h-100">
          <div class="card-header">Hotel access</div>
          <div class="card-body" style="max-height: 280px; overflow-y: auto;">
            @forelse ($hotels as $hotel)
              <div class="form-check">
                <input class="form-check-input @error('hotels') is-invalid @enderror" type="checkbox" name="hotels[]" value="{{ $hotel->id }}" id="hotel_{{ $hotel->id }}"
                  @checked(in_array($hotel->id, array_map('intval', old('hotels', [])), true))>
                <label class="form-check-label" for="hotel_{{ $hotel->id }}">{{ $hotel->name }} <span class="text-muted">({{ $hotel->code }})</span></label>
              </div>
            @empty
              <p class="text-secondary mb-0">No hotels available.</p>
            @endforelse
            @error('hotels')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-accent">Save user</button>
      <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
@endsection
