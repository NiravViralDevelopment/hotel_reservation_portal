@php
  $e = $enquiry ?? null;
  $v = function (string $key, $fallback = '') use ($e) {
      if (old($key) !== null) {
          return old($key);
      }
      if ($e === null) {
          return $fallback;
      }
      $value = $e->{$key} ?? null;
      if ($value instanceof \DateTimeInterface) {
          return $value->format('Y-m-d');
      }

      return $value ?? $fallback;
  };
  $selectedStatus = old('status', $e?->status);
  $selectedBasis = old('basis', $e?->basis);
  $selectedDay = old('day', $e?->day);
@endphp

<div class="card mb-3">
  <div class="card-header">
    <h2 class="enquiry-section-title"><i class="bi bi-calendar3"></i> Dates</h2>
    <span class="enquiry-section-hint">Day comes from the arrival date. Nights are calculated from arrival and departure.</span>
  </div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-6 col-xl-3">
        <label for="enquiry_date" class="form-label">Enquiry Date <span class="text-danger">*</span></label>
        <div class="date-placeholder-wrap">
          <input type="date" name="enquiry_date" id="enquiry_date" class="form-control @error('enquiry_date') is-invalid @enderror" value="{{ $v('enquiry_date', $e ? '' : date('Y-m-d')) }}" required>
          <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
        </div>
        @error('enquiry_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6 col-xl-3">
        <label for="response_date" class="form-label">Response Date <span class="text-danger">*</span></label>
        <div class="date-placeholder-wrap">
          <input type="date" name="response_date" id="response_date" class="form-control @error('response_date') is-invalid @enderror" value="{{ $v('response_date') }}" required>
          <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
        </div>
        @error('response_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6 col-xl-3">
        <label for="check_in" class="form-label">Arrival Date <span class="text-danger">*</span></label>
        <div class="date-placeholder-wrap">
          <input type="date" name="check_in" id="check_in" class="form-control @error('check_in') is-invalid @enderror" value="{{ $v('check_in') }}" min="{{ now()->toDateString() }}" required>
          <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
        </div>
        @error('check_in')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6 col-xl-3">
        <label for="check_out" class="form-label">Departure Date <span class="text-danger">*</span></label>
        <div class="date-placeholder-wrap">
          <input type="date" name="check_out" id="check_out" class="form-control @error('check_out') is-invalid @enderror" value="{{ $v('check_out') }}" @if ($v('check_in')) min="{{ \Illuminate\Support\Carbon::parse($v('check_in'))->addDay()->toDateString() }}" @endif required>
          <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
        </div>
        @error('check_out')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6 col-xl-3">
        <label for="day" class="form-label">Day <span class="field-auto-badge">Auto</span></label>
        <select name="day" id="day" class="form-select day-auto @error('day') is-invalid @enderror" tabindex="-1" aria-readonly="true">
          <option value="">From arrival date</option>
          @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $weekday)
            <option value="{{ $weekday }}" @selected($selectedDay === $weekday)>{{ $weekday }}</option>
          @endforeach
        </select>
        @error('day')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6 col-xl-3">
        <label for="nights" class="form-label">Nights <span class="field-auto-badge">Auto</span></label>
        <input type="text" name="nights" id="nights" inputmode="numeric" class="form-control nights-auto enquiry-calc @error('nights') is-invalid @enderror" value="{{ $v('nights') }}" placeholder="From dates" readonly tabindex="-1" aria-readonly="true" required>
        @error('nights')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6 col-xl-3">
        <label for="rooms_per_night" class="form-label">Total Room per Night <span class="text-danger">*</span></label>
        <input type="text" name="rooms_per_night" id="rooms_per_night" inputmode="numeric" class="form-control js-digits @error('rooms_per_night') is-invalid @enderror" value="{{ $v('rooms_per_night') }}" placeholder="Enter total rooms" required>
        @error('rooms_per_night')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
    </div>
  </div>
</div>

<div class="card mb-3">
  <div class="card-header">
    <h2 class="enquiry-section-title"><i class="bi bi-people"></i> Group</h2>
    <span class="enquiry-section-hint">Who the enquiry is for, and how to reach them.</span>
  </div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-6">
        <label for="group_name" class="form-label">Group Name <span class="text-danger">*</span></label>
        <input type="text" name="group_name" id="group_name" class="form-control @error('group_name') is-invalid @enderror" value="{{ $v('group_name') }}" placeholder="Enter group name" maxlength="255" required>
        @error('group_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6">
        <label for="ref" class="form-label">Ref No <span class="text-danger">*</span></label>
        <input type="text" name="ref" id="ref" class="form-control @error('ref') is-invalid @enderror" value="{{ $v('ref') }}" placeholder="Enter ref no" maxlength="255" required>
        @error('ref')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6">
        <label for="email" class="form-label">Email ID <span class="text-danger">*</span></label>
        <input type="text" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ $v('email') }}" placeholder="Enter email" maxlength="255" inputmode="email" required>
        @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6">
        <label for="status" class="form-label">Status</label>
        <select name="status" id="status" class="form-select select2 @error('status') is-invalid @enderror" data-placeholder="Select status">
          <option value="">Select status</option>
          @foreach ($statuses as $status)
            <option value="{{ $status }}" @selected($selectedStatus === $status)>{{ $status }}</option>
          @endforeach
        </select>
        <div class="form-text">{{ $e ? 'Leave blank to keep the current status.' : 'Blank uses the default from Status Master.' }}</div>
        @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
    </div>
  </div>
</div>

<div class="card mb-3">
  <div class="card-header">
    <h2 class="enquiry-section-title"><i class="bi bi-door-open"></i> Rooms and rates</h2>
    <span class="enquiry-section-hint">Room dates run from arrival through the night before departure (checkout day is not charged).</span>
  </div>
  <div class="card-body">
    @php
      $dailySeed = old('daily_rooms');
      if (! is_array($dailySeed)) {
          $dailySeed = [];
          foreach (($e?->daily_room_rates ?? []) as $row) {
              if (is_array($row) && ! empty($row['date'])) {
                  $dailySeed[$row['date']] = $row;
              }
          }
      }
    @endphp
    <script type="application/json" id="daily-room-seed">@json($dailySeed)</script>
    <div class="stay-date-breakdown" id="stay-date-breakdown">
      <div id="stay-date-list"></div>
    </div>
  </div>
</div>

<div class="card mb-3">
  <div class="card-header">
    <h2 class="enquiry-section-title"><i class="bi bi-currency-pound"></i> Revenue and terms</h2>
    <span class="enquiry-section-hint">Total revenue updates as you enter rooms, rates, and nights.</span>
  </div>
  <div class="card-body">
    <div class="row g-3 align-items-start">
      <div class="col-md-6 col-xl-3">
        <label for="basis" class="form-label">Basis</label>
        <select name="basis" id="basis" class="form-select select2 @error('basis') is-invalid @enderror" data-placeholder="Select basis">
          <option value="">Select basis</option>
          @foreach (['BB', 'DBB', 'HB', 'FB', 'RO'] as $basis)
            <option value="{{ $basis }}" @selected($selectedBasis === $basis)>{{ $basis }}</option>
          @endforeach
        </select>
        @error('basis')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6 col-xl-3">
        <label for="option_date" class="form-label">Option Date</label>
        <div class="date-placeholder-wrap">
          <input type="date" name="option_date" id="option_date" class="form-control @error('option_date') is-invalid @enderror" value="{{ $v('option_date') }}">
          <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
        </div>
        @error('option_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-xl-6">
        <label for="cxl_policy" class="form-label">CXL Policy</label>
        <input type="text" name="cxl_policy" id="cxl_policy" class="form-control @error('cxl_policy') is-invalid @enderror" value="{{ $v('cxl_policy') }}" placeholder="e.g. 30 days" maxlength="255">
        <div class="form-text">Enter the number of days before arrival.</div>
        @error('cxl_policy')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6 col-xl-3">
        <label for="cxl_due_date" class="form-label">CXL Due Date <span class="field-auto-badge">Auto</span></label>
        <div class="date-placeholder-wrap">
          <input type="date" name="cxl_due_date" id="cxl_due_date" class="form-control cxl-due-auto @error('cxl_due_date') is-invalid @enderror" value="{{ $v('cxl_due_date') }}" readonly tabindex="-1" aria-readonly="true">
          <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
        </div>
        <div class="form-text">Arrival date minus the CXL Policy days.</div>
        @error('cxl_due_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="revenue-panel">
          <label for="total_revenue_display" class="form-label">Total Revenue</label>
          <input type="text" id="total_revenue_display" class="form-control" value="" placeholder="£0.00" readonly>
          <input type="hidden" name="total_revenue" id="total_revenue" value="{{ $v('total_revenue') }}">
          <div class="form-text">((Single × rate) + (Double × rate) + (Triple × rate)) × nights</div>
          @error('total_revenue')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
      </div>
      <div class="col-md-6 col-xl-9">
        <label for="remarks" class="form-label">Remarks</label>
        <textarea name="remarks" id="remarks" rows="3" class="form-control @error('remarks') is-invalid @enderror" placeholder="Enter remarks">{{ $v('remarks') }}</textarea>
        @error('remarks')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
    </div>
  </div>
</div>
