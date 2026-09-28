@extends('layouts.guest')

@section('title', 'Set New Password')

@section('content')
  <div class="login-page">
    <div class="login-card">
      <div class="login-brand">
        <div class="login-brand-icon">
          <i class="bi bi-shield-lock"></i>
        </div>
        <h1>Set New Password</h1>
        <p>Choose a new password for your account</p>
      </div>

      <form method="POST" action="{{ route('password.update') }}" class="login-form">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-floating mb-3">
          <input
            type="email"
            class="form-control @error('email') is-invalid @enderror"
            id="email"
            name="email"
            value="{{ old('email', $email) }}"
            required
            autofocus
          >
          <label for="email"><i class="bi bi-envelope me-1"></i> Email Address</label>
          @error('email')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-floating mb-3 password-field">
          <input
            type="password"
            class="form-control @error('password') is-invalid @enderror"
            id="password"
            name="password"
            placeholder="Password"
            required
            minlength="6"
            autocomplete="new-password"
          >
          <label for="password"><i class="bi bi-lock me-1"></i> New Password</label>
          <button type="button" class="password-toggle" data-password-toggle="password" aria-label="Show password" title="Show password">
            <i class="bi bi-eye"></i>
          </button>
          @error('password')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-floating mb-4 password-field">
          <input
            type="password"
            class="form-control"
            id="password_confirmation"
            name="password_confirmation"
            placeholder="Confirm password"
            required
            minlength="6"
            autocomplete="new-password"
          >
          <label for="password_confirmation"><i class="bi bi-lock-fill me-1"></i> Confirm Password</label>
          <button type="button" class="password-toggle" data-password-toggle="password_confirmation" aria-label="Show password" title="Show password">
            <i class="bi bi-eye"></i>
          </button>
        </div>

        <button type="submit" class="btn btn-brand w-100 mb-3">
          <i class="bi bi-check2-circle me-2"></i>Update Password
        </button>

        <div class="text-center">
          <a href="{{ route('login') }}" class="small">Back to Sign In</a>
        </div>
      </form>
    </div>
  </div>
@endsection
