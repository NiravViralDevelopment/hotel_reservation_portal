@extends('layouts.app')

@section('title', 'Edit role')
@section('page', 'roles')

@section('content')
  @php
    $isAdminRole = $role->name === 'Administrator';
    $selected = $isAdminRole
      ? collect($permissionGroups)->flatMap(fn ($g) => collect($g['permissions'])->pluck('name'))->all()
      : old('permissions', $role->permissions->pluck('name')->all());
  @endphp

  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
          <li class="breadcrumb-item"><a href="{{ route('roles.show', $role) }}">{{ $role->name }}</a></li>
          <li class="breadcrumb-item active">Edit</li>
        </ol>
      </nav>
      <h1 class="page-title">Edit role</h1>
      <p class="page-subtitle">Choose which modules and actions this role can use.</p>
    </div>
  </div>

  <form method="POST" action="{{ route('roles.update', $role) }}" id="rolePermissionsForm">
    @csrf
    @method('PUT')

    <div class="card mb-4">
      <div class="card-header">Role details</div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="name" class="form-label">Role name</label>
            <input type="text" id="name" class="form-control" value="{{ $role->name }}" readonly>
            <div class="form-text">Role name cannot be changed.</div>
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">
        <span>Permissions</span>
      </div>
      <div class="card-body">
        @if ($isAdminRole)
          <div class="alert alert-info small mb-4">
            <strong>Administrator</strong> always has full access to every module.
            Permissions below are locked and cannot be reduced.
          </div>
        @else
          <p class="small text-secondary mb-4 mb-md-3">
            Tip: turn on <strong>View</strong> for a module so it appears in the sidebar. Use a module’s <strong>All</strong> switch to select every action in that module.
          </p>
        @endif

        <div class="row g-3">
          @foreach ($permissionGroups as $group)
            @php
              $groupNames = collect($group['permissions'])->pluck('name')->all();
              $groupSelectedCount = count(array_intersect($groupNames, $selected));
              $allSelected = $groupSelectedCount === count($groupNames) && count($groupNames) > 0;
            @endphp
            <div class="col-md-6 col-xl-4">
              <div class="border rounded-3 h-100 p-3 role-perm-group" data-group="{{ $group['key'] }}">
                <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                  <div>
                    <div class="fw-semibold">{{ $group['label'] }}</div>
                    <div class="small text-secondary">{{ count($group['permissions']) }} actions</div>
                  </div>
                  @unless ($isAdminRole)
                    <div class="form-check form-switch m-0">
                      <input
                        class="form-check-input role-group-toggle"
                        type="checkbox"
                        role="switch"
                        id="group_toggle_{{ $group['key'] }}"
                        data-group="{{ $group['key'] }}"
                        @checked($allSelected)
                        title="Select all in {{ $group['label'] }}"
                      >
                      <label class="form-check-label small" for="group_toggle_{{ $group['key'] }}">All</label>
                    </div>
                  @endunless
                </div>

                <div class="d-flex flex-column gap-2">
                  @foreach ($group['permissions'] as $permission)
                    <div class="form-check">
                      <input
                        class="form-check-input role-perm-check @error('permissions') is-invalid @enderror"
                        type="checkbox"
                        name="permissions[]"
                        value="{{ $permission['name'] }}"
                        id="perm_{{ $permission['id'] }}"
                        data-group="{{ $group['key'] }}"
                        @checked(in_array($permission['name'], $selected, true))
                        @disabled($isAdminRole)
                      >
                      @if ($isAdminRole)
                        <input type="hidden" name="permissions[]" value="{{ $permission['name'] }}">
                      @endif
                      <label class="form-check-label" for="perm_{{ $permission['id'] }}">
                        {{ $permission['label'] }}
                        <span class="text-secondary small d-block">{{ $permission['name'] }}</span>
                      </label>
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          @endforeach
        </div>
        @error('permissions')<div class="text-danger small mt-3">{{ $message }}</div>@enderror
      </div>
    </div>

    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-accent"><i class="bi bi-check-lg"></i> Update role</button>
      <a href="{{ route('roles.show', $role) }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
@endsection

@push('scripts')
  @unless ($isAdminRole)
    <script>
      (function () {
        const form = document.getElementById('rolePermissionsForm');
        if (!form) return;

        function checksForGroup(group) {
          return form.querySelectorAll('.role-perm-check[data-group="' + group + '"]');
        }

        function syncGroupToggle(group) {
          const toggle = form.querySelector('.role-group-toggle[data-group="' + group + '"]');
          if (!toggle) return;
          const checks = Array.from(checksForGroup(group));
          toggle.checked = checks.length > 0 && checks.every(function (el) { return el.checked; });
          toggle.indeterminate = checks.some(function (el) { return el.checked; }) && !toggle.checked;
        }

        form.querySelectorAll('.role-group-toggle').forEach(function (toggle) {
          toggle.addEventListener('change', function () {
            checksForGroup(toggle.dataset.group).forEach(function (el) {
              el.checked = toggle.checked;
            });
            toggle.indeterminate = false;
          });
        });

        form.querySelectorAll('.role-perm-check').forEach(function (check) {
          check.addEventListener('change', function () {
            syncGroupToggle(check.dataset.group);
          });
        });

        form.querySelectorAll('.role-group-toggle').forEach(function (toggle) {
          syncGroupToggle(toggle.dataset.group);
        });
      })();
    </script>
  @endunless
@endpush
