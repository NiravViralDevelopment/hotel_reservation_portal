@extends('layouts.app')

@section('title', 'BOB — '.$selectedYear)
@section('page', 'bob')

@php
  $formatCell = function ($value, string $format) {
      if ($format === 'dash') {
          return '-';
      }

      if ($value === null || $value === '') {
          return '—';
      }

      $number = (float) $value;
      $negative = $number < 0;
      $formatted = number_format(abs($number), $format === 'money' ? 2 : 0);

      if ($format === 'money') {
          return ($negative ? '-£' : '£').$formatted;
      }

      return ($negative ? '-' : '').$formatted;
  };
@endphp

@section('content')
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">BOB</li>
        </ol>
      </nav>
      <h1 class="page-title">BOB</h1>
      <p class="page-subtitle mb-0">Confirmed group bookings for {{ $selectedYear }}, grouped by arrival date. Manual cells are saved against {{ $previousYear }}.</p>
    </div>

    <form method="GET" action="{{ route('bob.index') }}" class="d-flex flex-wrap gap-2 align-items-center">
      <label for="bob-year" class="form-label mb-0 small text-secondary">Year</label>
      <select id="bob-year" name="year" class="form-select form-select-sm select2" style="width: auto; min-width: 7rem;">
        @foreach ($yearOptions as $year)
          <option value="{{ $year }}" @selected($selectedYear === (int) $year)>{{ $year }}</option>
        @endforeach
      </select>
      <button type="submit" class="btn btn-outline-secondary btn-sm">Apply</button>
      @if ($selectedYear !== (int) now()->year)
        <a href="{{ route('bob.index') }}" class="btn btn-outline-danger btn-sm">
          <i class="bi bi-x-circle"></i> Clear
        </a>
      @endif
    </form>
  </div>

  <form method="POST" action="{{ route('bob.update') }}" id="bob-manual-form">
    @csrf
    <input type="hidden" name="year" value="{{ $selectedYear }}">

    <div class="card">
      <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <span>Monthly figures</span>
        <span class="d-flex flex-wrap align-items-center gap-2">
          <span class="small text-secondary">{{ $selectedYear }} and {{ $previousYear }}</span>
          <a
            href="{{ route('bob.export', ['year' => $selectedYear]) }}"
            class="btn btn-outline-secondary btn-sm"
            title="Download these monthly figures as Excel"
          >
            <i class="bi bi-download"></i> Export Excel
          </a>
          <button type="submit" class="btn btn-accent btn-sm">Save manual figures</button>
        </span>
      </div>
      <div class="table-scroll-hint"><i class="bi bi-arrows-expand"></i> Scroll horizontally to see all columns</div>
      <div class="table-wrapper table-scroll-wide bob-table-scroll">
        <table class="table table-hover table-sm mb-0 bob-table">
          <thead>
            <tr>
              <th rowspan="2" class="bob-sticky" scope="col">Month</th>
              @foreach ($groups as $group)
                <th colspan="{{ $group['span'] }}" class="text-center bob-group bob-group-{{ $group['key'] }}" scope="colgroup">{{ $group['label'] }}</th>
              @endforeach
            </tr>
            <tr>
              @foreach ($columns as $column)
                <th class="text-end bob-col-{{ $column['group'] }}" scope="col">{{ $column['label'] }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            @foreach ($months as $month)
              <tr data-bob="{{ $month['bob'] }}" data-nights="{{ $month['nights'] }}">
                <th class="bob-sticky fw-semibold" scope="row">{{ $month['label'] }}</th>
                @foreach ($month['cells'] as $cell)
                  @if ($cell['editable'])
                    <td class="bob-col-{{ $cell['group'] }} bob-editable">
                      <input
                        type="text"
                        inputmode="{{ $cell['format'] === 'number' ? 'numeric' : 'decimal' }}"
                        name="months[{{ $month['month'] }}][{{ $cell['input'] }}]"
                        value="{{ old('months.'.$month['month'].'.'.$cell['input'], $cell['raw']) }}"
                        placeholder="-"
                        class="form-control form-control-sm bob-manual"
                        data-manual="{{ $cell['input'] }}"
                        data-format="{{ $cell['format'] }}"
                        aria-label="{{ $cell['label'] }} for {{ $month['label'] }}"
                        autocomplete="off"
                      >
                    </td>
                  @else
                    <td
                      class="text-end bob-col-{{ $cell['group'] }}{{ $cell['value'] === null ? ' text-secondary' : '' }}"
                      @if ($cell['formula']) data-formula="{{ $cell['key'] }}" @endif
                    >
                      {{ $formatCell($cell['value'], $cell['format']) }}
                    </td>
                  @endif
                @endforeach
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </form>
@endsection

@push('scripts')
  <script>
    (function () {
      var table = document.querySelector('.bob-table');
      if (!table) return;

      function num(value) {
        if (value === null || value === undefined || String(value).trim() === '') return null;
        var parsed = Number(value);
        return Number.isFinite(parsed) ? parsed : null;
      }

      function money(value) {
        if (value === null) return '—';
        var negative = value < 0;
        var text = Math.abs(value).toLocaleString('en-GB', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        return (negative ? '-£' : '£') + text;
      }

      function paint(cell, value) {
        cell.textContent = value === null ? '—' : money(Math.round(value * 100) / 100);
        cell.classList.toggle('text-secondary', value === null);
      }

      function refresh() {
        table.querySelectorAll('tbody tr').forEach(function (row) {
          var bob = num(row.getAttribute('data-bob'));
          var nights = num(row.getAttribute('data-nights'));
          var actual = num(row.querySelector('[data-manual="bob_nett_actual"]') && row.querySelector('[data-manual="bob_nett_actual"]').value);
          var stlyInput = num(row.querySelector('[data-manual="stly_bob_nett"]') && row.querySelector('[data-manual="stly_bob_nett"]').value);
          var stly = stlyInput === null ? 0 : stlyInput;
          var stlyNights = num(row.querySelector('[data-manual="stly_room_nights"]') && row.querySelector('[data-manual="stly_room_nights"]').value);
          var adrPrevious = num(row.querySelector('[data-manual="adr_nett"]') && row.querySelector('[data-manual="adr_nett"]').value);
          var adr = (bob !== null && nights) ? Math.round((bob / nights) * 100) / 100 : null;
          var stlyAdr = (stly !== null && stlyNights) ? Math.round((stly / stlyNights) * 100) / 100 : null;

          var values = {
            bob_variance_vs_previous: (bob !== null && actual !== null) ? bob - actual : null,
            bob_variance_stly_vs_current: (bob !== null && stly !== null) ? bob - stly : null,
            adr_nett: adr,
            adr_variance_vs_previous: (adr !== null && adrPrevious !== null) ? adr - adrPrevious : null,
            stly_adr_nett: stlyAdr
          };

          Object.keys(values).forEach(function (key) {
            var cell = row.querySelector('[data-formula="' + key + '"]');
            if (cell) paint(cell, values[key]);
          });
        });
      }

      function plain(value) {
        return String(value === null || value === undefined ? '' : value).replace(/[£,\s]/g, '').trim();
      }

      function zeroish(value) {
        return /^-?0+(?:\.0+)?$/.test(plain(value));
      }

      function tidy(input) {
        var integer = input.getAttribute('data-format') === 'number';
        var raw = plain(input.value).replace(integer ? /[^\d-]/g : /[^\d.-]/g, '');
        if (integer) {
          raw = raw.replace(/(?!^)-/g, '');
        } else {
          raw = raw.replace(/(?!^)-/g, '');
          var pieces = raw.split('.');
          if (pieces.length > 2) {
            raw = pieces[0] + '.' + pieces.slice(1).join('');
          }
        }

        if (raw === '' || raw === '-' || raw === '.' || raw === '-.' || zeroish(raw)) {
          input.value = '';
          return;
        }

        var parsed = Number(raw);
        if (!Number.isFinite(parsed)) {
          input.value = '';
          return;
        }

        if (integer) {
          var whole = Math.trunc(parsed);
          input.value = whole > 0 ? String(whole) : '';
          return;
        }

        var rounded = Math.round(parsed * 100) / 100;
        input.value = rounded === 0 ? '' : String(rounded);
      }

      table.querySelectorAll('.bob-manual').forEach(function (input) {
        tidy(input);

        function selectAll() {
          if (input.value === '') return;
          input.setSelectionRange(0, input.value.length);
        }

        input.addEventListener('focus', function () {
          if (input.value.trim() === '' || zeroish(input.value)) {
            input.value = '';
            return;
          }
          selectAll();
        });

        input.addEventListener('mouseup', function (event) {
          if (input.value === '') return;
          event.preventDefault();
          selectAll();
        });

        input.addEventListener('blur', function () {
          tidy(input);
          refresh();
        });
      });

      var form = document.getElementById('bob-manual-form');
      if (form) {
        form.addEventListener('submit', function () {
          table.querySelectorAll('.bob-manual').forEach(tidy);
        });
      }

      table.addEventListener('input', refresh);
      refresh();
    })();
  </script>
@endpush
