@extends('layouts.app')

@section('title', 'Report results')
@section('page', 'reports')

@section('content')
  @php
    $reportKey = $validated['report'] ?? 'enquiries';
    $moduleSlug = match ($reportKey) {
      'group_bookings' => 'group-bookings',
      'cancelled_bookings' => 'cancelled-bookings',
      default => 'enquiries',
    };
    $titles = [
      'enquiries' => 'Enquiries',
      'group_bookings' => 'Group bookings',
      'cancelled_bookings' => 'Cancelled bookings',
    ];
    $pageTitle = $titles[$reportKey] ?? 'Report results';
  @endphp

  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
          <li class="breadcrumb-item active">{{ $pageTitle }}</li>
        </ol>
      </nav>
      <h1 class="page-title">{{ $pageTitle }}</h1>
      <p class="page-subtitle mb-0">
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
    <div class="d-flex flex-wrap gap-2">
      <a
        href="{{ route('reports.export', array_merge(['report' => $moduleSlug], request()->only(['date_from', 'date_to', 'sort', 'dir']))) }}"
        class="btn btn-outline-success btn-sm"
      >
        <i class="bi bi-download"></i> Export
      </a>
      <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">All reports</a>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('reports.module', $moduleSlug) }}" class="row g-3 align-items-end">
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
        @if (request('sort'))
          <input type="hidden" name="sort" value="{{ request('sort') }}">
        @endif
        @if (request('dir'))
          <input type="hidden" name="dir" value="{{ request('dir') }}">
        @endif
      </form>
    </div>
  </div>

  <div class="card">
    @if ($reportKey === 'enquiries')
      @include('enquiries.partials.list-table', [
        'rows' => $results,
        'variant' => 'enquiry',
        'defaultSort' => 'enquiry_date',
        'defaultDir' => 'desc',
        'emptyMessage' => 'No enquiries in this date range.',
        'readOnly' => true,
        'viewOnlyActions' => true,
        'showCreatedBy' => true,
      ])
    @elseif ($reportKey === 'group_bookings')
      @include('group-bookings.partials.list-table', [
        'rows' => $results,
        'defaultSort' => 'check_in',
        'defaultDir' => 'asc',
        'emptyMessage' => 'No group bookings in this date range.',
        'viewOnlyActions' => true,
        'showCreatedBy' => true,
      ])
    @else
      @include('group-bookings.partials.list-table', [
        'rows' => $results,
        'defaultSort' => 'updated_at',
        'defaultDir' => 'desc',
        'emptyMessage' => 'No cancelled bookings in this date range.',
        'showCancellationReason' => true,
        'viewOnlyActions' => true,
        'showCreatedBy' => true,
      ])
    @endif
  </div>
@endsection
