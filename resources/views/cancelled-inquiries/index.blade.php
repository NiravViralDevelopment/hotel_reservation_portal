@extends('layouts.app')

@section('title', 'Cancelled Inquiry')
@section('page', 'enquiries')

@section('content')
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Cancelled Inquiry</li>
        </ol>
      </nav>
      <h1 class="page-title">Cancelled Inquiry</h1>
      <p class="page-subtitle">Enquiries cancelled from the edit screen, with the cancellation reason.</p>
    </div>
    <a
      href="{{ route('cancelled-inquiries.export', array_merge(request()->only(['q', 'hotel_id', 'sort', 'dir', 'date_from', 'date_to']), (! request()->exists('date_from') && ! request()->exists('date_to')) ? ['date_from' => $dateFrom, 'date_to' => $dateTo] : [])) }}"
      class="btn btn-outline-secondary btn-sm"
      title="Download the filtered list as Excel"
    >
      <i class="bi bi-download"></i> Export Excel
    </a>
  </div>

  <div class="card">
    <div class="table-toolbar">
      @include('stay-lists.filters', [
        'filterRoute' => 'cancelled-inquiries.index',
        'dateInputPrefix' => 'cancelled_inquiry',
        'dateFrom' => $dateFrom,
        'dateTo' => $dateTo,
        'hotels' => $hotels,
      ])
    </div>
    @include('enquiries.partials.list-table', [
      'rows' => $enquiries,
      'variant' => 'enquiry',
      'showCancellationReason' => true,
      'defaultSort' => 'updated_at',
      'defaultDir' => 'desc',
      'emptyMessage' => 'No cancelled inquiries found.',
      'editRoute' => 'cancelled-inquiries.edit',
    ])
    @include('partials.pagination-footer', ['paginator' => $enquiries])
  </div>
@endsection
