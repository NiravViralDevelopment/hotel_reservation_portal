@php
  $v = function (string $key, $fallback = '') use ($enquiry) {
      if (old($key) !== null) {
          return old($key);
      }
      $value = $enquiry->{$key} ?? null;
      if ($value instanceof \DateTimeInterface) {
          return $value->format('Y-m-d');
      }

      return $value === null || $value === '' ? $fallback : $value;
  };
  $contactValue = old('contact_name');
  if ($contactValue === null) {
      $contactValue = $enquiry->contact_name ?: ($enquiry->contact?->name ?? '');
  }
  $selectedBasis = old('basis');
  if ($selectedBasis === null) {
      $selectedBasis = in_array($enquiry->basis, ['BB', 'DBB'], true) ? $enquiry->basis : '';
  }
  $selectedDay = old('day', $enquiry->day);
@endphp

<form method="POST" action="{{ route('enquiries.group-booking.store', $enquiry) }}" class="enquiry-form" id="group-booking-form">
  @csrf
  <input type="hidden" name="form_context" value="group">
  <div id="gb-daily-room-fields" hidden></div>
  <script type="application/json" id="gb-daily-room-seed">@json($enquiry->daily_room_rates ?? [])</script>

  <div class="card mb-3">
    <div class="card-header">
      <h2 class="enquiry-section-title"><i class="bi bi-calendar3"></i> Stay</h2>
      <span class="enquiry-section-hint">Day is filled in from the date of arrival.</span>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6 col-xl-3">
          <label for="gb_check_in" class="form-label">Date of Arrival <span class="text-danger">*</span></label>
          <div class="date-placeholder-wrap">
            <input type="date" name="check_in" id="gb_check_in" class="form-control @error('check_in') is-invalid @enderror" value="{{ $v('check_in') }}" min="{{ now()->toDateString() }}" required>
            <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
          </div>
          @error('check_in')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 col-xl-3">
          <label for="gb_check_out" class="form-label">Date of Departure <span class="text-danger">*</span></label>
          <div class="date-placeholder-wrap">
            <input type="date" name="check_out" id="gb_check_out" class="form-control @error('check_out') is-invalid @enderror" value="{{ $v('check_out') }}" required>
            <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
          </div>
          @error('check_out')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 col-xl-3">
          <label for="gb_day" class="form-label">Day <span class="field-auto-badge">Auto</span></label>
          <select name="day" id="gb_day" class="form-select day-auto @error('day') is-invalid @enderror" tabindex="-1" aria-readonly="true">
            <option value="">From arrival date</option>
            @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $weekday)
              <option value="{{ $weekday }}" @selected($selectedDay === $weekday)>{{ $weekday }}</option>
            @endforeach
          </select>
          @error('day')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 col-xl-3">
          <label for="gb_nights" class="form-label">No. of Nights <span class="text-danger">*</span></label>
          <input type="text" name="nights" id="gb_nights" inputmode="numeric" class="form-control js-digits gb-calc @error('nights') is-invalid @enderror" value="{{ $v('nights') }}" required>
          @error('nights')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 col-xl-3">
          <label for="gb_block_id" class="form-label">Block ID <span class="text-danger">*</span></label>
          <input type="text" name="block_id" id="gb_block_id" class="form-control @error('block_id') is-invalid @enderror" value="{{ $v('block_id') }}" maxlength="255" required>
          @error('block_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header">
      <h2 class="enquiry-section-title"><i class="bi bi-person-vcard"></i> Client</h2>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label for="gb_client" class="form-label">Client</label>
          <input type="text" name="client" id="gb_client" class="form-control @error('client') is-invalid @enderror" value="{{ $v('client') }}" maxlength="255">
          @error('client')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label for="gb_agency_ref" class="form-label">Agency - Ref</label>
          <input type="text" name="agency_ref" id="gb_agency_ref" class="form-control @error('agency_ref') is-invalid @enderror" value="{{ $v('agency_ref') }}" maxlength="255">
          @error('agency_ref')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label for="gb_contact_name" class="form-label">Contact</label>
          <input type="text" name="contact_name" id="gb_contact_name" class="form-control @error('contact_name') is-invalid @enderror" value="{{ $contactValue }}" maxlength="255">
          @error('contact_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label for="gb_email" class="form-label">Email ID</label>
          <input type="text" name="email" id="gb_email" class="form-control @error('email') is-invalid @enderror" value="{{ $v('email') }}" maxlength="255" inputmode="email">
          @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header">
      <h2 class="enquiry-section-title"><i class="bi bi-file-earmark-text"></i> Contract and payment</h2>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6 col-xl-4">
          <label for="gb_contract_sent_on" class="form-label">Contract Sent On</label>
          <div class="date-placeholder-wrap">
            <input type="date" name="contract_sent_on" id="gb_contract_sent_on" class="form-control @error('contract_sent_on') is-invalid @enderror" value="{{ $v('contract_sent_on') }}">
            <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
          </div>
          @error('contract_sent_on')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 col-xl-4">
          <label for="gb_contract_received_on" class="form-label">Contract Recd On</label>
          <div class="date-placeholder-wrap">
            <input type="date" name="contract_received_on" id="gb_contract_received_on" class="form-control @error('contract_received_on') is-invalid @enderror" value="{{ $v('contract_received_on') }}">
            <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
          </div>
          @error('contract_received_on')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 col-xl-4">
          <label for="gb_saved_to_doc" class="form-label">Saved to Doc</label>
          <input type="text" name="saved_to_doc" id="gb_saved_to_doc" class="form-control @error('saved_to_doc') is-invalid @enderror" value="{{ $v('saved_to_doc') }}" maxlength="255">
          @error('saved_to_doc')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        @php
          $termSubmitted = old('form_context') === 'group';
          $selectedPaymentTerm = $termSubmitted ? old('payment_term') : ($enquiry->payment_term ?? null);
          $paymentTermDays = $termSubmitted ? old('payment_term_days') : ($enquiry->payment_term_days ?? '');
        @endphp
        <div class="col-md-6 col-xl-4">
          <span class="form-label d-block">Payment Term</span>
          <div class="form-check">
            <input class="form-check-input js-payment-term" type="checkbox" name="payment_term" id="gb_payment_term_pre" value="Pre Arrival" @checked($selectedPaymentTerm === 'Pre Arrival')>
            <label class="form-check-label" for="gb_payment_term_pre">Pre Arrival</label>
          </div>
          <div class="form-check mb-0">
            <input class="form-check-input js-payment-term" type="checkbox" name="payment_term" id="gb_payment_term_post" value="Post Departure" @checked($selectedPaymentTerm === 'Post Departure')>
            <label class="form-check-label" for="gb_payment_term_post">Post Departure</label>
          </div>
          @error('payment_term')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          <div id="gb_payment_term_days_wrap" class="mt-2" @unless (in_array($selectedPaymentTerm, ['Pre Arrival', 'Post Departure'], true)) hidden @endunless>
            <label for="gb_payment_term_days" class="form-label">Number of days</label>
            <input type="text" name="payment_term_days" id="gb_payment_term_days" inputmode="numeric" class="form-control js-digits @error('payment_term_days') is-invalid @enderror" value="{{ $paymentTermDays }}" placeholder="Enter days" maxlength="3">
            @error('payment_term_days')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="col-md-6 col-xl-4">
          <label for="gb_payment_due_date" class="form-label">Due Date <span class="field-auto-badge">Auto</span></label>
          <div class="date-placeholder-wrap">
            <input type="date" name="payment_due_date" id="gb_payment_due_date" class="form-control cxl-due-auto @error('payment_due_date') is-invalid @enderror" value="{{ $v('payment_due_date') }}" readonly tabindex="-1" aria-readonly="true">
            <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
          </div>
          <div class="form-text" id="gb_payment_due_hint">Select a payment term and enter the number of days.</div>
          @error('payment_due_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 col-xl-4">
          <label for="gb_payment_status" class="form-label">Payment Status</label>
          <input type="text" name="payment_status" id="gb_payment_status" class="form-control @error('payment_status') is-invalid @enderror" value="{{ $v('payment_status') }}" maxlength="255">
          @error('payment_status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header">
      <h2 class="enquiry-section-title"><i class="bi bi-shield-exclamation"></i> Cancellation and commission</h2>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6 col-xl-3">
          <label for="gb_cxl_policy" class="form-label">CXL Policy</label>
          <input type="text" name="cxl_policy" id="gb_cxl_policy" class="form-control @error('cxl_policy') is-invalid @enderror" value="{{ $v('cxl_policy') }}" maxlength="255">
          @error('cxl_policy')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 col-xl-3">
          <label for="gb_cxl_due_date" class="form-label">CXL Due Date</label>
          <div class="date-placeholder-wrap">
            <input type="date" name="cxl_due_date" id="gb_cxl_due_date" class="form-control @error('cxl_due_date') is-invalid @enderror" value="{{ $v('cxl_due_date') }}">
            <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
          </div>
          @error('cxl_due_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        @php
          $commissionSubmitted = old('form_context') === 'group';
          $selectedHasCommission = $commissionSubmitted
              ? old('has_commission')
              : ($enquiry->has_commission === null ? null : ($enquiry->has_commission ? '1' : '0'));
          $selectedCommissionPayable = $commissionSubmitted
              ? old('commission_payable_status')
              : ($enquiry->commission_payable_status ?? '');
        @endphp
        <div class="col-md-6 col-xl-3">
          <span class="form-label d-block">Commission</span>
          <div class="form-check">
            <input class="form-check-input js-has-commission" type="checkbox" name="has_commission" id="gb_has_commission_yes" value="1" @checked($selectedHasCommission === '1' || $selectedHasCommission === 1 || $selectedHasCommission === true)>
            <label class="form-check-label" for="gb_has_commission_yes">Yes</label>
          </div>
          <div class="form-check mb-0">
            <input class="form-check-input js-has-commission" type="checkbox" name="has_commission" id="gb_has_commission_no" value="0" @checked($selectedHasCommission === '0' || $selectedHasCommission === 0 || $selectedHasCommission === false)>
            <label class="form-check-label" for="gb_has_commission_no">No</label>
          </div>
          @error('has_commission')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header">
      <h2 class="enquiry-section-title"><i class="bi bi-door-open"></i> Rooms and rates</h2>
      <span class="enquiry-section-hint" id="gb_rooms_hint">Room counts, rates, and nights update Total RNs, Total Rev, BB Revenue, and Nett Rev.</span>
    </div>
    <div class="card-body">
      <div class="row g-3">
        @foreach ([
          'single' => ['label' => 'Single', 'icon' => 'bi-person', 'rooms' => 'Single RNs', 'rate' => 'Single Gross Rate'],
          'double' => ['label' => 'Double', 'icon' => 'bi-people', 'rooms' => 'Double RNs', 'rate' => 'Double Gross Rate'],
          'triple' => ['label' => 'Triple', 'icon' => 'bi-people-fill', 'rooms' => 'Triple RNs', 'rate' => 'Triple Gross Rate'],
        ] as $type => $meta)
          <div class="col-md-4">
            <div class="room-rate-card">
              <h3><i class="bi {{ $meta['icon'] }}"></i> {{ $meta['label'] }}</h3>
              <div class="mb-3">
                <label for="gb_{{ $type }}_rooms" class="form-label">{{ $meta['rooms'] }}</label>
                <input type="text" name="{{ $type }}_rooms" id="gb_{{ $type }}_rooms" inputmode="numeric" class="form-control js-digits gb-calc @error($type.'_rooms') is-invalid @enderror" value="{{ $v($type.'_rooms') }}">
                @error($type.'_rooms')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>
              <div>
                <label for="gb_{{ $type }}_rate" class="form-label">{{ $meta['rate'] }}</label>
                <input type="text" name="{{ $type }}_rate" id="gb_{{ $type }}_rate" inputmode="decimal" class="form-control js-decimal gb-calc @error($type.'_rate') is-invalid @enderror" value="{{ $v($type.'_rate') }}">
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
      <h2 class="enquiry-section-title"><i class="bi bi-currency-pound"></i> Revenue</h2>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6 col-xl-3">
          <div class="revenue-panel">
            <label for="gb_total_rns_display" class="form-label">Total RNs</label>
            <input type="text" id="gb_total_rns_display" class="form-control" value="" readonly>
            <input type="hidden" name="total_rns" id="gb_total_rns" value="{{ $v('total_rns') }}">
            <div class="form-text" id="gb_total_rns_hint">(Single + Double + Triple) × nights</div>
          </div>
        </div>
        <div class="col-md-6 col-xl-3">
          <div class="revenue-panel">
            <label for="gb_total_revenue_display" class="form-label">Total Rev</label>
            <input type="text" id="gb_total_revenue_display" class="form-control" value="" placeholder="£0.00" readonly>
            <input type="hidden" name="total_revenue" id="gb_total_revenue" value="{{ $v('total_revenue') }}">
            <div class="form-text" id="gb_total_revenue_hint">((Single × rate) + (Double × rate) + (Triple × rate)) × nights</div>
          </div>
        </div>
        <div class="col-md-6 col-xl-3">
          <div class="revenue-panel">
            <label for="gb_bb_revenue_display" class="form-label">BB Revenue (Nett £)</label>
            <input type="text" id="gb_bb_revenue_display" class="form-control @error('bb_revenue') is-invalid @enderror" value="" readonly>
            <input type="hidden" name="bb_revenue" id="gb_bb_revenue" value="{{ $v('bb_revenue') }}">
            <div class="form-text" id="gb_bb_revenue_hint">(Single × 10 + Double × 20 + Triple × 30) × nights</div>
            @error('bb_revenue')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="col-md-6 col-xl-3">
          <div class="revenue-panel">
            <label for="gb_dinner_revenue_display" class="form-label">Dinner Revenue (Nett £)</label>
            <input type="text" id="gb_dinner_revenue_display" class="form-control @error('dinner_revenue') is-invalid @enderror" value="" readonly>
            <input type="hidden" name="dinner_revenue" id="gb_dinner_revenue" value="{{ $v('dinner_revenue') }}">
            <div class="form-text">£0</div>
            @error('dinner_revenue')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="col-md-6 col-xl-3">
          <div class="revenue-panel">
            <label for="gb_nett_rev_ex_vat_display" class="form-label">Nett Rev EX VAT &amp; BF</label>
            <input type="text" id="gb_nett_rev_ex_vat_display" class="form-control @error('nett_rev_ex_vat') is-invalid @enderror" value="" readonly>
            <input type="hidden" name="nett_rev_ex_vat" id="gb_nett_rev_ex_vat" value="{{ $v('nett_rev_ex_vat') }}">
            <div class="form-text">(Total Rev × 100 / 120) − BB Revenue</div>
            @error('nett_rev_ex_vat')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="col-md-6 col-xl-3">
          <label for="gb_basis" class="form-label">BB/DBB</label>
          <select name="basis" id="gb_basis" class="form-select @error('basis') is-invalid @enderror">
            <option value="">Select BB/DBB</option>
            @foreach (['BB', 'DBB'] as $basis)
              <option value="{{ $basis }}" @selected($selectedBasis === $basis)>{{ $basis }}</option>
            @endforeach
          </select>
          @error('basis')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header">
      <h2 class="enquiry-section-title"><i class="bi bi-receipt"></i> Rooming and invoice</h2>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6 col-xl-4">
          <label for="gb_booking_update" class="form-label">Update</label>
          <input type="text" name="booking_update" id="gb_booking_update" class="form-control @error('booking_update') is-invalid @enderror" value="{{ $v('booking_update') }}" maxlength="255">
          @error('booking_update')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 col-xl-4">
          <label for="gb_rooming" class="form-label">Rooming</label>
          <input type="text" name="rooming" id="gb_rooming" class="form-control @error('rooming') is-invalid @enderror" value="{{ $v('rooming') }}" maxlength="255">
          @error('rooming')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 col-xl-4">
          <label for="gb_invoice_status" class="form-label">Invoice Status</label>
          <input type="text" name="invoice_status" id="gb_invoice_status" class="form-control @error('invoice_status') is-invalid @enderror" value="{{ $v('invoice_status') }}" maxlength="255">
          @error('invoice_status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 col-xl-4">
          <label for="gb_invoice_sent_on" class="form-label">Invoice Sent On</label>
          <div class="date-placeholder-wrap">
            <input type="date" name="invoice_sent_on" id="gb_invoice_sent_on" class="form-control @error('invoice_sent_on') is-invalid @enderror" value="{{ $v('invoice_sent_on') }}">
            <span class="date-placeholder" aria-hidden="true">DD/MM/YYYY</span>
          </div>
          @error('invoice_sent_on')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 col-xl-4">
          <label for="gb_invoice_amount" class="form-label">Invoice Amount</label>
          <input type="text" name="invoice_amount" id="gb_invoice_amount" inputmode="decimal" class="form-control js-decimal @error('invoice_amount') is-invalid @enderror" value="{{ $v('invoice_amount') }}">
          @error('invoice_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 col-xl-4" id="gb_commission_payable_wrap" @unless ($selectedHasCommission === '1' || $selectedHasCommission === 1 || $selectedHasCommission === true) hidden @endunless>
          <label for="gb_commission_payable_status" class="form-label">Commission Payable Status</label>
          <select name="commission_payable_status" id="gb_commission_payable_status" class="form-select @error('commission_payable_status') is-invalid @enderror">
            <option value="">Select status</option>
            <option value="Pending" @selected($selectedCommissionPayable === 'Pending')>Pending</option>
            <option value="Received" @selected($selectedCommissionPayable === 'Received')>Received</option>
          </select>
          @error('commission_payable_status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
      </div>
    </div>
  </div>

  <div class="enquiry-sticky-actions">
    <button type="submit" class="btn btn-accent"><i class="bi bi-check-lg"></i> Submit group booking</button>
    @if ($enquiry->is_confirm && ! $enquiry->is_cancel)
      <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelGroupBookingModal">
        <i class="bi bi-x-circle"></i> Cancel group booking
      </button>
    @endif
    @isset($groupBookingBackUrl)
      <a href="{{ $groupBookingBackUrl }}" class="btn btn-outline-secondary">Back</a>
    @endisset
  </div>
</form>

@if ($enquiry->is_confirm && ! $enquiry->is_cancel)
  <div class="modal fade" id="cancelGroupBookingModal" tabindex="-1" aria-labelledby="cancelGroupBookingModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form method="POST" action="{{ route('enquiries.cancel-booking', $enquiry) }}">
          @csrf
          <input type="hidden" name="cancel_scope" value="group">
          <div class="modal-header">
            <h5 class="modal-title" id="cancelGroupBookingModalTitle">Cancel group booking</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p class="text-secondary">Cancel this group booking? The status will be set to Cancelled and it will move to Cancelled Bookings.</p>
            <label for="booking_cancellation_reason" class="form-label">Cancellation reason <span class="text-danger">*</span></label>
            <textarea name="cancellation_reason" id="booking_cancellation_reason" rows="4" class="form-control @error('cancellation_reason') is-invalid @enderror" required maxlength="2000" placeholder="Enter the cancellation reason">{{ old('cancel_scope') === 'group' ? old('cancellation_reason') : '' }}</textarea>
            @if (old('cancel_scope') === 'group')
              @error('cancellation_reason')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            @endif
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Back</button>
            <button type="submit" class="btn btn-danger">Cancel group booking</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endif
