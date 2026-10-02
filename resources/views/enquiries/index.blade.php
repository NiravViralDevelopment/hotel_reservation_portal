@extends('layouts.app')

@section('title', 'Enquiries')
@section('page', 'enquiries')

@section('content')
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Enquiries</li>
        </ol>
      </nav>
      <h1 class="page-title">Enquiries</h1>
      <p class="page-subtitle">Incoming group booking enquiries and pipeline.</p>
    </div>
    @can('create', App\Models\Enquiry::class)
      <a href="{{ route('enquiries.create') }}" class="btn btn-accent btn-sm"><i class="bi bi-plus-lg"></i> Add enquiry</a>
    @endcan
  </div>

  <div class="card">
    <div class="table-toolbar">
      <form method="GET" action="{{ route('enquiries.index') }}" class="d-flex flex-wrap gap-2 align-items-center w-100">
        <div class="input-group search-input" style="min-width: 200px; max-width: 280px;">
          <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
          <input
            type="search"
            name="q"
            class="form-control border-start-0"
            placeholder="Search ref, group, email…"
            value="{{ request('q') }}"
          >
        </div>
        <select name="hotel_id" class="form-select form-select-sm select2" style="width:auto; min-width: 140px;">
          <option value="">All hotels</option>
          @foreach ($hotels as $hotel)
            <option value="{{ $hotel->id }}" @selected((string) request('hotel_id') === (string) $hotel->id)>{{ $hotel->name }}</option>
          @endforeach
        </select>
        <select name="travel_agency_id" class="form-select form-select-sm select2" style="width:auto; min-width: 160px;">
          <option value="">All agencies</option>
          @foreach ($travelAgencies as $agency)
            <option value="{{ $agency->id }}" @selected((string) request('travel_agency_id') === (string) $agency->id)>{{ $agency->name }}</option>
          @endforeach
        </select>
        <select name="status" class="form-select form-select-sm select2" style="width:auto; min-width: 130px;">
          <option value="">All statuses</option>
          @foreach ($statuses as $status)
            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
          @endforeach
        </select>
        <select name="response" class="form-select form-select-sm select2" style="width:auto; min-width: 150px;">
          <option value="">All responses</option>
          <option value="awaiting" @selected(request('response') === 'awaiting')>Awaiting response</option>
          <option value="received" @selected(request('response') === 'received')>Response received</option>
        </select>
        <input type="date" name="enquiry_date_from" class="form-control form-control-sm" style="width:auto;" value="{{ request('enquiry_date_from') }}" title="Enquiry date from">
        <input type="date" name="enquiry_date_to" class="form-control form-control-sm" style="width:auto;" value="{{ request('enquiry_date_to') }}" title="Enquiry date to">
        <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-funnel"></i> Filter</button>
        @if (request()->hasAny(['q', 'hotel_id', 'travel_agency_id', 'status', 'response', 'enquiry_date_from', 'enquiry_date_to']))
          <a href="{{ route('enquiries.index') }}" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-x-circle"></i> Clear
          </a>
        @endif
        @if (request('sort'))
          <input type="hidden" name="sort" value="{{ request('sort') }}">
        @endif
        @if (request('dir'))
          <input type="hidden" name="dir" value="{{ request('dir') }}">
        @endif
      </form>
    </div>
    <div class="table-scroll-hint"><i class="bi bi-arrows-expand"></i> Scroll horizontally to see all columns</div>
    <div class="table-wrapper table-scroll-wide table-scroll-enquiries">
      <table class="table table-hover table-sm mb-0" id="enquiriesTable" style="font-size:0.78rem">
        <thead>
          <tr>
            <x-sortable-th column="ref" label="Ref" default="enquiry_date" default-dir="desc" />
            <x-sortable-th column="year" label="Year" default="enquiry_date" default-dir="desc" />
            <x-sortable-th column="enquiry_date" label="Date" default="enquiry_date" default-dir="desc" />
            <th>Day</th>
            <x-sortable-th column="response_date" label="Response date" default="enquiry_date" default-dir="desc" />
            <x-sortable-th column="group_name" label="Group name" default="enquiry_date" default-dir="desc" />
            <th>Email</th>
            <th>Agency</th>
            <th>Hotel</th>
            <th>Check-in</th>
            <th>Check-in day</th>
            <th>Check-out</th>
            <x-sortable-th column="nights" label="Nights" default="enquiry_date" default-dir="desc" />
            <th>Rooms / night</th>
            <th>Single</th>
            <th>Single rate</th>
            <th>Double</th>
            <th>Double rate</th>
            <th>Triple</th>
            <th>Triple rate</th>
            <th>Basis</th>
            <x-sortable-th column="total_revenue" label="Total revenue" default="enquiry_date" default-dir="desc" />
            <th>Tax</th>
            <th>Tax %</th>
            <th>Tax revenue</th>
            <th>CXL policy</th>
            <th>Option date</th>
            <th>Remarks</th>
            <x-sortable-th column="status" label="Status" default="enquiry_date" default-dir="desc" />
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse ($enquiries as $enquiry)
            <tr>
              <td class="fw-semibold text-nowrap"><a href="{{ route('enquiries.show', $enquiry) }}">{{ $enquiry->ref }}</a></td>
              <td>{{ $enquiry->year ?? '—' }}</td>
              <td class="text-nowrap">{{ $enquiry->enquiry_date?->format('d M Y') ?? '—' }}</td>
              <td>{{ $enquiry->day ?? '—' }}</td>
              <td class="text-nowrap">
                @if ($enquiry->response_date)
                  {{ $enquiry->response_date->format('d M Y') }}
                @else
                  <span class="text-secondary">Awaiting</span>
                @endif
              </td>
              <td class="text-nowrap">{{ $enquiry->group_name }}</td>
              <td class="text-nowrap">{{ $enquiry->email ?: '—' }}</td>
              <td class="text-nowrap">{{ $enquiry->travelAgency?->name ?? '—' }}</td>
              <td class="text-nowrap">{{ $enquiry->hotel?->code ?? '—' }}</td>
              <td class="text-nowrap">{{ $enquiry->check_in?->format('d M Y') ?? '—' }}</td>
              <td class="text-nowrap">{{ $enquiry->check_in_day ?: '—' }}</td>
              <td class="text-nowrap">{{ $enquiry->check_out?->format('d M Y') ?? '—' }}</td>
              <td>{{ $enquiry->nights ?? '—' }}</td>
              <td>{{ $enquiry->rooms_per_night ?? '—' }}</td>
              <td>{{ $enquiry->single_rooms ?? 0 }}</td>
              <td>£{{ number_format((float) ($enquiry->single_rate ?? 0), 2) }}</td>
              <td>{{ $enquiry->double_rooms ?? 0 }}</td>
              <td>£{{ number_format((float) ($enquiry->double_rate ?? 0), 2) }}</td>
              <td>{{ $enquiry->triple_rooms ?? 0 }}</td>
              <td>£{{ number_format((float) ($enquiry->triple_rate ?? 0), 2) }}</td>
              <td>{{ $enquiry->basis ?: '—' }}</td>
              <td class="text-nowrap">£{{ number_format((float) ($enquiry->total_revenue ?? 0), 2) }}</td>
              <td>{{ $enquiry->has_tax ? 'Yes' : 'No' }}</td>
              <td>{{ $enquiry->has_tax ? number_format((float) ($enquiry->tax_percentage ?? 0), 2).'%' : '—' }}</td>
              <td class="text-nowrap">£{{ number_format((float) ($enquiry->tax_revenue ?? 0), 2) }}</td>
              <td class="text-nowrap">{{ $enquiry->cxl_policy ?: '—' }}</td>
              <td class="text-nowrap">{{ $enquiry->option_date?->format('d M Y') ?? '—' }}</td>
              <td style="max-width:220px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $enquiry->remarks }}">{{ $enquiry->remarks ?: '—' }}</td>
              <td><x-badge-status :status="$enquiry->status" /></td>
              <td class="text-end text-nowrap">
                @can('view', $enquiry)
                  <a href="{{ route('enquiries.show', $enquiry) }}" class="btn btn-sm btn-outline-secondary" title="View">
                    <i class="bi bi-eye"></i>
                  </a>
                @endcan
                @can('update', $enquiry)
                  <a href="{{ route('enquiries.show', $enquiry) }}#client-response" class="btn btn-sm btn-outline-secondary" title="Client response for {{ $enquiry->group_name }}">
                    <i class="bi bi-chat-left-text"></i>
                  </a>
                  <a href="{{ route('enquiries.edit', $enquiry) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                    <i class="bi bi-pencil"></i>
                  </a>
                @endcan
                @can('delete', $enquiry)
                  <form method="POST" action="{{ route('enquiries.destroy', $enquiry) }}" class="d-inline" onsubmit="return confirm('Delete this enquiry?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                @endcan
              </td>
            </tr>
          @empty
            <tr><td colspan="30" class="text-center text-secondary py-4">No enquiries found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @include('partials.pagination-footer', ['paginator' => $enquiries])
  </div>
@endsection
