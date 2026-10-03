@extends('layouts.app')

@section('title', 'Add user')
@section('page', 'users')

@push('styles')
<style>
  .user-form-layout .user-section-title {
    font-size: 0.9375rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .user-form-layout .user-section-title i { color: var(--brand-accent); }
  .user-form-layout .user-sticky-actions {
    position: sticky;
    bottom: 0;
    z-index: 5;
    display: flex;
    gap: 0.5rem;
    padding: 0.85rem 0;
    margin-top: 0.5rem;
    background: linear-gradient(180deg, transparent, var(--bg-body) 28%);
  }
</style>
@endpush

@section('content')
<div class="user-form-layout">
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
        <li class="breadcrumb-item active">Add user</li>
      </ol>
    </nav>
    <h1 class="page-title">Add user</h1>
    <p class="page-subtitle mb-0">Create a login account, set access, then save. Fields marked <span class="text-danger">*</span> are required.</p>
  </div>

  <form
    method="POST"
    action="{{ route('users.store') }}"
    class="user-form"
    novalidate
    data-password-required="1"
    data-existing-emails='@json($existingEmails)'
  >
    @csrf

    <div class="row g-4">
      <div class="col-lg-7">
        <div class="card mb-4">
          <div class="card-header">
            <h2 class="user-section-title"><i class="bi bi-person-vcard"></i> Profile</h2>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label for="name" class="form-label">Full name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Enter name" maxlength="255" autocomplete="name">
                @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="text" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Enter email" maxlength="255" inputmode="email" autocomplete="email">
                <div class="form-text">Sign-in email. Must be unique.</div>
                @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="Enter phone" maxlength="20" autocomplete="tel">
                @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label for="job_title" class="form-label">Job title</label>
                <input type="text" name="job_title" id="job_title" class="form-control @error('job_title') is-invalid @enderror" value="{{ old('job_title') }}" placeholder="Enter job title" maxlength="255">
                @error('job_title')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                  <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
                  <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
                </select>
                <div class="form-text">Inactive users cannot sign in.</div>
                @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>
        </div>

        <div class="card mb-4">
          <div class="card-header">
            <h2 class="user-section-title"><i class="bi bi-shield-lock"></i> Password</h2>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                <div class="password-field">
                  <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter password" autocomplete="new-password">
                  <button type="button" class="password-toggle" data-password-toggle="password" aria-label="Show password" title="Show password">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
                <div class="form-text">Minimum 8 characters.</div>
                @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label for="password_confirmation" class="form-label">Confirm password <span class="text-danger">*</span></label>
                <div class="password-field">
                  <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Enter confirm password" autocomplete="new-password">
                  <button type="button" class="password-toggle" data-password-toggle="password_confirmation" aria-label="Show password" title="Show password">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="card mb-4">
          <div class="card-header">
            <h2 class="user-section-title"><i class="bi bi-person-badge"></i> Roles</h2>
          </div>
          <div class="card-body">
            <p class="small text-secondary mb-3">Select one or more roles.</p>
            @forelse ($roles as $role)
              <div class="form-check mb-2">
                <input
                  class="form-check-input"
                  type="checkbox"
                  name="roles[]"
                  value="{{ $role->name }}"
                  id="role_{{ $role->id }}"
                  @checked(in_array($role->name, old('roles', []), true))
                >
                <label class="form-check-label" for="role_{{ $role->id }}">{{ $role->name }}</label>
              </div>
            @empty
              <p class="text-secondary mb-0">No roles defined.</p>
            @endforelse
            @error('roles')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="card mb-4">
          <div class="card-header">
            <h2 class="user-section-title"><i class="bi bi-building"></i> Hotel access</h2>
          </div>
          <div class="card-body" style="max-height: 360px; overflow-y: auto;">
            <p class="small text-secondary mb-3">Select hotels this user can access.</p>
            @forelse ($hotels as $hotel)
              <div class="form-check mb-2">
                <input
                  class="form-check-input"
                  type="checkbox"
                  name="hotels[]"
                  value="{{ $hotel->id }}"
                  id="hotel_{{ $hotel->id }}"
                  @checked(in_array($hotel->id, array_map('intval', old('hotels', [])), true))
                >
                <label class="form-check-label" for="hotel_{{ $hotel->id }}">
                  {{ $hotel->name }} <span class="text-muted">({{ $hotel->code }})</span>
                </label>
              </div>
            @empty
              <p class="text-secondary mb-0">No hotels available.</p>
            @endforelse
            @error('hotels')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="user-sticky-actions">
      <button type="submit" class="btn btn-accent"><i class="bi bi-check-lg"></i> Save user</button>
      <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/user-validation.js') }}?v={{ @filemtime(public_path('assets/js/user-validation.js')) }}"></script>
@endpush
