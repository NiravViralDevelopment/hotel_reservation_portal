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

  function segmentSeed() {
    var node = document.getElementById('stay-segment-seed');
    if (!node) return [];
    try {
      var parsed = JSON.parse(node.textContent || '[]');
      return Array.isArray(parsed) ? parsed : [];
    } catch (error) {
      return [];
    }
  }

  function nightsBetween(startIso, endIso) {
    var start = parseDate(startIso);
    var end = parseDate(endIso);
    if (!start || !end || end <= start) return 0;
    return Math.round((end.getTime() - start.getTime()) / 86400000);
  }

  function weekdayName(iso) {
    var date = parseDate(iso);
    return date ? weekdays[date.getDay()] : '';
  }

  function rangeRoomsBlank(range) {
    return ['single_rooms', 'single_rate', 'double_rooms', 'double_rate', 'triple_rooms', 'triple_rate'].every(function (key) {
      return range[key] == null || String(range[key]) === '';
    });
  }

  function blankRange(arrival, departure) {
    return {
      check_in: arrival || '',
      check_out: departure || '',
      single_rooms: '',
      single_rate: '',
      double_rooms: '',
      double_rate: '',
      triple_rooms: '',
      triple_rate: ''
    };
  }

  function rangesFromDaily(map, arrival, departure) {
    var start = parseDate(arrival);
    var end = parseDate(departure);
    if (!start || !end || end <= start) return [];
    var fields = ['single_rooms', 'single_rate', 'double_rooms', 'double_rate', 'triple_rooms', 'triple_rate'];
    var ranges = [];
    var current = null;
    var cursor = new Date(start.getTime());
    while (cursor < end && ranges.length < 50) {
      var iso = isoDate(cursor);
      var saved = map[iso] || {};
      var signature = fields.map(function (field) {
        return saved[field] == null ? '' : String(saved[field]);
      }).join('|');
      var next = new Date(cursor.getTime());
      next.setDate(next.getDate() + 1);
      var nextIso = isoDate(next);
      if (current && current.signature === signature && current.check_out === iso) {
        current.check_out = nextIso;
      } else {
        current = blankRange(iso, nextIso);
        current.signature = signature;
        fields.forEach(function (field) {
          current[field] = saved[field] == null ? '' : saved[field];
        });
        ranges.push(current);
      }
      cursor = next;
    }
    return ranges;
  }

  function readRanges() {
    var ranges = [];
    document.querySelectorAll('#stay-date-list tbody tr[data-stay-range]').forEach(function (row) {
      var item = blankRange('', '');
      row.querySelectorAll('[data-field]').forEach(function (input) {
        item[input.getAttribute('data-field')] = input.value;
      });
      ranges.push(item);
    });
    return ranges;
  }

  function rangeField(index, key, value, numeric, isDate) {
    var type = isDate ? 'date' : 'text';
    var mode = isDate ? '' : ' inputmode="' + (numeric ? 'numeric' : 'decimal') + '"';
    var classes = 'form-control form-control-sm' + (isDate ? '' : (numeric ? ' js-digits' : ' js-decimal'));
    var limits = '';
    if (isDate) {
      var stayArrival = document.getElementById('check_in');
      var stayDeparture = document.getElementById('check_out');
      if (stayArrival && stayArrival.value) limits += ' min="' + escAttr(stayArrival.value) + '"';
      if (stayDeparture && stayDeparture.value) limits += ' max="' + escAttr(stayDeparture.value) + '"';
    }
    return '<input type="' + type + '"' + mode + limits + ' class="' + classes + '" name="stay_segments[' + index + '][' + key + ']" data-field="' + key + '" value="' + escAttr(value) + '">';
  }

  function renderStayDates(nextRanges) {
    var list = document.getElementById('stay-date-list');
    var arrival = document.getElementById('check_in');
    var departure = document.getElementById('check_out');
    if (!list || !arrival || !departure) return;

    var ranges = nextRanges || readRanges();
    if (!nextRanges && ranges.length === 1 && rangeRoomsBlank(ranges[0]) && arrival.value && departure.value > arrival.value) {
      ranges[0].check_in = arrival.value;
      ranges[0].check_out = departure.value;
    }
    if (!ranges.length) {
      var seeded = segmentSeed();
      if (seeded.length) {
        ranges = seeded.map(function (row) { return Object.assign(blankRange('', ''), row); });
      } else if (arrival.value && departure.value && departure.value > arrival.value) {
        var grouped = rangesFromDaily(dailySeedMap(), arrival.value, departure.value);
        ranges = grouped.length ? grouped : [blankRange(arrival.value, departure.value)];
      }
    }

    if (!ranges.length) {
      list.innerHTML = '<p class="text-secondary small mb-0">Select the arrival date and departure date, then enter the rooms and rate for that stay. Add another row when a later part of the stay has different rooms or rates.</p>';
      recalcRevenue();
      return;
    }

    var body = ranges.map(function (range, index) {
      return '<tr data-stay-range="' + index + '">'
        + '<td>' + rangeField(index, 'check_in', range.check_in, false, true) + '</td>'
        + '<td>' + rangeField(index, 'check_out', range.check_out, false, true) + '</td>'
        + '<td class="text-nowrap" data-calc="day"></td>'
        + '<td data-calc="nights">0</td>'
        + '<td>' + rangeField(index, 'single_rooms', range.single_rooms, true, false) + '</td>'
        + '<td>' + rangeField(index, 'single_rate', range.single_rate, false, false) + '</td>'
        + '<td>' + rangeField(index, 'double_rooms', range.double_rooms, true, false) + '</td>'
        + '<td>' + rangeField(index, 'double_rate', range.double_rate, false, false) + '</td>'
        + '<td>' + rangeField(index, 'triple_rooms', range.triple_rooms, true, false) + '</td>'
        + '<td>' + rangeField(index, 'triple_rate', range.triple_rate, false, false) + '</td>'
        + '<td class="stay-calc" data-calc="room_nights">0</td>'
        + '<td class="stay-calc" data-calc="revenue">0.00</td>'
        + '<td><button type="button" class="btn btn-sm btn-outline-secondary" data-remove-range aria-label="Remove date range"><i class="bi bi-x-lg"></i></button></td>'
        + '</tr>';
    }).join('');

    list.innerHTML = '<div class="table-responsive"><table class="table table-sm table-hover stay-date-table mb-0">'
      + '<thead><tr>'
      + '<th>Arrival</th><th>Departure</th><th>Day</th><th>Nights</th>'
      + '<th>Single RNs</th><th>Single Rate</th>'
      + '<th>Double RNs</th><th>Double Rate</th>'
      + '<th>Triple RNs</th><th>Triple Rate</th>'
      + '<th class="stay-calc">Total Room Nights</th>'
      + '<th class="stay-calc">Total Rev</th>'
      + '<th></th>'
      + '</tr></thead><tbody>' + body + '</tbody>'
      + '<tfoot><tr>'
      + '<th scope="row" colspan="3">Total</th>'
      + '<td data-total="nights">0</td>'
      + '<td data-total="single_rooms">0</td><td data-total="single_rate">0.00</td>'
      + '<td data-total="double_rooms">0</td><td data-total="double_rate">0.00</td>'
      + '<td data-total="triple_rooms">0</td><td data-total="triple_rate">0.00</td>'
      + '<td class="stay-calc" data-total="room_nights">0</td>'
      + '<td class="stay-calc" data-total="revenue">0.00</td>'
      + '<td></td>'
      + '</tr></tfoot></table></div>';
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

  function rowAmount(row, field) {
    var input = row.querySelector('[data-field="' + field + '"]');
    var value = input ? parseFloat(input.value) : 0;
    return isNaN(value) ? 0 : value;
  }

  function setCalc(row, name, text) {
    var cell = row.querySelector('[data-calc="' + name + '"]');
    if (cell) cell.textContent = text;
  }

  function setStayTotal(field, value, asMoney) {
    var cell = document.querySelector('#stay-date-list tfoot [data-total="' + field + '"]');
    if (!cell) return;
    if (asMoney) {
      cell.textContent = value.toFixed(2);
    } else {
      cell.textContent = String(Math.round(value));
    }
  }

  function recalcRevenue() {
    var totalEl = document.getElementById('total_revenue');
    var displayEl = document.getElementById('total_revenue_display');
    if (!totalEl) return;
    var rows = document.querySelectorAll('#stay-date-list tbody tr[data-stay-range]');
    if (!rows.length) {
      totalEl.value = '';
      if (displayEl) displayEl.value = '';
      return;
    }
    var typeRooms = { single: 0, double: 0, triple: 0 };
    var typeRevenue = { single: 0, double: 0, triple: 0 };
    var nightTotal = 0;
    var roomNights = 0;
    var revenueTotal = 0;
    rows.forEach(function (row) {
      var nights = nightsBetween(rowAmountText(row, 'check_in'), rowAmountText(row, 'check_out'));
      var single = rowAmount(row, 'single_rooms');
      var singleRate = rowAmount(row, 'single_rate');
      var doubleRooms = rowAmount(row, 'double_rooms');
      var doubleRate = rowAmount(row, 'double_rate');
      var triple = rowAmount(row, 'triple_rooms');
      var tripleRate = rowAmount(row, 'triple_rate');
      var nightlyRooms = single + doubleRooms + triple;
      var nightlyRevenue = (single * singleRate) + (doubleRooms * doubleRate) + (triple * tripleRate);
      var rowRoomNights = nights * nightlyRooms;
      var rowRevenue = nights * nightlyRevenue;
      typeRooms.single += nights * single;
      typeRooms.double += nights * doubleRooms;
      typeRooms.triple += nights * triple;
      typeRevenue.single += nights * single * singleRate;
      typeRevenue.double += nights * doubleRooms * doubleRate;
      typeRevenue.triple += nights * triple * tripleRate;
      nightTotal += nights;
      roomNights += rowRoomNights;
      revenueTotal += rowRevenue;
      setCalc(row, 'day', weekdayName(rowAmountText(row, 'check_in')));
      setCalc(row, 'nights', nights > 0 ? String(nights) : '—');
      setCalc(row, 'room_nights', String(Math.round(rowRoomNights)));
      setCalc(row, 'revenue', rowRevenue.toFixed(2));
    });
    setStayTotal('nights', nightTotal, false);
    setStayTotal('single_rooms', typeRooms.single, false);
    setStayTotal('single_rate', typeRooms.single > 0 ? typeRevenue.single / typeRooms.single : 0, true);
    setStayTotal('double_rooms', typeRooms.double, false);
    setStayTotal('double_rate', typeRooms.double > 0 ? typeRevenue.double / typeRooms.double : 0, true);
    setStayTotal('triple_rooms', typeRooms.triple, false);
    setStayTotal('triple_rate', typeRooms.triple > 0 ? typeRevenue.triple / typeRooms.triple : 0, true);
    setStayTotal('room_nights', roomNights, false);
    setStayTotal('revenue', revenueTotal, true);
    totalEl.value = revenueTotal.toFixed(2);
    if (displayEl) displayEl.value = formatPounds(revenueTotal);
  }

  function rowAmountText(row, field) {
    var input = row.querySelector('[data-field="' + field + '"]');
    return input ? input.value : '';
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
    stayList.addEventListener('change', recalcRevenue);
    stayList.addEventListener('click', function (event) {
      var button = event.target.closest('[data-remove-range]');
      if (!button) return;
      var ranges = readRanges();
      var row = button.closest('tr');
      var index = row ? Array.prototype.indexOf.call(row.parentNode.children, row) : -1;
      if (index >= 0) ranges.splice(index, 1);
      renderStayDates(ranges);
    });
  }
  var addRangeBtn = document.getElementById('add-stay-range');
  if (addRangeBtn) {
    addRangeBtn.addEventListener('click', function () {
      var ranges = readRanges();
      var arrival = document.getElementById('check_in');
      var departure = document.getElementById('check_out');
      if (!ranges.length && arrival && departure && arrival.value && departure.value) {
        ranges.push(blankRange(arrival.value, departure.value));
      } else {
        var previous = ranges[ranges.length - 1] || blankRange('', '');
        ranges.push(blankRange(previous.check_out || (arrival ? arrival.value : ''), departure ? departure.value : ''));
      }
      renderStayDates(ranges);
    });
  }

  updateDay();
  updateNights();
  updateCxlDueDate();
  applyRoomChain();
  renderStayDates();
})();
</script>
