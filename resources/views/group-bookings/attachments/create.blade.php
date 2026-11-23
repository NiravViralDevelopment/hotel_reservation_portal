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
        @include('group-bookings.attachments._form')
      </div>
    </div>
    <div class="d-flex gap-2 mt-4">
      <button type="submit" class="btn btn-accent">Save</button>
      <a href="{{ route('group-bookings.attachments.index', $enquiry) }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
@endsection
