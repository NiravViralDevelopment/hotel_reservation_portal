@extends('layouts.app')

@section('title', 'Report results')
@section('page', 'reports')

@section('content')
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
          <li class="breadcrumb-item active">Results</li>
        </ol>
      </nav>
      <h1 class="page-title">Enquiries</h1>
      <p class="page-subtitle">
        @if (!empty($validated['date_from']) && !empty($validated['date_to']))
          {{ \Illuminate\Support\Carbon::parse($validated['date_from'])->format('d M Y') }}
          —
          {{ \Illuminate\Support\Carbon::parse($validated['date_to'])->format('d M Y') }}
          · {{ $results->count() }} {{ \Illuminate\Support\Str::plural('record', $results->count()) }}
        @else
          All dates
        @endif
      </p>
    </div>
    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">All reports</a>
  </div>

  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('reports.module', 'enquiries') }}" class="row g-3 align-items-end">
        <div class="col-md-4">
          <label for="date_from" class="form-label">Start date</label>
          <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $validated['date_from'] }}" required>
        </div>
        <div class="col-md-4">
          <label for="date_to" class="form-label">End date</label>
          <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $validated['date_to'] }}" required>
        </div>
        <div class="col-md-4">
          <button type="submit" class="btn btn-accent btn-sm">Update report</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="table-scroll-hint"><i class="bi bi-arrows-expand"></i> Scroll horizontally to see all columns</div>
    <div class="table-wrapper table-scroll-wide table-scroll-enquiries">
      <table class="table table-hover table-sm mb-0" style="font-size:0.78rem">
        <thead>
          <tr>
            <th>Ref</th>
            <th>Group</th>
            <th>Arrival date</th>
            <th>End date</th>
            <th>Client</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>Hotel</th>
            <th>Agency</th>
            <th>Total price</th>
            <th>Grand total</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($results as $enquiry)
            <tr>
              <td class="fw-semibold text-nowrap"><a href="{{ route('enquiries.show', $enquiry) }}">{{ $enquiry->ref ?: '—' }}</a></td>
              <td class="text-nowrap">{{ $enquiry->group_name }}</td>
              <td class="text-nowrap">{{ $enquiry->check_in?->format('d M Y') ?? '—' }}</td>
              <td class="text-nowrap">{{ $enquiry->check_out?->format('d M Y') ?? '—' }}</td>
              <td class="text-nowrap">{{ $enquiry->client ?: '—' }}</td>
              <td class="text-nowrap">{{ $enquiry->email ?: '—' }}</td>
              <td class="text-nowrap">{{ $enquiry->mobile ?: '—' }}</td>
              <td class="text-nowrap">{{ $enquiry->hotel?->code ?? '—' }}</td>
              <td class="text-nowrap">{{ $enquiry->travelAgency?->name ?? '—' }}</td>
              <td class="text-nowrap">£{{ number_format((float) ($enquiry->total_price ?? 0), 2) }}</td>
              <td class="text-nowrap">£{{ number_format((float) ($enquiry->grand_total ?? 0), 2) }}</td>
              <td><x-badge-status :status="$enquiry->status" /></td>
            </tr>
          @empty
            <tr><td colspan="12" class="text-center text-secondary py-4">No enquiries in this date range.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection
