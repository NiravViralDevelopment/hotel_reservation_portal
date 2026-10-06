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
    });
    arrivalEl.addEventListener('input', function () {
      updateDay();
      updateNights();
      updateCxlDueDate();
    });
  }

  var cxlPolicyEl = document.getElementById('cxl_policy');
  if (cxlPolicyEl) {
    cxlPolicyEl.addEventListener('input', updateCxlDueDate);
    cxlPolicyEl.addEventListener('change', updateCxlDueDate);
  }

  var departureEl = document.getElementById('check_out');
  if (departureEl) {
    departureEl.addEventListener('change', updateNights);
    departureEl.addEventListener('input', updateNights);
  }

  document.querySelectorAll('.enquiry-calc').forEach(function (el) {
    el.addEventListener('input', recalcRevenue);
    el.addEventListener('change', recalcRevenue);
  });

  updateDay();
  updateNights();
  updateCxlDueDate();
})();
</script>
