@extends('layouts.app')

@section('title', 'Edit status')
@section('page', 'status-masters')

@section('content')
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('status-masters.index') }}">Status master</a></li>
        <li class="breadcrumb-item active">{{ ucwords(str_replace('_', ' ', $statusMaster->title)) }}</li>
      </ol>
    </nav>
    <h1 class="page-title">Edit status</h1>
  </div>

  <form method="POST" action="{{ route('status-masters.update', $statusMaster) }}" id="statusMasterForm" novalidate>
    @csrf
    @method('PUT')
    <div class="card">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-8">
            <label for="title" class="form-label">Status title <span class="text-danger">*</span></label>
            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $statusMaster->title) }}" maxlength="255">
            <div class="invalid-feedback js-client-error" id="titleError" style="display:none;"></div>
            @error('title')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="status" class="form-label">Active / Inactive <span class="text-danger">*</span></label>
            <select name="status" id="status" class="form-select select2 @error('status') is-invalid @enderror">
              <option value="active" @selected(old('status', $statusMaster->status) === 'active')>Active</option>
              <option value="inactive" @selected(old('status', $statusMaster->status) === 'inactive')>Inactive</option>
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>
    <div class="d-flex gap-2 mt-4">
      <button type="submit" class="btn btn-accent" id="statusMasterSubmit">Update status</button>
      <a href="{{ route('status-masters.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
@endsection

@push('scripts')
<script>
(function () {
  var existingTitles = @json($existingTitles);
  var titleEl = document.getElementById('title');
  var errorEl = document.getElementById('titleError');
  var form = document.getElementById('statusMasterForm');

  function normalize(value) {
    return String(value || '').trim().toLowerCase();
  }

  function setError(message) {
    titleEl.classList.add('is-invalid');
    errorEl.textContent = message;
    errorEl.style.display = 'block';
  }

  function clearError() {
    titleEl.classList.remove('is-invalid');
    errorEl.textContent = '';
    errorEl.style.display = 'none';
  }

  function validateTitle() {
    var value = String(titleEl.value || '').trim();
    if (!value) {
      setError('Status title is required.');
      return false;
    }
    if (existingTitles.indexOf(normalize(value)) !== -1) {
      setError('This status title already exists.');
      return false;
    }
    clearError();
    return true;
  }

  ['keyup', 'input', 'blur', 'change'].forEach(function (evt) {
    titleEl.addEventListener(evt, validateTitle);
  });

  form.addEventListener('submit', function (e) {
    if (!validateTitle()) {
      e.preventDefault();
      titleEl.focus();
    }
  });
})();
</script>
@endpush
