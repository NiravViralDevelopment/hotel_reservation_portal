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
    <span class="enquiry-section-hint">Day is filled in from the arrival date.</span>
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
          <input type="date" name="check_in" id="check_in" class="form-control @error('check_in') is-invalid @enderror" value="{{ $v('check_in') }}" required>
          <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
        </div>
        @error('check_in')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
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
        <label for="nights" class="form-label">Nights <span class="text-danger">*</span></label>
        <input type="text" name="nights" id="nights" inputmode="numeric" class="form-control js-digits enquiry-calc @error('nights') is-invalid @enderror" value="{{ $v('nights') }}" placeholder="Enter nights" required>
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
        <label for="ref" class="form-label">Ref No</label>
        <input type="text" name="ref" id="ref" class="form-control @error('ref') is-invalid @enderror" value="{{ $v('ref') }}" placeholder="Enter ref no" maxlength="255">
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
    <span class="enquiry-section-hint">Enter the room count and nightly rate for each type.</span>
  </div>
  <div class="card-body">
    <div class="row g-3">
      @foreach ([
        'single' => ['label' => 'Single', 'icon' => 'bi-person'],
        'double' => ['label' => 'Double', 'icon' => 'bi-people'],
        'triple' => ['label' => 'Triple', 'icon' => 'bi-people-fill'],
      ] as $type => $meta)
        <div class="col-md-4">
          <div class="room-rate-card">
            <h3><i class="bi {{ $meta['icon'] }}"></i> {{ $meta['label'] }}</h3>
            <div class="mb-3">
              <label for="{{ $type }}_rooms" class="form-label">Rooms</label>
              <input type="text" name="{{ $type }}_rooms" id="{{ $type }}_rooms" inputmode="numeric" class="form-control js-digits enquiry-calc @error($type.'_rooms') is-invalid @enderror" value="{{ $v($type.'_rooms') }}" placeholder="Rooms">
              @error($type.'_rooms')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div>
              <label for="{{ $type }}_rate" class="form-label">Rate</label>
              <input type="text" name="{{ $type }}_rate" id="{{ $type }}_rate" inputmode="decimal" class="form-control js-decimal enquiry-calc @error($type.'_rate') is-invalid @enderror" value="{{ $v($type.'_rate') }}" placeholder="Nightly rate">
              @error($type.'_rate')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>
      @endforeach
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
      <div class="col-md-6 col-xl-3">
        <label for="cxl_due_date" class="form-label">CXL Due Date</label>
        <div class="date-placeholder-wrap">
          <input type="date" name="cxl_due_date" id="cxl_due_date" class="form-control @error('cxl_due_date') is-invalid @enderror" value="{{ $v('cxl_due_date') }}">
          <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
        </div>
        @error('cxl_due_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-xl-6">
        <label for="cxl_policy" class="form-label">CXL Policy</label>
        <input type="text" name="cxl_policy" id="cxl_policy" class="form-control @error('cxl_policy') is-invalid @enderror" value="{{ $v('cxl_policy') }}" placeholder="Enter CXL policy" maxlength="255">
        @error('cxl_policy')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="revenue-panel">
          <label for="total_revenue_display" class="form-label">Total Revenue</label>
          <input type="text" id="total_revenue_display" class="form-control" value="" placeholder="£0.00" readonly>
          <input type="hidden" name="total_revenue" id="total_revenue" value="{{ $v('total_revenue') }}">
          <div class="form-text">Rooms × rates, then × nights.</div>
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
