@extends('layouts.app')

@section('title', $groupBooking->block_id)
@section('page', 'booking-detail')

@section('content')
  @php $b = $groupBooking; @endphp
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('group-bookings.index') }}">Group bookings</a></li>
          <li class="breadcrumb-item active">{{ $b->block_id }}</li>
        </ol>
      </nav>
      <h1 class="page-title">{{ $b->group_name }}</h1>
      <p class="page-subtitle">{{ $b->block_id }} · <x-badge-status :status="$b->status" />
        @if ($b->payment_status_display)
          · <x-badge-status :status="$b->payment_status_display" />
        @endif
      </p>
    </div>
    <div class="d-flex gap-2">
      @can('update', $b)
        <a href="{{ route('group-bookings.edit', $b) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil"></i> Edit</a>
      @endcan
    </div>
  </div>

  <div class="detail-grid-3 mb-4">
    <div class="card">
      <div class="card-header"><i class="bi bi-info-circle me-2"></i>Contract details</div>
      <div class="card-body">
        <div class="info-card mb-3"><div class="info-card-label">Hotel</div><div class="info-card-value">{{ $b->hotel?->name ?? '—' }}</div></div>
        <div class="info-card mb-3"><div class="info-card-label">Company</div><div class="info-card-value">{{ $b->company?->name ?? '—' }}</div></div>
        <div class="info-card mb-3"><div class="info-card-label">Agency</div><div class="info-card-value">{{ $b->travelAgency?->name ?? $b->agency_name ?? '—' }}</div></div>
        <div class="info-card"><div class="info-card-label">Revenue</div><div class="info-card-value">£{{ number_format((float) ($b->revenue ?? 0), 2) }}</div></div>
      </div>
    </div>
    <div class="card">
      <div class="card-header"><i class="bi bi-person me-2"></i>Contact</div>
      <div class="card-body">
        <div class="info-card mb-3"><div class="info-card-label">Contact</div><div class="info-card-value">{{ $b->contact?->name ?? $b->contact_name ?? '—' }}</div></div>
        <div class="info-card mb-3"><div class="info-card-label">Email</div><div class="info-card-value">{{ $b->email ?? '—' }}</div></div>
        <div class="info-card"><div class="info-card-label">Client</div><div class="info-card-value">{{ $b->client ?? '—' }}</div></div>
      </div>
    </div>
    <div class="card">
      <div class="card-header"><i class="bi bi-calendar-event me-2"></i>Stay</div>
      <div class="card-body">
        <div class="info-card mb-3"><div class="info-card-label">Arrival</div><div class="info-card-value">{{ $b->arrival?->format('d M Y') ?? '—' }} ({{ $b->arrival_day ?? '—' }})</div></div>
        <div class="info-card mb-3"><div class="info-card-label">Departure</div><div class="info-card-value">{{ $b->departure?->format('d M Y') ?? '—' }}</div></div>
        <div class="info-card"><div class="info-card-label">Nights / Rooms / Pax</div><div class="info-card-value">{{ $b->nights ?? '—' }} / {{ $b->rooms ?? '—' }} / {{ $b->pax ?? '—' }}</div></div>
      </div>
    </div>
  </div>

  <div class="detail-grid-3 mb-4">
    <div class="card">
      <div class="card-header">Payment Terms and Conditions</div>
      <div class="card-body">
        <div class="info-card mb-3"><div class="info-card-label">Payment Term</div><div class="info-card-value">{{ $b->payment_term ?? '—' }}</div></div>
        <div class="info-card mb-3"><div class="info-card-label">Due Date</div><div class="info-card-value">{{ $b->due_date?->format('d M Y') ?? '—' }}</div></div>
        <div class="info-card mb-3"><div class="info-card-label">Payment Status</div><div class="info-card-value">{{ $b->payment_status ?? '—' }}</div></div>
        <div class="info-card"><div class="info-card-label">Payment display</div><div class="info-card-value">{{ $b->payment_status_display?->value ?? $b->payment_status_display ?? '—' }}</div></div>
      </div>
    </div>
    <div class="card">
      <div class="card-header">CXL Policy</div>
      <div class="card-body">
        <div class="info-card mb-3"><div class="info-card-label">CXL Policy</div><div class="info-card-value">{{ $b->cxl_policy ?? '—' }}</div></div>
        <div class="info-card mb-3"><div class="info-card-label">CXL Due Date</div><div class="info-card-value">{{ $b->cxl_due_date?->format('d M Y') ?? '—' }}</div></div>
        <div class="info-card"><div class="info-card-label">CXL Date</div><div class="info-card-value">{{ $b->cxl_date?->format('d M Y') ?? '—' }}</div></div>
      </div>
    </div>
    <div class="card">
      <div class="card-header">Commercial</div>
      <div class="card-body">
        <div class="info-card mb-3"><div class="info-card-label">Commission</div><div class="info-card-value">£{{ number_format((float) ($b->commission ?? 0), 2) }}</div></div>
        <div class="info-card"><div class="info-card-label">Revenue</div><div class="info-card-value">£{{ number_format((float) ($b->revenue ?? 0), 2) }}</div></div>
      </div>
    </div>
  </div>

  @if ($b->documents->isNotEmpty())
    <div class="card mb-4">
      <div class="card-header">Documents</div>
      <div class="table-wrapper">
        <table class="table table-sm table-hover mb-0">
          <thead>
            <tr>
              <th>Name</th>
              <th>Uploaded by</th>
              <th>Date</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach ($b->documents as $document)
              <tr>
                <td>{{ $document->name }}</td>
                <td>{{ $document->uploadedBy?->name ?? '—' }}</td>
                <td>{{ $document->created_at?->format('d M Y') ?? '—' }}</td>
                <td class="text-end">
                  <a href="{{ route('group-bookings.documents.download', [$b, $document]) }}" class="btn btn-sm btn-outline-secondary">Download</a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @endif

  @if ($b->dailyRows->isNotEmpty())
    <div class="card mb-4">
      <div class="card-header">Daily rows ({{ $b->dailyRows->count() }})</div>
      <div class="table-wrapper">
        <table class="table table-sm table-hover mb-0">
          <thead>
            <tr>
              <th>Sheet</th>
              <th>Arrival</th>
              <th>Departure</th>
              <th>Nights</th>
              <th>RNs</th>
              <th>Revenue</th>
              <th>Meal plan</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($b->dailyRows as $row)
              <tr>
                <td>{{ $row->sheet ?? '—' }}</td>
                <td>{{ $row->arrival?->format('d M Y') ?? '—' }}</td>
                <td>{{ $row->departure?->format('d M Y') ?? '—' }}</td>
                <td>{{ $row->nights ?? '—' }}</td>
                <td>{{ $row->total_rns ?? '—' }}</td>
                <td>£{{ number_format((float) ($row->total_rev ?? 0), 2) }}</td>
                <td>{{ $row->meal_plan ?? '—' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @endif

  @can('cancel', $b)
    @if (($b->status?->value ?? $b->status) !== 'Cancelled')
      <div class="card border-danger">
        <div class="card-header text-danger">Cancel booking</div>
        <div class="card-body">
          <form method="POST" action="{{ route('group-bookings.cancel', $b) }}" onsubmit="return confirm('Cancel this group booking?');">
            @csrf
            <div class="row g-3">
              <div class="col-md-4">
                <label for="cxl_date" class="form-label">Cancellation date</label>
                <input type="date" name="cxl_date" id="cxl_date" class="form-control @error('cxl_date') is-invalid @enderror" value="{{ old('cxl_date') }}">
                @error('cxl_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-4">
                <label for="revenue_lost" class="form-label">Revenue lost (£)</label>
                <input type="number" step="0.01" name="revenue_lost" id="revenue_lost" class="form-control @error('revenue_lost') is-invalid @enderror" value="{{ old('revenue_lost') }}" placeholder="Enter revenue lost (£)">
                @error('revenue_lost')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-12">
                <label for="cancellation_reason" class="form-label">Reason</label>
                <textarea name="cancellation_reason" id="cancellation_reason" rows="2" class="form-control @error('cancellation_reason') is-invalid @enderror" placeholder="Enter cancellation reason">{{ old('cancellation_reason') }}</textarea>
                @error('cancellation_reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-outline-danger">Cancel booking</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    @endif
  @endcan
@endsection
