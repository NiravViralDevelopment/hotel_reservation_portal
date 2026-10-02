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
      <div class="input-group search-input">
        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
        <input type="search" class="form-control border-start-0 table-search" placeholder="Search enquiries…" data-table="enquiriesTable">
      </div>
    </div>
    <div class="table-wrapper">
      <table class="table table-hover mb-0" id="enquiriesTable">
        <thead>
          <tr>
            <x-sortable-th column="ref" label="Ref" default="enquiry_date" default-dir="desc" />
            <x-sortable-th column="enquiry_date" label="Date" default="enquiry_date" default-dir="desc" />
            <x-sortable-th column="response_date" label="Response date" default="enquiry_date" default-dir="desc" />
            <x-sortable-th column="group_name" label="Group" default="enquiry_date" default-dir="desc" />
            <th>Agency</th>
            <th>Hotel</th>
            <x-sortable-th column="nights" label="Nights" default="enquiry_date" default-dir="desc" />
            <x-sortable-th column="total_revenue" label="Revenue" default="enquiry_date" default-dir="desc" />
            <x-sortable-th column="status" label="Status" default="enquiry_date" default-dir="desc" />
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse ($enquiries as $enquiry)
            <tr>
              <td class="fw-semibold"><a href="{{ route('enquiries.show', $enquiry) }}">{{ $enquiry->ref }}</a></td>
              <td>{{ $enquiry->enquiry_date?->format('d M Y') ?? '—' }}</td>
              <td>
                @if ($enquiry->response_date)
                  {{ $enquiry->response_date->format('d M Y') }}
                @else
                  <span class="text-secondary">Awaiting</span>
                @endif
              </td>
              <td>{{ $enquiry->group_name }}</td>
              <td>{{ $enquiry->travelAgency?->name ?? '—' }}</td>
              <td>{{ $enquiry->hotel?->code ?? '—' }}</td>
              <td>{{ $enquiry->nights ?? '—' }}</td>
              <td>£{{ number_format((float) ($enquiry->total_revenue ?? 0), 2) }}</td>
              <td><x-badge-status :status="$enquiry->status" /></td>
              <td class="text-end text-nowrap">
                @can('view', $enquiry)
                  <a href="{{ route('enquiries.show', $enquiry) }}" class="btn btn-sm btn-outline-secondary" title="View">
                    <i class="bi bi-eye"></i> View
                  </a>
                @endcan
                @can('update', $enquiry)
                  <a href="{{ route('enquiries.show', $enquiry) }}#client-response" class="btn btn-sm btn-outline-secondary" title="Client response for {{ $enquiry->group_name }}">
                    <i class="bi bi-chat-left-text"></i> Response
                  </a>
                  <a href="{{ route('enquiries.edit', $enquiry) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                    <i class="bi bi-pencil"></i> Edit
                  </a>
                @endcan
                @can('delete', $enquiry)
                  <form method="POST" action="{{ route('enquiries.destroy', $enquiry) }}" class="d-inline" onsubmit="return confirm('Delete this enquiry?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                      <i class="bi bi-trash"></i> Delete
                    </button>
                  </form>
                @endcan
              </td>
            </tr>
          @empty
            <tr><td colspan="10" class="text-center text-secondary py-4">No enquiries found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @include('partials.pagination-footer', ['paginator' => $enquiries])
  </div>
@endsection
