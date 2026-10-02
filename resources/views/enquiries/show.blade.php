@extends('layouts.app')

@section('title', $enquiry->ref)
@section('page', 'enquiries')

@section('content')
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('enquiries.index') }}">Enquiries</a></li>
          <li class="breadcrumb-item active">{{ $enquiry->ref }}</li>
        </ol>
      </nav>
      <h1 class="page-title">{{ $enquiry->group_name }}</h1>
      <p class="page-subtitle">{{ $enquiry->ref }} · <x-badge-status :status="$enquiry->status" /></p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      @can('convert', $enquiry)
        <form method="POST" action="{{ route('enquiries.convert', $enquiry) }}" onsubmit="return confirm('Confirm this enquiry and create a group booking?');">
          @csrf
          <button type="submit" class="btn btn-accent btn-sm"><i class="bi bi-arrow-right-circle"></i> Confirm to booking</button>
        </form>
      @endcan
      @can('update', $enquiry)
        <a href="{{ route('enquiries.edit', $enquiry) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil"></i> Edit</a>
      @endcan
      @can('delete', $enquiry)
        <form method="POST" action="{{ route('enquiries.destroy', $enquiry) }}" onsubmit="return confirm('Delete this enquiry?');">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i> Delete</button>
        </form>
      @endcan
    </div>
  </div>

  <div class="detail-grid-3 mb-4">
    <div class="card">
      <div class="card-header">Enquiry</div>
      <div class="card-body">
        <div class="info-card mb-3"><div class="info-card-label">Date</div><div class="info-card-value">{{ $enquiry->enquiry_date?->format('d M Y') ?? '—' }} {{ $enquiry->day ? "({$enquiry->day})" : '' }}</div></div>
        <div class="info-card mb-3"><div class="info-card-label">Response date</div><div class="info-card-value">{{ $enquiry->response_date?->format('d M Y') ?? '—' }}</div></div>
        <div class="info-card mb-3"><div class="info-card-label">Client response</div><div class="info-card-value">{{ $enquiry->client_response ?: '—' }}</div></div>
        <div class="info-card mb-3"><div class="info-card-label">Year</div><div class="info-card-value">{{ $enquiry->year ?? '—' }}</div></div>
        <div class="info-card"><div class="info-card-label">Email</div><div class="info-card-value">{{ $enquiry->email ?? '—' }}</div></div>
      </div>
    </div>
    <div class="card">
      <div class="card-header">Partners</div>
      <div class="card-body">
        <div class="info-card mb-3"><div class="info-card-label">Agency</div><div class="info-card-value">{{ $enquiry->travelAgency?->name ?? '—' }}</div></div>
        <div class="info-card"><div class="info-card-label">Hotel</div><div class="info-card-value">{{ $enquiry->hotel?->name ?? '—' }}</div></div>
      </div>
    </div>
    <div class="card">
      <div class="card-header">Commercial</div>
      <div class="card-body">
        <div class="info-card mb-3"><div class="info-card-label">Check-in</div><div class="info-card-value">{{ $enquiry->check_in?->format('d M Y') ?? '—' }}</div></div>
        <div class="info-card mb-3"><div class="info-card-label">Check-out</div><div class="info-card-value">{{ $enquiry->check_out?->format('d M Y') ?? '—' }}</div></div>
        <div class="info-card mb-3"><div class="info-card-label">Nights</div><div class="info-card-value">{{ $enquiry->nights ?? '—' }}</div></div>
        <div class="info-card mb-3"><div class="info-card-label">Rooms / night</div><div class="info-card-value">{{ $enquiry->rooms_per_night ?? '—' }}</div></div>
        <div class="info-card"><div class="info-card-label">Total revenue</div><div class="info-card-value">£{{ number_format((float) ($enquiry->total_revenue ?? 0), 2) }}</div></div>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header">Room breakdown</div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4">
          <div class="info-card"><div class="info-card-label">Single</div><div class="info-card-value">{{ $enquiry->single_rooms ?? 0 }} × £{{ number_format((float) ($enquiry->single_rate ?? 0), 2) }}</div></div>
        </div>
        <div class="col-md-4">
          <div class="info-card"><div class="info-card-label">Double</div><div class="info-card-value">{{ $enquiry->double_rooms ?? 0 }} × £{{ number_format((float) ($enquiry->double_rate ?? 0), 2) }}</div></div>
        </div>
        <div class="col-md-4">
          <div class="info-card"><div class="info-card-label">Triple</div><div class="info-card-value">{{ $enquiry->triple_rooms ?? 0 }} × £{{ number_format((float) ($enquiry->triple_rate ?? 0), 2) }}</div></div>
        </div>
      </div>
    </div>
  </div>

  @if ($enquiry->convertedBooking)
    <div class="alert alert-info">
      Linked booking:
      <a href="{{ route('group-bookings.show', $enquiry->convertedBooking) }}">{{ $enquiry->convertedBooking->block_id }}</a>
      @if (($enquiry->convertedBooking->status?->value ?? $enquiry->convertedBooking->status) === 'Cancelled')
        · <a href="{{ route('cancelled-bookings.index') }}">View in Cancelled Bookings</a>
      @endif
    </div>
  @endif

  @can('update', $enquiry)
    <div class="card mb-4" id="client-response">
      <div class="card-header">Client response</div>
      <div class="card-body">
        <form method="POST" action="{{ route('enquiries.response', $enquiry) }}" class="row g-3 enquiry-form" novalidate>
          @csrf
          <div class="col-md-8">
            <label for="enquiry_name" class="form-label">Enquiry name</label>
            <input type="text" id="enquiry_name" class="form-control" value="{{ $enquiry->group_name }}" readonly>
            <div class="form-text">{{ $enquiry->ref }}. Each reply is kept in this enquiry’s history.</div>
          </div>
          <div class="col-md-4">
            <label for="response_date" class="form-label">Response date <span class="text-danger">*</span></label>
            <input type="date" name="response_date" id="response_date" class="form-control @error('response_date') is-invalid @enderror" value="{{ old('response_date') }}">
            @error('response_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <label for="client_response" class="form-label">Client response <span class="text-danger">*</span></label>
            <textarea name="client_response" id="client_response" rows="3" class="form-control @error('client_response') is-invalid @enderror" placeholder="What did the client say?" maxlength="2000">{{ old('client_response') }}</textarea>
            @error('client_response')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-accent btn-sm">Save response</button>
          </div>
        </form>
      </div>
    </div>
  @endcan

  <div class="card mb-4">
    <div class="card-header">Response history — {{ $enquiry->group_name }}</div>
    <div class="table-wrapper">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>Response date</th>
            <th>Client response</th>
            <th>Recorded</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($enquiry->responses as $response)
            <tr>
              <td class="text-nowrap">{{ $response->response_date?->format('d M Y') ?? '—' }}</td>
              <td>{{ $response->client_response }}</td>
              <td class="text-nowrap">
                {{ $response->user?->name ?? '—' }}
                <div class="small text-secondary">{{ $response->created_at?->format('d M Y H:i') }}</div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" class="text-center text-secondary py-4">No responses yet for this enquiry.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  @if ($enquiry->remarks)
    <div class="card mb-4">
      <div class="card-header">Remarks</div>
      <div class="card-body"><p class="mb-0 text-secondary">{{ $enquiry->remarks }}</p></div>
    </div>
  @endif
@endsection

@push('scripts')
<script src="{{ asset('assets/js/enquiry-validation.js') }}?v={{ @filemtime(public_path('assets/js/enquiry-validation.js')) }}"></script>
@endpush
