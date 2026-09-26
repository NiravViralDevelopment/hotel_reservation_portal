@extends('layouts.guest')

@section('title', 'Sign In')
@section('meta_description', 'Hotel Group Booking Management System — Secure Login')

@section('content')
  <div class="login-page">
    <div class="login-card">
      <div class="login-brand">
        <div class="login-brand-icon">
          <i class="bi bi-building"></i>
        </div>
        <h1>Hotel Group Booking</h1>
        <p>Management System &mdash; United Kingdom</p>
      </div>

      <form method="POST" action="{{ route('login') }}" id="loginForm" class="login-form needs-validation" novalidate data-laravel-auth>
        @csrf

        <div class="form-floating mb-3">
          <input
            type="email"
            class="form-control @error('email') is-invalid @enderror"
            id="loginEmail"
            name="email"
            placeholder="name@company.co.uk"
            value="{{ old('email', 'admin@hotelgroup.co.uk') }}"
            required
            autofocus
            autocomplete="username"
          >
          <label for="loginEmail"><i class="bi bi-envelope me-1"></i> Email Address</label>
          @error('email')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @else
            <div class="invalid-feedback">Please enter a valid email address.</div>
          @enderror
        </div>

        <div class="form-floating mb-3">
          <input
            type="password"
            class="form-control @error('password') is-invalid @enderror"
            id="loginPassword"
            name="password"
            placeholder="Password"
            required
            minlength="6"
            autocomplete="current-password"
          >
          <label for="loginPassword"><i class="bi bi-lock me-1"></i> Password</label>
          @error('password')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @else
            <div class="invalid-feedback">Password is required (minimum 6 characters).</div>
          @enderror
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="rememberMe" name="remember" value="1" @checked(old('remember', true))>
            <label class="form-check-label small text-secondary" for="rememberMe">Remember me</label>
          </div>
          <a href="{{ route('password.request') }}" class="small">Forgot password?</a>
        </div>

        <button type="submit" class="btn btn-brand w-100 mb-3">
          <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
        </button>

        <div class="text-center">
          <button type="button" class="btn btn-link btn-sm text-secondary" data-theme-toggle title="Toggle dark/light mode">
            <i class="bi bi-moon-fill"></i> Toggle Theme
          </button>
        </div>
      </form>

      <div class="login-footer">
        <p class="mb-1">&copy; {{ date('Y') }} Hotel Group Holdings Ltd. All rights reserved.</p>
        <p class="mb-0">Registered in England &amp; Wales &middot; Company No. 08472931</p>
      </div>
    </div>
  </div>
@endsection
