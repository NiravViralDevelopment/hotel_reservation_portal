@extends('layouts.app')

@section('title', 'Edit user')
@section('page', 'users')

@section('content')
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
          <li class="breadcrumb-item active">{{ $user->name }}</li>
        </ol>
      </nav>
      <h1 class="page-title">Edit user</h1>
      <p class="page-subtitle">{{ $user->email }}</p>
    </div>
  </div>

  <form method="POST" action="{{ route('users.update', $user) }}">
    @csrf
    @method('PUT')
    <div class="card mb-4">
      <div class="card-header">Account details</div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="job_title" class="form-label">Job title</label>
            <input type="text" name="job_title" id="job_title" class="form-control @error('job_title') is-invalid @enderror" value="{{ old('job_title', $user->job_title) }}">
            @error('job_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="department" class="form-label">Department</label>
            <input type="text" name="department" id="department" class="form-control @error('department') is-invalid @enderror" value="{{ old('department', $user->department) }}">
            @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
              <option value="active" @selected(old('status', $user->status) === 'active')>Active</option>
              <option value="inactive" @selected(old('status', $user->status) === 'inactive')>Inactive</option>
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="password" class="form-label">New password</label>
            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text">Leave blank to keep current password.</div>
          </div>
          <div class="col-md-4">
            <label for="password_confirmation" class="form-label">Confirm new password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" autocomplete="new-password">
          </div>
        </div>
      </div>
    </div>

    @php
      $selectedRoles = old('roles', $user->roles->pluck('name')->all());
      $selectedHotels = old('hotels', $user->hotels->pluck('id')->all());
    @endphp

    <div class="row g-4 mb-4">
      <div class="col-lg-6">
        <div class="card h-100">
          <div class="card-header">Roles</div>
          <div class="card-body">
            @forelse ($roles as $role)
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->name }}" id="role_{{ $role->id }}"
                  @checked(in_array($role->name, $selectedRoles, true))>
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
                <input class="form-check-input" type="checkbox" name="hotels[]" value="{{ $hotel->id }}" id="hotel_{{ $hotel->id }}"
                  @checked(in_array($hotel->id, array_map('intval', $selectedHotels), true))>
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
      <button type="submit" class="btn btn-accent">Update user</button>
      <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
@endsection
