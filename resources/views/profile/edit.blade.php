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
    <p class="page-subtitle">{{ $user->email }}</p>
  </div>

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header">Profile details</div>
        <div class="card-body">
          <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
              <div class="col-md-6">
                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" placeholder="Enter name" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" class="form-control" value="{{ $user->email }}" readonly disabled>
                <div class="form-text">Email can only be changed by an administrator.</div>
              </div>
              <div class="col-md-6">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" placeholder="Enter phone">
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label for="job_title" class="form-label">Job title</label>
                <input type="text" name="job_title" id="job_title" class="form-control @error('job_title') is-invalid @enderror" value="{{ old('job_title', $user->job_title) }}" placeholder="Enter job title">
                @error('job_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-accent">Save profile</button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class="card mt-4">
        <div class="card-header">Change password</div>
        <div class="card-body">
          <form method="POST" action="{{ route('profile.password') }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
              <div class="col-md-6">
                <label for="current_password" class="form-label">Current password <span class="text-danger">*</span></label>
                <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror" required autocomplete="current-password">
                @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6"></div>
              <div class="col-md-6">
                <label for="password" class="form-label">New password <span class="text-danger">*</span></label>
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password" minlength="6">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label for="password_confirmation" class="form-label">Confirm password <span class="text-danger">*</span></label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required autocomplete="new-password" minlength="6">
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
