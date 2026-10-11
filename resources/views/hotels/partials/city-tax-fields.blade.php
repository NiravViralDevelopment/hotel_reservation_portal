@php
  $cityTaxBasis = old('city_tax_basis', isset($hotel) ? ($hotel->city_tax_basis ?? '') : '');
  $cityTaxAmount = old('city_tax_amount', isset($hotel) ? $hotel->city_tax_amount : '');
  $cityTaxPercentage = old('city_tax_percentage', isset($hotel) ? $hotel->city_tax_percentage : '');
@endphp

<div class="card mb-4">
  <div class="card-header">
    <h2 class="hotel-section-title"><i class="bi bi-percent"></i> City tax</h2>
  </div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-6">
        <div class="form-label">Select one method</div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="city_tax_basis" id="city_tax_basis_total_rns" value="total_rns" @checked($cityTaxBasis === 'total_rns')>
          <label class="form-check-label" for="city_tax_basis_total_rns">Is City Tax on Total RNs</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="city_tax_basis" id="city_tax_basis_nett_rev" value="nett_rev" @checked($cityTaxBasis === 'nett_rev')>
          <label class="form-check-label" for="city_tax_basis_nett_rev">Is City Tax on Nett Rev EX VAT &amp; BF</label>
        </div>
        @error('city_tax_basis')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6" id="city_tax_amount_wrap" @unless ($cityTaxBasis === 'total_rns') hidden @endunless>
        <label for="city_tax_amount" class="form-label">Amount (£) <span class="text-danger">*</span></label>
        <input type="text" name="city_tax_amount" id="city_tax_amount" inputmode="decimal" class="form-control @error('city_tax_amount') is-invalid @enderror" value="{{ $cityTaxAmount }}" placeholder="0.00">
        <div class="form-text">City Tax = Total RNs × this amount.</div>
        @error('city_tax_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6" id="city_tax_percentage_wrap" @unless ($cityTaxBasis === 'nett_rev') hidden @endunless>
        <label for="city_tax_percentage" class="form-label">Percentage (%) <span class="text-danger">*</span></label>
        <input type="text" name="city_tax_percentage" id="city_tax_percentage" inputmode="decimal" class="form-control @error('city_tax_percentage') is-invalid @enderror" value="{{ $cityTaxPercentage }}" placeholder="0">
        <div class="form-text">City Tax = Nett Rev EX VAT &amp; BF × this percentage / 100.</div>
        @error('city_tax_percentage')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  (function () {
    var amountWrap = document.getElementById('city_tax_amount_wrap');
    var percentWrap = document.getElementById('city_tax_percentage_wrap');
    var amount = document.getElementById('city_tax_amount');
    var percent = document.getElementById('city_tax_percentage');
    var form = amount ? amount.closest('form') : null;
    if (!form) return;

    function selectedBasis() {
      var checked = form.querySelector('input[name="city_tax_basis"]:checked');
      return checked ? checked.value : '';
    }

    function sync() {
      var basis = selectedBasis();
      if (amountWrap) amountWrap.hidden = basis !== 'total_rns';
      if (percentWrap) percentWrap.hidden = basis !== 'nett_rev';
      if (amount) amount.disabled = basis !== 'total_rns';
      if (percent) percent.disabled = basis !== 'nett_rev';
    }

    form.querySelectorAll('input[name="city_tax_basis"]').forEach(function (input) {
      input.addEventListener('change', sync);
    });
    sync();
  })();
</script>
@endpush
