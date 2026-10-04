@extends('layouts.app')

@section('title', 'Cancelled Inquiry')
@section('page', 'enquiries')

@section('content')
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Cancelled Inquiry</li>
      </ol>
    </nav>
    <h1 class="page-title">Cancelled Inquiry</h1>
    <p class="page-subtitle">Enquiries cancelled from the edit screen, with the cancellation reason.</p>
  </div>

  <div class="card">
    @include('enquiries.partials.list-table', [
      'rows' => $enquiries,
      'variant' => 'enquiry',
      'showCancellationReason' => true,
      'defaultSort' => 'updated_at',
      'defaultDir' => 'desc',
      'emptyMessage' => 'No cancelled inquiries found.',
    ])
    @include('partials.pagination-footer', ['paginator' => $enquiries])
  </div>
@endsection
