@extends('layouts.app')

@section('title', 'Profile')
@section('page', 'profile')

@section('content')
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Profile</li>
      </ol>
    </nav>
    <h1 class="page-title">My profile</h1>
      </div>

  <div class="row g-4">
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header">Profile details</div>
        <div class="card-body">
          <form method="POST" action="{{ route('profile.update') }}" id="profile-details-form" class="profile-form" novalidate>
            @csrf
            @method('PUT')
            <div class="row g-3">
              <div class="col-md-6">
                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                <input
                  type="text"
                  name="name"
                  id="name"
                  class="form-control"
                  value="{{ old('name', $user->name) }}"
                  placeholder="Enter name"
                  maxlength="30"
                  autocomplete="name"
                  required
                >
              </div>
              <div class="col-md-6">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" class="form-control" value="{{ $user->email }}" readonly disabled>
                <div class="form-text">Email can only be changed by an administrator.</div>
              </div>
              <div class="col-md-6">
                <label for="phone" class="form-label">Phone</label>
                <input
                  type="text"
                  name="phone"
                  id="phone"
                  class="form-control"
                  value="{{ old('phone', $user->phone) }}"
                  placeholder="Enter phone"
                  inputmode="tel"
                  maxlength="20"
                  autocomplete="tel"
                >
              </div>
              <div class="col-md-6">
                <label for="job_title" class="form-label">Job title</label>
                <input
                  type="text"
                  name="job_title"
                  id="job_title"
                  class="form-control"
                  value="{{ old('job_title', $user->job_title) }}"
                  placeholder="Enter job title"
                  maxlength="255"
                >
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-accent">Save profile</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header">Change password</div>
        <div class="card-body">
          <form method="POST" action="{{ route('profile.password') }}" id="profile-password-form" class="profile-form" novalidate>
            @csrf
            @method('PUT')
            <div class="row g-3">
              <div class="col-12">
                <label for="current_password" class="form-label">Current password <span class="text-danger">*</span></label>
                <div class="password-field">
                  <input
                    type="password"
                    name="current_password"
                    id="current_password"
                    class="form-control"
                    placeholder="Enter current password"
                    required
                    autocomplete="current-password"
                  >
                  <button type="button" class="password-toggle" data-password-toggle="current_password" aria-label="Show password" title="Show password">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
              </div>
              <div class="col-md-6">
                <label for="password" class="form-label">New password <span class="text-danger">*</span></label>
                <div class="password-field">
                  <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-control"
                    placeholder="Enter new password"
                    required
                    autocomplete="new-password"
                    minlength="8"
                  >
                  <button type="button" class="password-toggle" data-password-toggle="password" aria-label="Show password" title="Show password">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
              </div>
              <div class="col-md-6">
                <label for="password_confirmation" class="form-label">Confirm password <span class="text-danger">*</span></label>
                <div class="password-field">
                  <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    class="form-control"
                    placeholder="Confirm new password"
                    required
                    autocomplete="new-password"
                    minlength="8"
                  >
                  <button type="button" class="password-toggle" data-password-toggle="password_confirmation" aria-label="Show password" title="Show password">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-accent">Change password</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script src="{{ asset('assets/js/profile-validation.js') }}?v={{ @filemtime(public_path('assets/js/profile-validation.js')) }}"></script>
@endpush
