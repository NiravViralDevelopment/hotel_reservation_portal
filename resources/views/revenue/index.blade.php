@extends('layouts.app')

@section('title', 'Revenue')
@section('page', 'revenue')

@section('content')
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Revenue</li>
        </ol>
      </nav>
      <h1 class="page-title">Revenue — {{ $year }}</h1>
      <p class="page-subtitle">Year total: <strong>£{{ number_format((float) $yearTotal, 2) }}</strong></p>
    </div>
    <form method="GET" action="{{ route('revenue.index') }}" class="d-flex gap-2">
      <select name="year" class="form-select form-select-sm" style="width:auto">
        @for ($y = (int) date('Y') + 1; $y >= 2020; $y--)
          <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
        @endfor
      </select>
      <button type="submit" class="btn btn-outline-secondary btn-sm">Go</button>
    </form>
  </div>

  <div class="card">
    <div class="table-wrapper">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>Month</th>
            <th class="text-end">Room nights</th>
            <th class="text-end">Total revenue</th>
            <th class="text-end">BB revenue</th>
            <th class="text-end">Dinner revenue</th>
            <th class="text-end">BOB snapshot</th>
          </tr>
        </thead>
        <tbody>
          @for ($m = 1; $m <= 12; $m++)
            @php
              $row = $monthlyRevenue->get($m);
              $snap = $snapshots->get($m);
              $monthName = \Illuminate\Support\Carbon::create($year, $m, 1)->format('F');
            @endphp
            <tr>
              <td class="fw-semibold">{{ $monthName }}</td>
              <td class="text-end">{{ $row->room_nights ?? '—' }}</td>
              <td class="text-end">£{{ number_format((float) ($row->total_revenue ?? 0), 2) }}</td>
              <td class="text-end">£{{ number_format((float) ($row->bb_revenue ?? 0), 2) }}</td>
              <td class="text-end">£{{ number_format((float) ($row->dinner_revenue ?? 0), 2) }}</td>
              <td class="text-end">@if($snap)£{{ number_format((float) ($snap->bob_current ?? 0), 2) }}@else — @endif</td>
            </tr>
          @endfor
        </tbody>
        <tfoot class="table-light">
          <tr>
            <th>Total</th>
            <th></th>
            <th class="text-end">£{{ number_format((float) $yearTotal, 2) }}</th>
            <th colspan="3"></th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
@endsection
