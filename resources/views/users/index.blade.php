@extends('layouts.app')

@section('title', 'Users')
@section('page', 'users')

@section('content')
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Users</li>
        </ol>
      </nav>
      <h1 class="page-title">Users</h1>
      <p class="page-subtitle">System users and hotel access assignments.</p>
    </div>
    @can('create', App\Models\User::class)
      <a href="{{ route('users.create') }}" class="btn btn-accent btn-sm"><i class="bi bi-plus-lg"></i> Add user</a>
    @endcan
  </div>

  <div class="card">
    <div class="table-wrapper">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Department</th>
            <th>Roles</th>
            <th>Hotels</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse ($users as $user)
            <tr>
              <td class="fw-semibold">{{ $user->name }}</td>
              <td><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
              <td>{{ $user->phone ?? '—' }}</td>
              <td>{{ $user->department ?? '—' }}</td>
              <td>
                @forelse ($user->roles as $role)
                  <span class="badge bg-secondary me-1">{{ $role->name }}</span>
                @empty
                  —
                @endforelse
              </td>
              <td class="text-center">{{ $user->hotels->count() }}</td>
              <td><x-badge-status :status="$user->status" /></td>
              <td class="text-end">
                @can('update', $user)
                  <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                @endcan
                @can('delete', $user)
                  <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('Deactivate this user?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">Deactivate</button>
                  </form>
                @endcan
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center text-secondary py-4">No users found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @include('partials.pagination-footer', ['paginator' => $users])
  </div>
@endsection
