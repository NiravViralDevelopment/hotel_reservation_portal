@extends('layouts.app')

@section('title', 'Add enquiry')
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
        <li class="breadcrumb-item active">Add enquiry</li>
      </ol>
    </nav>
    <h1 class="page-title">Add enquiry</h1>
    <p class="page-subtitle mb-0">Work through the sections below. Fields marked <span class="text-danger">*</span> are required.</p>
  </div>

  <form method="POST" action="{{ route('enquiries.store') }}" class="enquiry-form" data-enquiry-create="1" novalidate>
    @csrf
    @include('enquiries.partials.entry-form')
    <div class="enquiry-sticky-actions">
      <button type="submit" class="btn btn-accent"><i class="bi bi-check-lg"></i> Save enquiry</button>
      <a href="{{ route('enquiries.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection

@push('scripts')
  @include('enquiries.partials.entry-form-scripts')
@endpush
