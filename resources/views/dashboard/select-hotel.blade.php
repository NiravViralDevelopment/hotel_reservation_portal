@extends('layouts.app')

@section('title', 'Select hotel')
@section('page', 'dashboard')

@section('content')
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item active">Select hotel</li>
      </ol>
    </nav>
    <h1 class="page-title">Select a hotel</h1>
    <p class="page-subtitle">Welcome, {{ auth()->user()->name }}. Choose one of your allocated hotels to continue.</p>
  </div>

  @if ($allocatedHotels->isEmpty())
    <div class="alert alert-warning">
      No hotels are allocated to your account. Please contact an administrator.
    </div>
  @else
    <div class="row g-3">
      @foreach ($allocatedHotels as $hotel)
        <div class="col-md-6 col-xl-4">
          <div class="card h-100 hotel-select-card">
            <div class="card-body d-flex flex-column">
              <div class="d-flex align-items-start gap-3 mb-3">
                <div class="stat-card-icon primary"><i class="bi bi-building"></i></div>
                <div>
                  <h2 class="h5 mb-1">{{ $hotel->name }}</h2>
                  <div class="text-secondary small">
                    {{ $hotel->code }}
                    @if ($hotel->city || $hotel->country)
                      · {{ collect([$hotel->city, $hotel->country])->filter()->implode(', ') }}
                    @endif
                  </div>
                </div>
              </div>
              <div class="mt-auto">
                <form method="POST" action="{{ route('hotel-context.switch') }}">
                  @csrf
                  <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                  <button type="submit" class="btn btn-accent w-100">
                    <i class="bi bi-box-arrow-in-right"></i> Enter hotel
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif
@endsection
