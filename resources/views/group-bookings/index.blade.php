@extends('layouts.app')

@section('title', 'Group bookings')
@section('page', 'group-bookings')

@section('content')
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Group bookings</li>
        </ol>
      </nav>
      <h1 class="page-title">Group bookings</h1>
      <p class="page-subtitle">Watch arrival, payment due, and CXL due date highlights so deadlines are not missed.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <a
        href="{{ route('group-bookings.export', array_merge(request()->only(['q', 'hotel_id', 'sort', 'dir', 'date_from', 'date_to']), (! request()->exists('date_from') && ! request()->exists('date_to')) ? ['date_from' => $dateFrom, 'date_to' => $dateTo] : [])) }}"
        class="btn btn-outline-secondary btn-sm"
        title="Download the filtered list as Excel"
      >
        <i class="bi bi-download"></i> Export Excel
      </a>
      <button type="button" class="btn btn-accent btn-sm" data-bs-toggle="modal" data-bs-target="#importGroupBookingsModal">
        <i class="bi bi-upload"></i> Import Excel
      </button>
    </div>
  </div>

  <div class="enquiry-reminder-grid">
    <div class="enquiry-reminder-card is-info">
      <div class="label">Arrivals (next 7 days)</div>
      <div class="value">{{ number_format($reminders['arrivals'] ?? 0) }}</div>
      <div class="meta">Highlighted in the Date of Arrival column</div>
    </div>
    <div class="enquiry-reminder-card {{ ($reminders['payments_overdue'] ?? 0) > 0 ? 'is-danger' : 'is-warning' }}">
      <div class="label">Payment due dates</div>
      <div class="value">{{ number_format($reminders['payments'] ?? 0) }}</div>
      <div class="meta">
        @if (($reminders['payments_overdue'] ?? 0) > 0)
          {{ $reminders['payments_overdue'] }} overdue
        @else
          Within 7 days / overdue
        @endif
      </div>
    </div>
    <div class="enquiry-reminder-card {{ ($reminders['cxl_overdue'] ?? 0) > 0 ? 'is-danger' : 'is-warning' }}">
      <div class="label">CXL due dates</div>
      <div class="value">{{ number_format($reminders['cxl'] ?? 0) }}</div>
      <div class="meta">
        @if (($reminders['cxl_overdue'] ?? 0) > 0)
          {{ $reminders['cxl_overdue'] }} overdue
        @else
          Within 7 days / overdue
        @endif
      </div>
    </div>
  </div>

  <div class="modal fade" id="importGroupBookingsModal" tabindex="-1" aria-labelledby="importGroupBookingsLabel" aria-hidden="true" @if($errors->has('file')) data-open-on-load="1" @endif>
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form method="POST" action="{{ route('group-bookings.import') }}" enctype="multipart/form-data">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title" id="importGroupBookingsLabel">Import group bookings</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p class="text-secondary small mb-3">
              Upload an Excel file mapped to enquiry fields. Every imported row is saved with
              <strong>is_confirm = 1</strong> and shown here.
            </p>
            <div class="mb-3">
              <label for="import_file" class="form-label">Excel file <span class="text-danger">*</span></label>
              <input type="file" name="file" id="import_file" class="form-control @error('file') is-invalid @enderror" accept=".xlsx,.xls" required>
              @error('file')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="mb-0">
              <label for="import_hotel_id" class="form-label">Hotel</label>
              <select name="hotel_id" id="import_hotel_id" class="form-select">
                <option value="">Current / first hotel</option>
                @foreach ($hotels as $hotel)
                  <option value="{{ $hotel->id }}" @selected((string) request('hotel_id') === (string) $hotel->id)>{{ $hotel->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-accent"><i class="bi bi-upload"></i> Import</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="table-toolbar">
      @include('stay-lists.filters', [
        'filterRoute' => 'group-bookings.index',
        'dateInputPrefix' => 'group_booking',
        'dateFrom' => $dateFrom,
        'dateTo' => $dateTo,
        'hotels' => $hotels,
      ])
      @if (request()->filled('hotel_id'))
        @php
          $filteredHotel = $hotels->firstWhere('id', (int) request('hotel_id'));
        @endphp
        @if ($filteredHotel && $filteredHotel->hasDocument())
          <a href="{{ route('group-bookings.hotel-contract', $filteredHotel) }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm mt-2">
            <i class="bi bi-file-earmark-pdf"></i> View hotel contract
          </a>
        @elseif ($filteredHotel)
          <span class="small text-secondary d-inline-block mt-2">No contract on this hotel</span>
        @endif
      @endif
    </div>

    @include('group-bookings.partials.list-table', [
      'rows' => $bookings,
      'highlightDates' => true,
      'defaultSort' => 'check_in',
      'defaultDir' => 'asc',
      'emptyMessage' => 'No confirmed bookings found.',
    ])

    @include('partials.pagination-footer', ['paginator' => $bookings])
  </div>
@endsection

@push('scripts')
<script>
(function () {
  var modalEl = document.getElementById('importGroupBookingsModal');
  if (modalEl && modalEl.getAttribute('data-open-on-load') === '1' && window.bootstrap) {
    new bootstrap.Modal(modalEl).show();
  }
})();
</script>
@endpush
