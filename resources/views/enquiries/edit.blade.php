@extends('layouts.app')

@section('title', 'Edit enquiry')
@section('page', 'enquiries')

@push('styles')
  @include('enquiries.partials.entry-form-styles')
@endpush

@section('content')
<div class="enquiry-form-layout">
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('enquiries.index') }}">Enquiries</a></li>
        <li class="breadcrumb-item active">Edit enquiry</li>
      </ol>
    </nav>
    <h1 class="page-title">Edit enquiry</h1>
    <p class="page-subtitle mb-0">Save changes to this enquiry, open Group Bookings, or cancel the inquiry.</p>
  </div>

  <form method="POST" action="{{ route('enquiries.update', $enquiry) }}" class="enquiry-form" data-enquiry-create="1" novalidate data-existing-pairs='@json($existingPairs)'>
    @csrf
    @method('PUT')
    <input type="hidden" name="form_context" value="enquiry">
    @include('enquiries.partials.entry-form')
    <div class="enquiry-sticky-actions">
      <button type="submit" class="btn btn-accent">
        <i class="bi bi-check-lg"></i> Save enquiry
      </button>
      <button type="button" class="btn btn-outline-secondary" id="show-group-booking">
        <i class="bi bi-calendar-check"></i> Group Bookings
      </button>
      <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelInquiryModal">
        <i class="bi bi-x-circle"></i> Cancel Inquiry
      </button>
    </div>
  </form>

  @php
    $showGroupBooking = request()->boolean('group')
        || old('cancel_scope') === 'group'
        || old('form_context') === 'group';
  @endphp
  <div id="group-booking-section" class="mt-2 {{ $showGroupBooking ? '' : 'd-none' }}">
    <div class="page-header mb-3">
      <h2 class="page-title h4 mb-1">Group Bookings</h2>
      <p class="page-subtitle mb-0">Fill in the booking details below. Submitting sets the status to Confirmed.</p>
    </div>
    @include('enquiries.partials.group-booking-form')
  </div>
</div>

<div class="modal fade" id="cancelInquiryModal" tabindex="-1" aria-labelledby="cancelInquiryModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="{{ route('enquiries.cancel', $enquiry) }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="cancelInquiryModalTitle">Cancel Inquiry</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="text-secondary">Cancel enquiry <strong>{{ $enquiry->group_name ?: 'this enquiry' }}</strong>? It will move to Cancelled Inquiry.</p>
          <label for="inquiry_cxl_date" class="form-label">CXL Date</label>
          <div class="date-placeholder-wrap mb-3">
            <input type="date" name="cxl_date" id="inquiry_cxl_date" class="form-control @error('cxl_date') is-invalid @enderror" value="{{ old('cxl_date') }}">
            <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
          </div>
          @error('cxl_date')<div class="invalid-feedback d-block mb-3">{{ $message }}</div>@enderror
          <label for="cancellation_reason" class="form-label">Cancellation reason <span class="text-danger">*</span></label>
          <textarea name="cancellation_reason" id="cancellation_reason" rows="4" class="form-control @error('cancellation_reason') is-invalid @enderror" required maxlength="2000" placeholder="Enter the cancellation reason">{{ old('cancellation_reason') }}</textarea>
          @error('cancellation_reason')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Back</button>
          <button type="submit" class="btn btn-danger">Cancel Inquiry</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
  @include('enquiries.partials.entry-form-scripts')
  @include('enquiries.partials.group-booking-scripts')
  @if ($errors->has('cancellation_reason') || $errors->has('cxl_date'))
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var modalId = @json(old('cancel_scope') === 'group' ? 'cancelGroupBookingModal' : 'cancelInquiryModal');
        var modalEl = document.getElementById(modalId);
        if (modalEl && window.bootstrap) {
          window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
      });
    </script>
  @endif
@endpush
