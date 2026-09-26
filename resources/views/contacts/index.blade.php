@extends('layouts.app')

@section('title', 'Contacts')
@section('page', 'contacts')

@section('content')
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Contacts</li>
        </ol>
      </nav>
      <h1 class="page-title">Contacts</h1>
      <p class="page-subtitle">Company and agency contact directory.</p>
    </div>
    @can('create', App\Models\Contact::class)
      <a href="{{ route('contacts.create') }}" class="btn btn-accent btn-sm"><i class="bi bi-plus-lg"></i> Add contact</a>
    @endcan
  </div>

  <div class="card">
    <div class="table-toolbar">
      <div class="input-group search-input">
        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
        <input type="search" class="form-control border-start-0 table-search" placeholder="Search contacts…" data-table="contactsTable">
      </div>
    </div>
    <div class="table-wrapper">
      <table class="table table-hover mb-0" id="contactsTable">
        <thead>
          <tr>
            <th>Name</th>
            <th>Company</th>
            <th>Agency</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Position</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse ($contacts as $contact)
            <tr>
              <td class="fw-semibold"><a href="{{ route('contacts.show', $contact) }}">{{ $contact->name }}</a></td>
              <td>{{ $contact->company?->name ?? '—' }}</td>
              <td>{{ $contact->travelAgency?->name ?? '—' }}</td>
              <td>{{ $contact->email ?? '—' }}</td>
              <td>{{ $contact->phone ?? '—' }}</td>
              <td>{{ $contact->position ?? '—' }}</td>
              <td class="text-end">
                @can('update', $contact)
                  <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                @endcan
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-secondary py-4">No contacts found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @include('partials.pagination-footer', ['paginator' => $contacts])
  </div>
@endsection
