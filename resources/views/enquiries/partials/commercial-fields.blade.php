@php
  $e = $enquiry ?? null;
  $val = function (string $key, $default = null) use ($e) {
      return old($key, $e?->{$key} ?? $default);
  };
@endphp

<div class="card mb-4">
  <div class="card-header">
    <h2 class="enquiry-section-title"><i class="bi bi-person-vcard"></i> Client &amp; booking details</h2>
  </div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-4">
        <label for="client" class="form-label">Client</label>
        <input type="text" name="client" id="client" class="form-control @error('client') is-invalid @enderror" value="{{ $val('client') }}" placeholder="Enter client name" maxlength="255">
        @error('client')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-4">
        <label for="mobile" class="form-label">Mobile no</label>
        <input type="text" name="mobile" id="mobile" class="form-control @error('mobile') is-invalid @enderror" value="{{ $val('mobile') }}" placeholder="Enter mobile number" maxlength="30">
        @error('mobile')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-4">
        <label for="source" class="form-label">Source</label>
        <input type="text" name="source" id="source" class="form-control @error('source') is-invalid @enderror" value="{{ $val('source') }}" placeholder="Enter source" maxlength="255">
        @error('source')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-4">
        <label for="service_person" class="form-label">Service person</label>
        <input type="text" name="service_person" id="service_person" class="form-control @error('service_person') is-invalid @enderror" value="{{ $val('service_person') }}" placeholder="Enter service person" maxlength="255">
        @error('service_person')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-8">
        <label for="subject" class="form-label">Subject</label>
        <input type="text" name="subject" id="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ $val('subject') }}" placeholder="Enter subject" maxlength="255">
        @error('subject')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6">
        <label for="booking_msg" class="form-label">Booking msg</label>
        <textarea name="booking_msg" id="booking_msg" rows="3" class="form-control @error('booking_msg') is-invalid @enderror" placeholder="Enter booking message">{{ $val('booking_msg') }}</textarea>
        @error('booking_msg')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6">
        <label for="breakdown" class="form-label">Breakdown</label>
        <textarea name="breakdown" id="breakdown" rows="3" class="form-control @error('breakdown') is-invalid @enderror" placeholder="Enter stay / pricing breakdown">{{ $val('breakdown') }}</textarea>
        @error('breakdown')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
    </div>
  </div>
</div>

<div class="card mb-4">
  <div class="card-header">
    <h2 class="enquiry-section-title"><i class="bi bi-cash-stack"></i> Pricing &amp; commission</h2>
  </div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-3">
        <label for="adults_price" class="form-label">Adults price</label>
        <input type="text" name="adults_price" id="adults_price" inputmode="decimal" class="form-control js-decimal enquiry-commercial-calc @error('adults_price') is-invalid @enderror" value="{{ $val('adults_price') }}" placeholder="0.00">
        @error('adults_price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-3">
        <label for="child_price" class="form-label">Child price</label>
        <input type="text" name="child_price" id="child_price" inputmode="decimal" class="form-control js-decimal enquiry-commercial-calc @error('child_price') is-invalid @enderror" value="{{ $val('child_price') }}" placeholder="0.00">
        @error('child_price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-3">
        <label for="adults_extra" class="form-label">Adults extra</label>
        <input type="text" name="adults_extra" id="adults_extra" inputmode="decimal" class="form-control js-decimal enquiry-commercial-calc @error('adults_extra') is-invalid @enderror" value="{{ $val('adults_extra') }}" placeholder="0.00">
        @error('adults_extra')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-3">
        <label for="child_extra" class="form-label">Child extra</label>
        <input type="text" name="child_extra" id="child_extra" inputmode="decimal" class="form-control js-decimal enquiry-commercial-calc @error('child_extra') is-invalid @enderror" value="{{ $val('child_extra') }}" placeholder="0.00">
        @error('child_extra')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-3">
        <label for="total_pax" class="form-label">Total no pax</label>
        <input type="text" name="total_pax" id="total_pax" inputmode="numeric" class="form-control js-digits @error('total_pax') is-invalid @enderror" value="{{ $val('total_pax') }}" placeholder="0">
        @error('total_pax')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-3">
        <label for="agent_price" class="form-label">Agent price</label>
        <input type="text" name="agent_price" id="agent_price" inputmode="decimal" class="form-control js-decimal enquiry-commercial-calc @error('agent_price') is-invalid @enderror" value="{{ $val('agent_price') }}" placeholder="0.00">
        @error('agent_price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-3">
        <label for="our_cost" class="form-label">Our cost</label>
        <input type="text" name="our_cost" id="our_cost" inputmode="decimal" class="form-control js-decimal enquiry-commercial-calc @error('our_cost') is-invalid @enderror" value="{{ $val('our_cost') }}" placeholder="0.00">
        @error('our_cost')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-3">
        <label for="package_price" class="form-label">P. price</label>
        <input type="text" name="package_price" id="package_price" inputmode="decimal" class="form-control js-decimal enquiry-commercial-calc @error('package_price') is-invalid @enderror" value="{{ $val('package_price') }}" placeholder="0.00">
        @error('package_price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-4">
        <label for="gst_policy" class="form-label">GST policy</label>
        <input type="text" name="gst_policy" id="gst_policy" class="form-control @error('gst_policy') is-invalid @enderror" value="{{ $val('gst_policy') }}" placeholder="Enter GST policy" maxlength="255">
        @error('gst_policy')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-4">
        <label for="total_price" class="form-label">Total price</label>
        <input type="text" name="total_price" id="total_price" inputmode="decimal" class="form-control js-decimal enquiry-commercial-calc @error('total_price') is-invalid @enderror" value="{{ $val('total_price') }}" placeholder="Auto / enter">
        <div class="form-text">Auto from adults/child prices + extras when left blank.</div>
        @error('total_price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-4">
        <label for="net_price" class="form-label">Net price</label>
        <input type="text" name="net_price" id="net_price" inputmode="decimal" class="form-control js-decimal enquiry-commercial-calc @error('net_price') is-invalid @enderror" value="{{ $val('net_price') }}" placeholder="Auto / enter">
        @error('net_price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-3">
        <label for="advance" class="form-label">Advance</label>
        <input type="text" name="advance" id="advance" inputmode="decimal" class="form-control js-decimal enquiry-commercial-calc @error('advance') is-invalid @enderror" value="{{ $val('advance') }}" placeholder="0.00">
        @error('advance')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-3">
        <label for="remaining" class="form-label">Remaining</label>
        <input type="text" name="remaining" id="remaining" inputmode="decimal" class="form-control @error('remaining') is-invalid @enderror" value="{{ $val('remaining') }}" placeholder="Auto" readonly>
        <div class="form-text">Grand total − advance.</div>
        @error('remaining')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-3">
        <label for="agent_comm_percent" class="form-label">Agent comm %</label>
        <input type="text" name="agent_comm_percent" id="agent_comm_percent" inputmode="decimal" class="form-control js-decimal enquiry-commercial-calc @error('agent_comm_percent') is-invalid @enderror" value="{{ $val('agent_comm_percent') }}" placeholder="0">
        @error('agent_comm_percent')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-3">
        <label for="agent_comm_amount" class="form-label">Agent comm amt</label>
        <input type="text" name="agent_comm_amount" id="agent_comm_amount" inputmode="decimal" class="form-control @error('agent_comm_amount') is-invalid @enderror" value="{{ $val('agent_comm_amount') }}" placeholder="Auto" readonly>
        @error('agent_comm_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-3">
        <label for="payable_to_agent" class="form-label">Payable to agent</label>
        <input type="text" name="payable_to_agent" id="payable_to_agent" inputmode="decimal" class="form-control @error('payable_to_agent') is-invalid @enderror" value="{{ $val('payable_to_agent') }}" placeholder="Auto" readonly>
        @error('payable_to_agent')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-3">
        <label for="service_total" class="form-label">Service total</label>
        <input type="text" name="service_total" id="service_total" inputmode="decimal" class="form-control js-decimal enquiry-commercial-calc @error('service_total') is-invalid @enderror" value="{{ $val('service_total') }}" placeholder="0.00">
        @error('service_total')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-3">
        <label for="total_tax" class="form-label">Total tax</label>
        <input type="text" name="total_tax" id="total_tax" inputmode="decimal" class="form-control js-decimal enquiry-commercial-calc @error('total_tax') is-invalid @enderror" value="{{ $val('total_tax') }}" placeholder="0.00">
        @error('total_tax')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-3">
        <label for="grand_total" class="form-label">Grand total</label>
        <input type="text" name="grand_total" id="grand_total" inputmode="decimal" class="form-control @error('grand_total') is-invalid @enderror" value="{{ $val('grand_total') }}" placeholder="Auto" readonly>
        <div class="form-text">Total price + service total + total tax.</div>
        @error('grand_total')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
    </div>
  </div>
</div>
