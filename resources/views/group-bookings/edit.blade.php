@extends('layouts.app')

@section('title', 'Edit booking')
@section('page', 'group-bookings')

@section('content')
  @php $b = $groupBooking; @endphp
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('group-bookings.index') }}">Group bookings</a></li>
        <li class="breadcrumb-item active">{{ $b->block_id }}</li>
      </ol>
    </nav>
    <h1 class="page-title">Edit {{ $b->block_id }}</h1>
  </div>

  <form method="POST" action="{{ route('group-bookings.update', $b) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="card mb-4">
      <div class="card-header">Booking details</div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label for="block_id" class="form-label">Block ID <span class="text-danger">*</span></label>
            <input type="text" name="block_id" id="block_id" class="form-control @error('block_id') is-invalid @enderror" value="{{ old('block_id', $b->block_id) }}" placeholder="Enter block ID" required>
            @error('block_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-8">
            <label for="group_name" class="form-label">Group name <span class="text-danger">*</span></label>
            <input type="text" name="group_name" id="group_name" class="form-control @error('group_name') is-invalid @enderror" value="{{ old('group_name', $b->group_name) }}" placeholder="Enter group name" required>
            @error('group_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="hotel_id" class="form-label">Hotel</label>
            <select name="hotel_id" id="hotel_id" class="form-select select2 @error('hotel_id') is-invalid @enderror">
              <option value="">— None —</option>
              @foreach ($hotels as $hotel)
                <option value="{{ $hotel->id }}" @selected(old('hotel_id', $b->hotel_id) == $hotel->id)>{{ $hotel->name }}</option>
              @endforeach
            </select>
            @error('hotel_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="company_id" class="form-label">Company</label>
            <select name="company_id" id="company_id" class="form-select select2 @error('company_id') is-invalid @enderror">
              <option value="">— None —</option>
              @foreach ($companies as $company)
                <option value="{{ $company->id }}" @selected(old('company_id', $b->company_id) == $company->id)>{{ $company->name }}</option>
              @endforeach
            </select>
            @error('company_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="travel_agency_id" class="form-label">Travel agency</label>
            <select name="travel_agency_id" id="travel_agency_id" class="form-select select2 @error('travel_agency_id') is-invalid @enderror">
              <option value="">— None —</option>
              @foreach ($travelAgencies as $agency)
                <option value="{{ $agency->id }}" @selected(old('travel_agency_id', $b->travel_agency_id) == $agency->id)>{{ $agency->name }}</option>
              @endforeach
            </select>
            @error('travel_agency_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="contact_id" class="form-label">Contact</label>
            <select name="contact_id" id="contact_id" class="form-select select2 @error('contact_id') is-invalid @enderror">
              <option value="">— None —</option>
              @foreach ($contacts as $contact)
                <option value="{{ $contact->id }}" @selected(old('contact_id', $b->contact_id) == $contact->id)>{{ $contact->name }}</option>
              @endforeach
            </select>
            @error('contact_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="arrival" class="form-label">Arrival <span class="text-danger">*</span></label>
            <input type="date" name="arrival" id="arrival" class="form-control @error('arrival') is-invalid @enderror" value="{{ old('arrival', $b->arrival?->format('Y-m-d')) }}" required>
            @error('arrival')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="departure" class="form-label">Departure <span class="text-danger">*</span></label>
            <input type="date" name="departure" id="departure" class="form-control @error('departure') is-invalid @enderror" value="{{ old('departure', $b->departure?->format('Y-m-d')) }}" required>
            @error('departure')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="nights" class="form-label">Nights</label>
            <input type="number" name="nights" id="nights" class="form-control @error('nights') is-invalid @enderror" value="{{ old('nights', $b->nights) }}" placeholder="Enter nights">
            @error('nights')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select select2 @error('status') is-invalid @enderror">
              @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected(old('status', $b->status?->value ?? $b->status) === $status)>{{ $status }}</option>
              @endforeach
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="revenue" class="form-label">Revenue (£)</label>
            <input type="number" step="0.01" name="revenue" id="revenue" class="form-control @error('revenue') is-invalid @enderror" value="{{ old('revenue', $b->revenue) }}" placeholder="Enter revenue (£)">
            @error('revenue')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="rooms" class="form-label">Rooms</label>
            <input type="number" name="rooms" id="rooms" class="form-control @error('rooms') is-invalid @enderror" value="{{ old('rooms', $b->rooms) }}" placeholder="Enter rooms">
            @error('rooms')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="pax" class="form-label">Pax</label>
            <input type="number" name="pax" id="pax" class="form-control @error('pax') is-invalid @enderror" value="{{ old('pax', $b->pax) }}" placeholder="Enter pax">
            @error('pax')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="client" class="form-label">Client</label>
            <input type="text" name="client" id="client" class="form-control @error('client') is-invalid @enderror" value="{{ old('client', $b->client) }}" placeholder="Enter client">
            @error('client')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $b->email) }}" placeholder="Enter email">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="commission" class="form-label">Commission (£)</label>
            <input type="number" step="0.01" name="commission" id="commission" min="0" class="form-control @error('commission') is-invalid @enderror" value="{{ old('commission', $b->commission) }}" placeholder="Enter commission (£)">
            @error('commission')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <label for="internal_notes" class="form-label">Internal notes</label>
            <textarea name="internal_notes" id="internal_notes" rows="2" class="form-control @error('internal_notes') is-invalid @enderror" placeholder="Enter internal notes">{{ old('internal_notes', $b->internal_notes) }}</textarea>
            @error('internal_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <label for="update_notes" class="form-label">Update notes</label>
            <textarea name="update_notes" id="update_notes" rows="2" class="form-control @error('update_notes') is-invalid @enderror" placeholder="Enter update notes">{{ old('update_notes', $b->update_notes) }}</textarea>
            @error('update_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">Payment Terms and Conditions</div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label for="payment_term" class="form-label">Payment Term</label>
            <input type="text" name="payment_term" id="payment_term" class="form-control @error('payment_term') is-invalid @enderror" value="{{ old('payment_term', $b->payment_term) }}" placeholder="Enter payment term">
            @error('payment_term')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="due_date" class="form-label">Due Date</label>
            <input type="date" name="due_date" id="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date', $b->due_date?->format('Y-m-d')) }}">
            @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="payment_status" class="form-label">Payment Status</label>
            <input type="text" name="payment_status" id="payment_status" class="form-control @error('payment_status') is-invalid @enderror" value="{{ old('payment_status', $b->payment_status) }}" placeholder="Enter payment status">
            @error('payment_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="payment_status_display" class="form-label">Payment display</label>
            <select name="payment_status_display" id="payment_status_display" class="form-select select2 @error('payment_status_display') is-invalid @enderror">
              <option value="">— None —</option>
              @foreach (\App\Enums\PaymentDisplayStatus::values() as $ps)
                <option value="{{ $ps }}" @selected(old('payment_status_display', $b->payment_status_display?->value ?? $b->payment_status_display) === $ps)>{{ $ps }}</option>
              @endforeach
            </select>
            @error('payment_status_display')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">CXL Policy</div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label for="cxl_policy" class="form-label">CXL Policy</label>
            <input type="text" name="cxl_policy" id="cxl_policy" class="form-control @error('cxl_policy') is-invalid @enderror" value="{{ old('cxl_policy', $b->cxl_policy) }}" placeholder="Enter CXL policy">
            @error('cxl_policy')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="cxl_due_date" class="form-label">CXL Due Date</label>
            <input type="date" name="cxl_due_date" id="cxl_due_date" class="form-control @error('cxl_due_date') is-invalid @enderror" value="{{ old('cxl_due_date', $b->cxl_due_date?->format('Y-m-d')) }}">
            @error('cxl_due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="cxl_date" class="form-label">CXL Date</label>
            <input type="date" name="cxl_date" id="cxl_date" class="form-control @error('cxl_date') is-invalid @enderror" value="{{ old('cxl_date', $b->cxl_date?->format('Y-m-d')) }}">
            @error('cxl_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">Attach document</div>
      <div class="card-body">
        @if ($b->documents->isNotEmpty())
          <div class="table-wrapper mb-3">
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
                    <td class="text-end text-nowrap">
                      <a href="{{ route('group-bookings.documents.download', [$b, $document]) }}" class="btn btn-sm btn-outline-secondary">Download</a>
                      <form method="POST" action="{{ route('group-bookings.documents.destroy', [$b, $document]) }}" class="d-inline" onsubmit="return confirm('Remove this document?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                      </form>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <p class="text-secondary small mb-3">No documents attached yet.</p>
        @endif

        <div class="row g-3">
          <div class="col-md-6">
            <label for="document_name" class="form-label">Document name</label>
            <input type="text" name="document_name" id="document_name" class="form-control @error('document_name') is-invalid @enderror" value="{{ old('document_name') }}" placeholder="Enter document name">
            @error('document_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="document_file" class="form-label">File</label>
            <input type="file" name="document_file" id="document_file" class="form-control @error('document_file') is-invalid @enderror">
            @error('document_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text">Max 10 MB. Saved when you update the booking.</div>
          </div>
        </div>
      </div>
    </div>

    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-accent">Update booking</button>
      <a href="{{ route('group-bookings.show', $b) }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
@endsection
