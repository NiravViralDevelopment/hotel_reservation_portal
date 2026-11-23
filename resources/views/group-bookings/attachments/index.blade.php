@extends('layouts.app')

@section('title', 'Manage Attachment')
@section('page', 'group-bookings')

@section('content')
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('group-bookings.index') }}">Confirm Group Bookings</a></li>
          <li class="breadcrumb-item active">Manage Attachment</li>
        </ol>
      </nav>
      <h1 class="page-title">Manage Attachment</h1>
      <p class="page-subtitle mb-0">
        {{ $enquiry->block_id ?: ($enquiry->client ?: 'Group booking') }}
        @if ($enquiry->hotel?->name)
          · {{ $enquiry->hotel->name }}
        @endif
      </p>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <a href="{{ route('group-bookings.attachments.create', $enquiry) }}" class="btn btn-accent btn-sm"><i class="bi bi-plus-lg"></i> Add</a>
      <a href="{{ route('group-bookings.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
  </div>

  <div class="card">
    <div class="table-wrapper">
      <table class="table table-hover mb-0 align-middle">
        <thead>
          <tr>
            <th>Title</th>
            <th>Feedback</th>
            <th>Date</th>
            <th>Attachment</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($attachments as $attachment)
            <tr>
              <td class="fw-semibold">{{ filled($attachment->title) ? $attachment->title : '—' }}</td>
              <td style="max-width:320px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $attachment->feedback }}">{{ filled($attachment->feedback) ? $attachment->feedback : '—' }}</td>
              <td class="text-nowrap">{{ $attachment->attachment_date?->format('d M Y') ?? '—' }}</td>
              <td class="text-nowrap">
                @if ($attachment->hasFile())
                  <a href="{{ $attachment->fileUrl($enquiry) }}" target="_blank" rel="noopener">
                    <i class="bi bi-paperclip"></i> {{ $attachment->original_name ?: 'Attachment' }}
                  </a>
                @else
                  —
                @endif
              </td>
              <td class="text-end text-nowrap">
                <a href="{{ route('group-bookings.attachments.edit', [$enquiry, $attachment]) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                  <i class="bi bi-pencil"></i>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center text-secondary py-5">
                <div class="mb-2"><i class="bi bi-paperclip fs-3"></i></div>
                <div>No attachments yet.</div>
                <a href="{{ route('group-bookings.attachments.create', $enquiry) }}" class="btn btn-accent btn-sm mt-2">
                  <i class="bi bi-plus-lg"></i> Add
                </a>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @include('partials.pagination-footer', ['paginator' => $attachments])
  </div>
@endsection
