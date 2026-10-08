<script src="<?php echo e(asset('assets/js/enquiry-validation.js')); ?>?v=<?php echo e(@filemtime(public_path('assets/js/enquiry-validation.js'))); ?>"></script>
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

  function applyRoomChain() {
    renderStayDates();
  }

  function formatDmY(iso) {
    var parts = iso.split('-');
    if (parts.length !== 3) return iso;
    return parts[2] + '-' + parts[1] + '-' + parts[0];
  }

  function escAttr(value) {
    return String(value == null ? '' : value)
      .replace(/&/g, '&amp;')
      .replace(/"/g, '&quot;')
      .replace(/</g, '&lt;');
  }

  function dailySeedMap() {
    var node = document.getElementById('daily-room-seed');
    if (!node) return {};
    try {
      var parsed = JSON.parse(node.textContent || '{}');
      return parsed && typeof parsed === 'object' ? parsed : {};
    } catch (error) {
      return {};
    }
  }

  var dailyCache = null;

  function collectDailyValues() {
    if (!dailyCache) dailyCache = dailySeedMap();
    document.querySelectorAll('#stay-date-list [data-stay-date]').forEach(function (row) {
      var date = row.getAttribute('data-stay-date');
      var current = Object.assign({}, dailyCache[date] || {});
      row.querySelectorAll('[data-field]').forEach(function (input) {
        current[input.getAttribute('data-field')] = input.value;
      });
      dailyCache[date] = current;
    });
    return dailyCache;
  }

  function stayInput(iso, field, value, numeric) {
    return '<input type="text" inputmode="' + (numeric ? 'numeric' : 'decimal') + '" class="form-control form-control-sm ' + (numeric ? 'js-digits' : 'js-decimal') + ' stay-room-input" name="daily_rooms[' + iso + '][' + field + ']" data-field="' + field + '" value="' + escAttr(value) + '">';
  }

  function renderStayDates() {
    var list = document.getElementById('stay-date-list');
    var arrival = document.getElementById('check_in');
    var departure = document.getElementById('check_out');
    if (!list || !arrival || !departure) return;
    if (!arrival.value || !departure.value || departure.value < arrival.value) {
      list.innerHTML = '<p class="text-secondary small mb-0">Select the arrival date and departure date to enter rooms and rates for each date.</p>';
      recalcRevenue();
      return;
    }

    var start = parseDate(arrival.value);
    var end = parseDate(departure.value);
    if (!start || !end) return;

    var saved = collectDailyValues();
    var rows = '';
    var cursor = new Date(start.getTime());
    var count = 0;
    var fields = ['single_rooms', 'single_rate', 'double_rooms', 'double_rate', 'triple_rooms', 'triple_rate'];
    while (cursor < end && count < 400) {
      var iso = isoDate(cursor);
      var row = saved[iso] || {};
      rows += '<tr data-stay-date="' + iso + '"><td class="fw-semibold text-nowrap">' + formatDmY(iso) + '</td>';
      fields.forEach(function (field) {
        rows += '<td>' + stayInput(iso, field, row[field] == null ? '' : row[field], field.indexOf('rooms') !== -1) + '</td>';
      });
      rows += '</tr>';
      cursor.setDate(cursor.getDate() + 1);
      count += 1;
    }

    var note = count >= 400
      ? '<p class="text-secondary small mb-2">Showing the first 400 dates.</p>'
      : '';
    list.innerHTML = note
      + '<div class="table-responsive"><table class="table table-sm table-hover stay-date-table mb-0">'
      + '<thead><tr>'
      + '<th rowspan="2">Date</th>'
      + '<th colspan="2">Single Room &amp; Rate</th>'
      + '<th colspan="2">Double Room &amp; Rate</th>'
      + '<th colspan="2">Triple Room &amp; Rate</th>'
      + '</tr><tr>'
      + '<th>Rooms</th><th>Rate</th>'
      + '<th>Rooms</th><th>Rate</th>'
      + '<th>Rooms</th><th>Rate</th>'
      + '</tr></thead><tbody>' + rows + '</tbody></table></div>';
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

  function sumStayField(rows, field) {
    var total = 0;
    rows.forEach(function (row) {
      var input = row.querySelector('[data-field="' + field + '"]');
      var value = input ? parseFloat(input.value) : 0;
      if (isNaN(value)) value = 0;
      total += value;
    });
    return total;
  }

  function recalcRevenue() {
    var totalEl = document.getElementById('total_revenue');
    var displayEl = document.getElementById('total_revenue_display');
    if (!totalEl) return;
    var rows = document.querySelectorAll('#stay-date-list tbody tr[data-stay-date]');
    if (!rows.length) {
      totalEl.value = '';
      if (displayEl) displayEl.value = '';
      return;
    }
    var nightsEl = document.getElementById('nights');
    var nights = nightsEl ? parseFloat(nightsEl.value) : 0;
    if (isNaN(nights) || nights < 1) nights = 1;
    var singleRooms = sumStayField(rows, 'single_rooms');
    var doubleRooms = sumStayField(rows, 'double_rooms');
    var tripleRooms = sumStayField(rows, 'triple_rooms');
    var total = (
      (singleRooms * sumStayField(rows, 'single_rate'))
      + (doubleRooms * sumStayField(rows, 'double_rate'))
      + (tripleRooms * sumStayField(rows, 'triple_rate'))
    ) * nights;
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

  var stayList = document.getElementById('stay-date-list');
  if (stayList) {
    stayList.addEventListener('input', function (event) {
      var input = event.target;
      if (!input || !input.getAttribute) return;
      if (input.classList.contains('js-digits')) {
        input.value = input.value.replace(/\D/g, '');
      }
      if (input.classList.contains('js-decimal')) {
        var cleaned = input.value.replace(/[^\d.]/g, '');
        var parts = cleaned.split('.');
        input.value = parts.length > 1 ? parts[0] + '.' + parts.slice(1).join('') : parts[0];
      }
      recalcRevenue();
    });
  }

  updateDay();
  updateNights();
  updateCxlDueDate();
  applyRoomChain();
  renderStayDates();
})();
</script>
<?php /**PATH E:\Working\hotel_reservation_portal\resources\views/enquiries/partials/entry-form-scripts.blade.php ENDPATH**/ ?>