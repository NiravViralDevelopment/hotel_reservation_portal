<script src="{{ asset('assets/js/enquiry-validation.js') }}?v={{ @filemtime(public_path('assets/js/enquiry-validation.js')) }}"></script>
<script>
(function () {
  var weekdays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

  document.querySelectorAll('.js-digits').forEach(function (el) {
    el.addEventListener('input', function () {
      el.value = el.value.replace(/\D/g, '');
    });
  });

  document.querySelectorAll('.js-decimal').forEach(function (el) {
    el.addEventListener('input', function () {
      var cleaned = el.value.replace(/[^\d.]/g, '');
      var parts = cleaned.split('.');
      el.value = parts.length > 1 ? parts[0] + '.' + parts.slice(1).join('') : parts[0];
    });
  });

  function num(id) {
    var el = document.getElementById(id);
    if (!el || el.value === '') return 0;
    var value = parseFloat(el.value);
    return isNaN(value) ? 0 : value;
  }

  function parseDate(value) {
    if (!value) return null;
    var date = new Date(value + 'T00:00:00');
    return isNaN(date.getTime()) ? null : date;
  }

  function isoDate(date) {
    var month = String(date.getMonth() + 1).padStart(2, '0');
    var day = String(date.getDate()).padStart(2, '0');
    return date.getFullYear() + '-' + month + '-' + day;
  }

  function updateDay() {
    var arrival = document.getElementById('check_in');
    var dayEl = document.getElementById('day');
    if (!arrival || !dayEl) return;
    var date = parseDate(arrival.value);
    dayEl.value = date ? weekdays[date.getDay()] : '';
  }

  function updateNights() {
    var arrival = document.getElementById('check_in');
    var departure = document.getElementById('check_out');
    var nightsEl = document.getElementById('nights');
    if (!arrival || !departure || !nightsEl) return;

    var start = parseDate(arrival.value);
    if (start) {
      var minDeparture = new Date(start.getTime());
      minDeparture.setDate(minDeparture.getDate() + 1);
      departure.min = isoDate(minDeparture);
    } else {
      departure.removeAttribute('min');
    }

    var end = parseDate(departure.value);
    if (!start || !end || end <= start) {
      nightsEl.value = '';
    } else {
      nightsEl.value = String(Math.round((end.getTime() - start.getTime()) / 86400000));
    }
    nightsEl.dispatchEvent(new Event('input', { bubbles: true }));
    nightsEl.dispatchEvent(new Event('change', { bubbles: true }));
    recalcRevenue();
  }

  var applyingRoomChain = false;

  function addDays(iso, days) {
    var date = parseDate(iso);
    if (!date) return '';
    date.setDate(date.getDate() + days);
    return isoDate(date);
  }

  function roomDate(type, edge) {
    return document.getElementById(type + '_' + edge + '_date');
  }

  function setPickerBounds(el, min, max) {
    if (!el) return;
    if (min) el.min = min;
    else el.removeAttribute('min');
    if (max) el.max = max;
    else el.removeAttribute('max');
  }

  function clearPeriod(fromEl, toEl) {
    fromEl.value = '';
    toEl.value = '';
    setPickerBounds(fromEl, '', '');
    setPickerBounds(toEl, '', '');
  }

  function placePeriod(fromEl, toEl, min, max, fillFrom) {
    if (!fromEl || !toEl) return false;
    if (!min || !max || min > max) {
      clearPeriod(fromEl, toEl);
      return false;
    }
    if (fromEl.value && (fromEl.value < min || fromEl.value > max)) fromEl.value = '';
    if (toEl.value && (toEl.value < min || toEl.value > max)) toEl.value = '';
    if (!fromEl.value && fillFrom) fromEl.value = min;
    if (fromEl.value && toEl.value && toEl.value < fromEl.value) toEl.value = '';
    var fromMax = toEl.value && toEl.value < max ? toEl.value : max;
    var toMin = fromEl.value && fromEl.value > min ? fromEl.value : min;
    setPickerBounds(fromEl, min, fromMax);
    setPickerBounds(toEl, toMin, max);
    return true;
  }

  function touchDate(el) {
    if (!el) return;
    el.dispatchEvent(new Event('input', { bubbles: true }));
    el.dispatchEvent(new Event('change', { bubbles: true }));
  }

  function applyRoomChain() {
    if (applyingRoomChain) return;
    applyingRoomChain = true;

    var arrivalEl = document.getElementById('check_in');
    var departureEl = document.getElementById('check_out');
    var arrival = arrivalEl && arrivalEl.value ? arrivalEl.value : '';
    var departure = departureEl && departureEl.value ? departureEl.value : '';
    var singleFrom = roomDate('single', 'from');
    var singleTo = roomDate('single', 'to');
    var doubleFrom = roomDate('double', 'from');
    var doubleTo = roomDate('double', 'to');
    var tripleFrom = roomDate('triple', 'from');
    var tripleTo = roomDate('triple', 'to');
    if (!singleFrom || !singleTo || !doubleFrom || !doubleTo || !tripleFrom || !tripleTo || !arrival || !departure) {
      applyingRoomChain = false;
      return;
    }

    if (arrival && !singleFrom.value) singleFrom.value = arrival;
    if (departure && !singleTo.value) singleTo.value = departure;
    placePeriod(singleFrom, singleTo, arrival, departure, false);

    var dayAfterSingle = singleTo.value ? addDays(singleTo.value, 1) : '';
    placePeriod(doubleFrom, doubleTo, dayAfterSingle || arrival, departure, !!dayAfterSingle);

    var dayAfterDouble = doubleTo.value ? addDays(doubleTo.value, 1) : '';
    if (!doubleTo.value) {
      clearPeriod(tripleFrom, tripleTo);
    } else {
      placePeriod(tripleFrom, tripleTo, dayAfterDouble, departure, true);
    }

    [singleFrom, singleTo, doubleFrom, doubleTo, tripleFrom, tripleTo].forEach(touchDate);
    applyingRoomChain = false;
  }

  function updateCxlDueDate() {
    var policy = document.getElementById('cxl_policy');
    var arrival = document.getElementById('check_in');
    var due = document.getElementById('cxl_due_date');
    if (!policy || !arrival || !due) return;

    var match = String(policy.value).match(/\d+/);
    var start = parseDate(arrival.value);
    if (!match || !start) {
      due.value = '';
    } else {
      var date = new Date(start.getTime());
      date.setDate(date.getDate() - parseInt(match[0], 10));
      due.value = isoDate(date);
    }
    due.dispatchEvent(new Event('input', { bubbles: true }));
    due.dispatchEvent(new Event('change', { bubbles: true }));
  }

  function formatPounds(amount) {
    var parts = amount.toFixed(2).split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    return '£' + parts.join('.');
  }

  function recalcRevenue() {
    var nightsEl = document.getElementById('nights');
    var totalEl = document.getElementById('total_revenue');
    var displayEl = document.getElementById('total_revenue_display');
    if (!totalEl) return;
    if (!nightsEl || nightsEl.value === '') {
      totalEl.value = '';
      if (displayEl) displayEl.value = '';
      return;
    }
    var nightly = (num('single_rooms') * num('single_rate'))
      + (num('double_rooms') * num('double_rate'))
      + (num('triple_rooms') * num('triple_rate'));
    var total = nightly * num('nights');
    totalEl.value = total.toFixed(2);
    if (displayEl) displayEl.value = formatPounds(total);
  }

  function localToday() {
    var now = new Date();
    var month = String(now.getMonth() + 1).padStart(2, '0');
    var day = String(now.getDate()).padStart(2, '0');
    return now.getFullYear() + '-' + month + '-' + day;
  }

  var arrivalLimit = document.getElementById('check_in');
  if (arrivalLimit) {
    arrivalLimit.min = localToday();
  }

  var arrivalEl = document.getElementById('check_in');
  if (arrivalEl) {
    arrivalEl.addEventListener('change', function () {
      updateDay();
      updateNights();
      updateCxlDueDate();
      applyRoomChain();
    });
    arrivalEl.addEventListener('input', function () {
      updateDay();
      updateNights();
      updateCxlDueDate();
      applyRoomChain();
    });
  }

  var cxlPolicyEl = document.getElementById('cxl_policy');
  if (cxlPolicyEl) {
    cxlPolicyEl.addEventListener('input', updateCxlDueDate);
    cxlPolicyEl.addEventListener('change', updateCxlDueDate);
  }

  document.querySelectorAll('.room-period-date').forEach(function (el) {
    el.addEventListener('change', applyRoomChain);
    el.addEventListener('input', applyRoomChain);
  });

  var departureEl = document.getElementById('check_out');
  if (departureEl) {
    departureEl.addEventListener('change', function () {
      updateNights();
      applyRoomChain();
    });
    departureEl.addEventListener('input', function () {
      updateNights();
      applyRoomChain();
    });
  }

  document.querySelectorAll('.enquiry-calc').forEach(function (el) {
    el.addEventListener('input', recalcRevenue);
    el.addEventListener('change', recalcRevenue);
  });

  updateDay();
  updateNights();
  updateCxlDueDate();
  applyRoomChain();
})();
</script>
