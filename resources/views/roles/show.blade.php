@extends('layouts.app')

@section('title', $role->name)
@section('page', 'roles')

@section('content')
  @php
    $selected = $role->permissions->pluck('name')->all();
    $isAdminRole = $role->name === 'Administrator';
  @endphp

  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
          <li class="breadcrumb-item active">{{ $role->name }}</li>
        </ol>
      </nav>
      <h1 class="page-title">{{ $role->name }}</h1>
      <p class="page-subtitle">Role details and assigned permissions.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back
      </a>
      @can('roles.edit')
        <a href="{{ route('roles.edit', $role) }}" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-pencil"></i> Edit
        </a>
      @endcan
    </div>
  </div>

  <div class="detail-grid-3 mb-4">
    <div class="card">
      <div class="card-header">Role details</div>
      <div class="card-body">
        <div class="info-card mb-3">
          <div class="info-card-label">Name</div>
          <div class="info-card-value">{{ $role->name }}</div>
        </div>
        <div class="info-card mb-3">
          <div class="info-card-label">Users with this role</div>
          <div class="info-card-value">{{ $role->users_count }}</div>
        </div>
        <div class="info-card">
          <div class="info-card-label">Permissions assigned</div>
          <div class="info-card-value">
            @if ($isAdminRole)
              All permissions
            @else
              {{ count($selected) }}
            @endif
          </div>
        </div>
      </div>
    </div>

    <div class="card" style="grid-column: span 2;">
      <div class="card-header">Users</div>
      <div class="card-body">
        @forelse ($role->users->take(8) as $user)
          <span class="badge bg-light text-dark border me-1 mb-1">{{ $user->name }}</span>
        @empty
          <p class="mb-0 text-secondary">No users assigned to this role yet.</p>
        @endforelse
        @if ($role->users->count() > 8)
          <div class="small text-secondary mt-2">+{{ $role->users->count() - 8 }} more</div>
        @endif
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>Permissions by module</span>
      @if ($isAdminRole)
        <span class="badge bg-info-subtle text-info-emphasis border">Full access</span>
      @endif
    </div>
    <div class="card-body">
      @if ($isAdminRole)
        <div class="alert alert-info small mb-4">
          <strong>Administrator</strong> always has full access to every module.
        </div>
      @endif

      <div class="row g-3">
        @foreach ($permissionGroups as $group)
          @php
            $enabledCount = collect($group['permissions'])->filter(fn ($p) => $isAdminRole || in_array($p['name'], $selected, true))->count();
            $totalCount = count($group['permissions']);
          @endphp
          <div class="col-md-6 col-xl-4">
            <div class="border rounded-3 h-100 p-3">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="fw-semibold">{{ $group['label'] }}</div>
                <span class="badge {{ $enabledCount === $totalCount ? 'bg-success-subtle text-success-emphasis' : ($enabledCount > 0 ? 'bg-warning-subtle text-warning-emphasis' : 'bg-light text-secondary') }} border">
                  {{ $enabledCount }}/{{ $totalCount }}
                </span>
              </div>
              <div class="d-flex flex-wrap gap-2">
                @foreach ($group['permissions'] as $permission)
                  @php $on = $isAdminRole || in_array($permission['name'], $selected, true); @endphp
                  <span class="badge {{ $on ? 'bg-success-subtle text-success-emphasis border' : 'bg-light text-secondary border' }}">
                    @if ($on)
                      <i class="bi bi-check-lg me-1"></i>
                    @else
                      <i class="bi bi-dash me-1"></i>
                    @endif
                    {{ $permission['label'] }}
                  </span>
                @endforeach
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
@endsection
