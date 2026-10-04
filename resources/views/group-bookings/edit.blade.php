@extends('layouts.app')

@section('title', 'Edit group booking')
@section('page', 'group-bookings')

@push('styles')
  @include('enquiries.partials.entry-form-styles')
@endpush

@section('content')
<div class="enquiry-form-layout">
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('group-bookings.index') }}">Group Bookings</a></li>
        <li class="breadcrumb-item active">Edit</li>
      </ol>
    </nav>
    <h1 class="page-title">Edit group booking</h1>
    <p class="page-subtitle mb-0">{{ $enquiry->group_name ?: ($enquiry->block_id ?: 'Group booking') }}</p>
  </div>

  @include('enquiries.partials.group-booking-form', [
    'groupBookingBackUrl' => route('group-bookings.show', $enquiry),
  ])
</div>
@endsection

@push('scripts')
  @include('enquiries.partials.entry-form-scripts')
  @include('enquiries.partials.group-booking-scripts')
  @if ($errors->has('cancellation_reason') && old('cancel_scope') === 'group')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var modalEl = document.getElementById('cancelGroupBookingModal');
        if (modalEl && window.bootstrap) {
          window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
      });
    </script>
  @endif
@endpush
