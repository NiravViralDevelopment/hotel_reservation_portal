@extends('layouts.app')

@section('title', 'Add attachment')
@section('page', 'group-bookings')

@section('content')
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('group-bookings.index') }}">Confirm Group Bookings</a></li>
        <li class="breadcrumb-item"><a href="{{ route('group-bookings.attachments.index', $enquiry) }}">Manage Attachment</a></li>
        <li class="breadcrumb-item active">Add</li>
      </ol>
    </nav>
    <h1 class="page-title">Add attachment</h1>
    <p class="page-subtitle mb-0">
      {{ $enquiry->block_id ?: ($enquiry->client ?: 'Group booking') }}
      @if ($enquiry->hotel?->name)
        · {{ $enquiry->hotel->name }}
      @endif
      · All fields are optional.
    </p>
  </div>

  <form method="POST" action="{{ route('group-bookings.attachments.store', $enquiry) }}" enctype="multipart/form-data" novalidate>
    @csrf
    <div class="card">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" maxlength="255">
            @error('title')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="date" class="form-label">Date</label>
            <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date') }}">
            @error('date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <label for="feedback" class="form-label">Feedback</label>
            <textarea name="feedback" id="feedback" rows="4" class="form-control @error('feedback') is-invalid @enderror" maxlength="5000">{{ old('feedback') }}</textarea>
            @error('feedback')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <label for="attachment" class="form-label">Attachment</label>
            <input type="file" name="attachment" id="attachment" class="form-control @error('attachment') is-invalid @enderror">
            <div class="form-text">Any file, up to 10 MB.</div>
            @error('attachment')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>
    <div class="d-flex gap-2 mt-4">
      <button type="submit" class="btn btn-accent">Save</button>
      <a href="{{ route('group-bookings.attachments.index', $enquiry) }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
@endsection
