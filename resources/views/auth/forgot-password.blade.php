@extends('layouts.guest')

@section('title', 'Forgot Password')

@section('content')
  <div class="login-page">
    <div class="login-card">
      <div class="login-brand">
        <div class="login-brand-icon">
          <i class="bi bi-key"></i>
        </div>
        <h1>Reset Password</h1>
        <p>Enter your registered email address</p>
      </div>

      <form method="POST" action="{{ route('password.email') }}" class="login-form">
        @csrf

        <div class="form-floating mb-3">
          <input
            type="email"
            class="form-control @error('email') is-invalid @enderror"
            id="resetEmail"
            name="email"
            placeholder="name@company.co.uk"
            value="{{ old('email') }}"
            required
            autofocus
          >
          <label for="resetEmail"><i class="bi bi-envelope me-1"></i> Email Address</label>
          @error('email')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>

        <button type="submit" class="btn btn-brand w-100 mb-3">
          <i class="bi bi-send me-2"></i>Send Reset Link
        </button>

        <div class="text-center">
          <a href="{{ route('login') }}" class="small">Back to Sign In</a>
        </div>
      </form>
    </div>
  </div>
@endsection
