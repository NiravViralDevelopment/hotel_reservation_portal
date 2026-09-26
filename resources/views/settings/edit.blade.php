@extends('layouts.app')

@section('title', 'Settings')
@section('page', 'settings')

@section('content')
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Settings</li>
      </ol>
    </nav>
    <h1 class="page-title">Application settings</h1>
    <p class="page-subtitle">Configure system defaults and labels.</p>
  </div>

  <form method="POST" action="{{ route('settings.update') }}">
    @csrf
    @method('PUT')
    @php $index = 0; @endphp
    @foreach ($grouped as $groupName => $items)
      <div class="card mb-4">
        <div class="card-header">{{ ucwords(str_replace('_', ' ', $groupName)) }}</div>
        <div class="card-body">
          <div class="row g-3">
            @foreach ($items as $setting)
              <div class="col-md-6">
                <label class="form-label">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                <input type="hidden" name="settings[{{ $index }}][group]" value="{{ $setting->group }}">
                <input type="hidden" name="settings[{{ $index }}][key]" value="{{ $setting->key }}">
                <input type="text" name="settings[{{ $index }}][value]" class="form-control @error('settings.'.$index.'.value') is-invalid @enderror" value="{{ old('settings.'.$index.'.value', $setting->value) }}">
                @error('settings.'.$index.'.value')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              @php $index++; @endphp
            @endforeach
          </div>
        </div>
      </div>
    @endforeach
    @error('settings')<div class="text-danger mb-3">{{ $message }}</div>@enderror
    <button type="submit" class="btn btn-accent">Save settings</button>
  </form>
@endsection
