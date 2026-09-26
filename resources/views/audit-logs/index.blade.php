@extends('layouts.app')

@section('title', 'Audit logs')
@section('page', 'audit-logs')

@section('content')
  <div class="page-header mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-1">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Audit logs</li>
      </ol>
    </nav>
    <h1 class="page-title">Audit logs</h1>
    <p class="page-subtitle">System activity and change history.</p>
  </div>

  <div class="card">
    <div class="table-toolbar">
      <form method="GET" action="{{ route('audit-logs.index') }}" class="d-flex flex-wrap gap-2 align-items-center">
        <select name="module" class="form-select form-select-sm" style="width:auto">
          <option value="">All modules</option>
          @foreach ($modules as $module)
            <option value="{{ $module }}" @selected(request('module') === $module)>{{ $module }}</option>
          @endforeach
        </select>
        <input type="text" name="action" class="form-control form-control-sm" style="width:auto" placeholder="Action" value="{{ request('action') }}">
        <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-funnel"></i> Filter</button>
      </form>
    </div>
    <div class="table-wrapper">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>When</th>
            <th>User</th>
            <th>Action</th>
            <th>Module</th>
            <th>Detail</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($logs as $log)
            <tr>
              <td class="text-nowrap">{{ $log->created_at?->format('d M Y H:i') ?? '—' }}</td>
              <td>{{ $log->user?->name ?? 'System' }}</td>
              <td><span class="badge bg-secondary">{{ $log->action }}</span></td>
              <td>{{ $log->module }}</td>
              <td class="text-truncate" style="max-width:280px">{{ $log->details ?? '—' }}</td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-secondary py-4">No audit entries.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @include('partials.pagination-footer', ['paginator' => $logs])
  </div>
@endsection
