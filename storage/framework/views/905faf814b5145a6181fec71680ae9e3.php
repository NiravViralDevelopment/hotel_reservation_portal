<script>
(function () {
  var form = document.getElementById('group-booking-form');
  if (!form) return;

  var weekdays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

  function field(id) {
    return document.getElementById(id);
  }

  function num(id) {
    var el = field(id);
    if (!el || el.value === '') return 0;
    var value = parseFloat(el.value);
    return isNaN(value) ? 0 : value;
  }

  function formatPounds(amount) {
    var parts = amount.toFixed(2).split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    return '£' + parts.join('.');
  }

  function updateDay() {
    var arrival = field('gb_check_in');
    var dayEl = field('gb_day');
    if (!arrival || !dayEl) return;
    if (!arrival.value) {
      dayEl.value = '';
      return;
    }
    var date = new Date(arrival.value + 'T00:00:00');
    dayEl.value = isNaN(date.getTime()) ? '' : weekdays[date.getDay()];
  }

  function money(amount) {
    return (Math.round((amount + Number.EPSILON) * 100) / 100).toFixed(2);
  }

  function setMoney(inputId, displayId, amount) {
    var input = field(inputId);
    var display = field(displayId);
    var rounded = money(amount);
    if (input) input.value = rounded;
    if (display) display.value = formatPounds(parseFloat(rounded));
  }

  function parseAmount(value) {
    var amount = parseFloat(value);
    return isNaN(amount) ? 0 : amount;
  }

  function storedDailyRows() {
    var node = document.getElementById('gb-daily-room-seed');
    if (!node) return [];
    try {
      var parsed = JSON.parse(node.textContent || '[]');
      return Array.isArray(parsed) ? parsed : [];
    } catch (error) {
      return [];
    }
  }

  function liveDailyRows() {
    var ranges = document.querySelectorAll('#stay-date-list tbody tr[data-stay-range]');
    if (ranges.length) {
      var expanded = [];
      ranges.forEach(function (row) {
        var item = {};
        row.querySelectorAll('[data-field]').forEach(function (input) {
          item[input.getAttribute('data-field')] = input.value;
        });
        if (!item.check_in || !item.check_out) return;
        var cursor = new Date(item.check_in + 'T00:00:00');
        var end = new Date(item.check_out + 'T00:00:00');
        if (isNaN(cursor.getTime()) || isNaN(end.getTime()) || end <= cursor) return;
        while (cursor < end && expanded.length < 400) {
          var month = String(cursor.getMonth() + 1).padStart(2, '0');
          var day = String(cursor.getDate()).padStart(2, '0');
          expanded.push({
            date: cursor.getFullYear() + '-' + month + '-' + day,
            single_rooms: item.single_rooms,
            single_rate: item.single_rate,
            double_rooms: item.double_rooms,
            double_rate: item.double_rate,
            triple_rooms: item.triple_rooms,
            triple_rate: item.triple_rate
          });
          cursor.setDate(cursor.getDate() + 1);
        }
      });
      if (expanded.length) return expanded;
    }
    var rows = [];
    document.querySelectorAll('#stay-date-list tbody tr[data-stay-date]').forEach(function (row) {
      var item = { date: row.getAttribute('data-stay-date') };
      ['single_rooms', 'single_rate', 'double_rooms', 'double_rate', 'triple_rooms', 'triple_rate'].forEach(function (key) {
        var input = row.querySelector('[data-field="' + key + '"]');
        item[key] = input ? input.value : '';
      });
      rows.push(item);
    });
    return rows;
  }

  function dailyRows() {
    var live = liveDailyRows();
    return live.length ? live : storedDailyRows();
  }

  function syncDailyFields(rows) {
    var holder = document.getElementById('gb-daily-room-fields');
    if (!holder) return;
    holder.innerHTML = rows.map(function (row) {
      var date = String(row.date || '');
      if (!/^\d{4}-\d{2}-\d{2}$/.test(date)) return '';
      return ['single_rooms', 'single_rate', 'double_rooms', 'double_rate', 'triple_rooms', 'triple_rate'].map(function (key) {
        return '<input type="hidden" name="daily_rooms[' + date + '][' + key + ']" value="' + String(row[key] == null ? '' : row[key]).replace(/"/g, '') + '">';
      }).join('');
    }).join('');
  }

  function setHint(id, text) {
    var hint = field(id);
    if (hint) hint.textContent = text;
  }

  function formatDmY(iso) {
    var parts = String(iso || '').split('-');
    if (parts.length !== 3) return String(iso || '');
    return parts[2] + '-' + parts[1] + '-' + parts[0];
  }

  function dateFigures(row) {
    var single = parseAmount(row.single_rooms);
    var singleRate = parseAmount(row.single_rate);
    var doubleRooms = parseAmount(row.double_rooms);
    var doubleRate = parseAmount(row.double_rate);
    var triple = parseAmount(row.triple_rooms);
    var tripleRate = parseAmount(row.triple_rate);
    var rooms = single + doubleRooms + triple;
    var revenue = (single * singleRate) + (doubleRooms * doubleRate) + (triple * tripleRate);
    return {
      date: row.date,
      single: single,
      singleRate: singleRate,
      singleRevenue: single * singleRate,
      doubleRooms: doubleRooms,
      doubleRate: doubleRate,
      doubleRevenue: doubleRooms * doubleRate,
      triple: triple,
      tripleRate: tripleRate,
      tripleRevenue: triple * tripleRate,
      rooms: rooms,
      revenue: revenue,
      average: rooms > 0 ? revenue / rooms : 0,
      covers: (single * 1) + (doubleRooms * 2) + (triple * 3)
    };
  }

  function summariseDates(rows) {
    var summary = {
      days: [],
      rooms: 0,
      revenue: 0,
      singleRooms: 0,
      singleRevenue: 0,
      doubleRooms: 0,
      doubleRevenue: 0,
      tripleRooms: 0,
      tripleRevenue: 0
    };
    rows.forEach(function (row) {
      var day = dateFigures(row);
      summary.days.push(day);
      summary.rooms += day.rooms;
      summary.revenue += day.revenue;
      summary.singleRooms += day.single;
      summary.singleRevenue += day.singleRevenue;
      summary.doubleRooms += day.doubleRooms;
      summary.doubleRevenue += day.doubleRevenue;
      summary.tripleRooms += day.triple;
      summary.tripleRevenue += day.tripleRevenue;
    });
    return summary;
  }

  function weightedRate(rooms, revenue) {
    return rooms > 0 ? revenue / rooms : 0;
  }

  function applyDailyRoomTotals(summary) {
    [
      ['single', summary.singleRooms, summary.singleRevenue],
      ['double', summary.doubleRooms, summary.doubleRevenue],
      ['triple', summary.tripleRooms, summary.tripleRevenue]
    ].forEach(function (item) {
      var roomsEl = field('gb_' + item[0] + '_rooms');
      var rateEl = field('gb_' + item[0] + '_rate');
      if (roomsEl) {
        roomsEl.value = String(Math.round(item[1]));
        roomsEl.readOnly = true;
      }
      if (rateEl) {
        rateEl.value = money(weightedRate(item[1], item[2]));
        rateEl.readOnly = true;
      }
    });
    setHint('gb_rooms_hint', 'Each date is totalled on its own. Room counts are the sum of those dates, and each rate is that room type’s revenue divided by its rooms.');
    setHint('gb_total_rns_hint', 'Sum of each date’s single, double, and triple rooms.');
    setHint('gb_total_revenue_hint', 'Sum of each date’s (Single × rate) + (Double × rate) + (Triple × rate).');
    setHint('gb_bb_revenue_hint', 'Sum of each date’s covers × breakfast rate. Covers = (Single × 1) + (Double × 2) + (Triple × 3).');
    setHint('gb_dinner_revenue_hint', 'Sum of each date’s covers × dinner rate. Covers = (Single × 1) + (Double × 2) + (Triple × 3).');
  }

  function unlockRoomFields() {
    ['single', 'double', 'triple'].forEach(function (type) {
      var roomsEl = field('gb_' + type + '_rooms');
      var rateEl = field('gb_' + type + '_rate');
      if (roomsEl) roomsEl.readOnly = false;
      if (rateEl) rateEl.readOnly = false;
    });
  }

  function resetAggregateHints() {
    setHint('gb_rooms_hint', 'Room counts, rates, and nights update Total RNs, Total Rev, BB Revenue, and Nett Rev.');
    setHint('gb_total_rns_hint', '(Single + Double + Triple) × nights');
    setHint('gb_total_revenue_hint', '((Single × rate) + (Double × rate) + (Triple × rate)) × nights');
    setHint('gb_bb_revenue_hint', '((Nights × Single RNs × 1) + (Nights × Double RNs × 2) + (Nights × Triple RNs × 3)) × breakfast rate');
    setHint('gb_dinner_revenue_hint', '((Nights × Single RNs × 1) + (Nights × Double RNs × 2) + (Nights × Triple RNs × 3)) × dinner rate');
  }

  function renderDailyBreakdown(summary) {
    var holder = document.getElementById('gb-daily-breakdown');
    if (!holder) return;
    if (!summary || !summary.days.length) {
      holder.innerHTML = '';
      return;
    }
    var basis = selectedBasis();
    var showBreakfast = basis === 'BB' || basis === 'DBB';
    var showDinner = basis === 'DBB';
    var breakfastRate = showBreakfast ? num('gb_breakfast_rate') : 0;
    var dinnerRate = showDinner ? num('gb_dinner_rate') : 0;
    var bbTotal = 0;
    var dinnerTotal = 0;
    var body = summary.days.map(function (day) {
      var bb = day.covers * breakfastRate;
      var dinner = day.covers * dinnerRate;
      bbTotal += bb;
      dinnerTotal += dinner;
      return '<tr>'
        + '<td class="fw-semibold text-nowrap">' + formatDmY(day.date) + '</td>'
        + '<td>' + Math.round(day.single) + '</td><td>' + money(day.singleRate) + '</td>'
        + '<td>' + Math.round(day.doubleRooms) + '</td><td>' + money(day.doubleRate) + '</td>'
        + '<td>' + Math.round(day.triple) + '</td><td>' + money(day.tripleRate) + '</td>'
        + '<td class="stay-calc">' + Math.round(day.rooms) + '</td>'
        + '<td class="stay-calc">' + money(day.average) + '</td>'
        + '<td class="stay-calc">' + money(day.revenue) + '</td>'
        + (showBreakfast ? '<td class="stay-calc">' + money(bb) + '</td>' : '')
        + (showDinner ? '<td class="stay-calc">' + money(dinner) + '</td>' : '')
        + '</tr>';
    }).join('');
    holder.innerHTML = '<h3 class="stay-date-title">Date totals</h3>'
      + '<p class="stay-date-hint">Rooms, rates, revenue, and meal covers are worked out for each date. The total row is the sum of those dates.</p>'
      + '<div class="table-responsive"><table class="table table-sm table-hover stay-date-table mb-0">'
      + '<thead><tr>'
      + '<th rowspan="2">Date</th>'
      + '<th colspan="2">Single</th><th colspan="2">Double</th><th colspan="2">Triple</th>'
      + '<th rowspan="2" class="stay-calc">Rooms</th>'
      + '<th rowspan="2" class="stay-calc">Avg rate</th>'
      + '<th rowspan="2" class="stay-calc">Revenue</th>'
      + (showBreakfast ? '<th rowspan="2" class="stay-calc">BB</th>' : '')
      + (showDinner ? '<th rowspan="2" class="stay-calc">Dinner</th>' : '')
      + '</tr><tr><th>Rooms</th><th>Rate</th><th>Rooms</th><th>Rate</th><th>Rooms</th><th>Rate</th></tr></thead>'
      + '<tbody>' + body + '</tbody><tfoot><tr>'
      + '<th scope="row">Total</th>'
      + '<td>' + Math.round(summary.singleRooms) + '</td><td>' + money(weightedRate(summary.singleRooms, summary.singleRevenue)) + '</td>'
      + '<td>' + Math.round(summary.doubleRooms) + '</td><td>' + money(weightedRate(summary.doubleRooms, summary.doubleRevenue)) + '</td>'
      + '<td>' + Math.round(summary.tripleRooms) + '</td><td>' + money(weightedRate(summary.tripleRooms, summary.tripleRevenue)) + '</td>'
      + '<td class="stay-calc">' + Math.round(summary.rooms) + '</td>'
      + '<td class="stay-calc">' + money(weightedRate(summary.rooms, summary.revenue)) + '</td>'
      + '<td class="stay-calc">' + money(summary.revenue) + '</td>'
      + (showBreakfast ? '<td class="stay-calc">' + money(bbTotal) + '</td>' : '')
      + (showDinner ? '<td class="stay-calc">' + money(dinnerTotal) + '</td>' : '')
      + '</tr></tfoot></table></div>';
  }

  function selectedBasis() {
    var el = field('gb_basis');
    return el ? el.value : '';
  }

  function updateMealInputs() {
    var basis = selectedBasis();
    var showBreakfast = basis === 'BB' || basis === 'DBB';
    var showDinner = basis === 'DBB';
    var breakfastWrap = field('gb_breakfast_rate_wrap');
    var dinnerRateWrap = field('gb_dinner_rate_wrap');
    var dinnerRevenueWrap = field('gb_dinner_revenue_wrap');
    if (breakfastWrap) breakfastWrap.hidden = !showBreakfast;
    if (dinnerRateWrap) dinnerRateWrap.hidden = !showDinner;
    if (dinnerRevenueWrap) dinnerRevenueWrap.hidden = !showDinner;
    if (!showBreakfast) {
      var breakfastRate = field('gb_breakfast_rate');
      if (breakfastRate) breakfastRate.value = '';
    }
    if (!showDinner) {
      var dinnerRate = field('gb_dinner_rate');
      if (dinnerRate) dinnerRate.value = '';
    }
  }

  function mealCoverNights() {
    var nights = num('gb_nights');
    return (nights * num('gb_single_rooms') * 1)
      + (nights * num('gb_double_rooms') * 2)
      + (nights * num('gb_triple_rooms') * 3);
  }

  function writeRevenue(totalRns, totalRev, meals) {
    updateMealInputs();
    var basis = selectedBasis();
    var breakfastRate = (basis === 'BB' || basis === 'DBB') ? num('gb_breakfast_rate') : 0;
    var dinnerRate = basis === 'DBB' ? num('gb_dinner_rate') : 0;
    var bbRevenue = meals ? meals.bb : mealCoverNights() * breakfastRate;
    var dinnerRevenue = meals ? meals.dinner : mealCoverNights() * dinnerRate;
    var dinnerRateEl = field('gb_dinner_rate');
    var hasDinnerRate = basis === 'DBB' && dinnerRateEl && dinnerRateEl.value !== '';
    var nettRev = ((totalRev * 100) / 120) - bbRevenue - (hasDinnerRate ? dinnerRevenue : 0);
    setHint('gb_nett_rev_ex_vat_hint', hasDinnerRate
      ? '(Total Rev × 100 / 120) − BB Revenue − Dinner Revenue'
      : '(Total Rev × 100 / 120) − BB Revenue');
    var rnsDisplay = field('gb_total_rns_display');
    var rnsInput = field('gb_total_rns');
    if (rnsDisplay) rnsDisplay.value = String(totalRns);
    if (rnsInput) rnsInput.value = String(totalRns);
    setMoney('gb_total_revenue', 'gb_total_revenue_display', totalRev);
    setMoney('gb_bb_revenue', 'gb_bb_revenue_display', bbRevenue);
    setMoney('gb_dinner_revenue', 'gb_dinner_revenue_display', dinnerRevenue);
    setMoney('gb_nett_rev_ex_vat', 'gb_nett_rev_ex_vat_display', nettRev);
    writeCityTax(totalRns, nettRev);
  }

  function cityTaxConfig() {
    var node = document.getElementById('gb-city-tax-config');
    if (!node) return { basis: '', amount: 0, percentage: 0 };
    try {
      var parsed = JSON.parse(node.textContent || '{}');
      return parsed && typeof parsed === 'object' ? parsed : { basis: '', amount: 0, percentage: 0 };
    } catch (error) {
      return { basis: '', amount: 0, percentage: 0 };
    }
  }

  function writeCityTax(totalRns, nettRev) {
    var config = cityTaxConfig();
    var hint = field('gb_city_tax_hint');
    var display = field('gb_city_tax_display');
    var input = field('gb_city_tax');
    var basis = config.basis || '';
    var cityTax = null;
    if (basis === 'total_rns') {
      var amount = parseAmount(config.amount);
      cityTax = totalRns * amount;
      if (hint) hint.textContent = 'Total RNs × £' + money(amount);
    } else if (basis === 'nett_rev') {
      var percentage = parseAmount(config.percentage);
      cityTax = nettRev * percentage / 100;
      if (hint) hint.textContent = 'Nett Rev EX VAT & BF × ' + money(percentage) + '%';
    } else if (hint) {
      hint.textContent = '';
    }
    if (cityTax === null) {
      if (display) display.value = '';
      if (input) input.value = '';
      return;
    }
    setMoney('gb_city_tax', 'gb_city_tax_display', cityTax);
  }

  function mealTotals(summary) {
    var basis = selectedBasis();
    var breakfastRate = (basis === 'BB' || basis === 'DBB') ? num('gb_breakfast_rate') : 0;
    var dinnerRate = basis === 'DBB' ? num('gb_dinner_rate') : 0;
    var bb = 0;
    var dinner = 0;
    summary.days.forEach(function (day) {
      bb += day.covers * breakfastRate;
      dinner += day.covers * dinnerRate;
    });
    return { bb: bb, dinner: dinner };
  }

  function segmentSeed() {
    var node = document.getElementById('gb-stay-segment-seed');
    if (!node) return [];
    try {
      var parsed = JSON.parse(node.textContent || '[]');
      return Array.isArray(parsed) ? parsed : [];
    } catch (error) {
      return [];
    }
  }

  var loadedWithSegments = segmentSeed().length > 0;
  var segmentSnapshot = null;

  function segmentNights(row) {
    if (!row.check_in || !row.check_out) return 0;
    var start = new Date(row.check_in + 'T00:00:00');
    var end = new Date(row.check_out + 'T00:00:00');
    if (isNaN(start.getTime()) || isNaN(end.getTime()) || end <= start) return 0;
    return Math.round((end.getTime() - start.getTime()) / 86400000);
  }

  function segmentDay(iso) {
    if (!iso) return '';
    var date = new Date(iso + 'T00:00:00');
    return isNaN(date.getTime()) ? '' : weekdays[date.getDay()];
  }

  function readSegmentRows() {
    var rows = [];
    document.querySelectorAll('#gb-segment-body tr').forEach(function (tr) {
      var item = {};
      tr.querySelectorAll('[data-seg]').forEach(function (input) {
        item[input.getAttribute('data-seg')] = input.value;
      });
      rows.push(item);
    });
    return rows;
  }

  function segmentFigure(row) {
    var nights = segmentNights(row);
    var single = parseAmount(row.single_rooms);
    var singleRate = parseAmount(row.single_rate);
    var doubleRooms = parseAmount(row.double_rooms);
    var doubleRate = parseAmount(row.double_rate);
    var triple = parseAmount(row.triple_rooms);
    var tripleRate = parseAmount(row.triple_rate);
    var nightly = (single * singleRate) + (doubleRooms * doubleRate) + (triple * tripleRate);
    return {
      nights: nights,
      day: segmentDay(row.check_in),
      revenue: nights * nightly,
      covers: nights * ((single * 1) + (doubleRooms * 2) + (triple * 3)),
      singleRn: nights * single,
      singleRev: nights * single * singleRate,
      doubleRn: nights * doubleRooms,
      doubleRev: nights * doubleRooms * doubleRate,
      tripleRn: nights * triple,
      tripleRev: nights * triple * tripleRate
    };
  }

  function segmentsOverlap(rows) {
    var ranges = [];
    rows.forEach(function (row) {
      if (segmentNights(row) > 0) ranges.push(row);
    });
    for (var i = 0; i < ranges.length; i += 1) {
      for (var j = i + 1; j < ranges.length; j += 1) {
        if (ranges[i].check_in < ranges[j].check_out && ranges[j].check_in < ranges[i].check_out) {
          return true;
        }
      }
    }
    return false;
  }

  function segmentDateLimits() {
    var arrival = field('gb_check_in');
    var departure = field('gb_check_out');
    var limits = '';
    if (arrival && arrival.value) limits += ' min="' + String(arrival.value).replace(/"/g, '&quot;') + '"';
    if (departure && departure.value) limits += ' max="' + String(departure.value).replace(/"/g, '&quot;') + '"';
    return limits;
  }

  function limitSegmentDates() {
    var arrival = field('gb_check_in');
    var departure = field('gb_check_out');
    document.querySelectorAll('#gb-segment-body input[data-seg="check_in"], #gb-segment-body input[data-seg="check_out"]').forEach(function (input) {
      if (arrival && arrival.value) input.min = arrival.value;
      else input.removeAttribute('min');
      if (departure && departure.value) input.max = departure.value;
      else input.removeAttribute('max');
    });
  }

  function segField(key, value, numeric, isDate) {
    var type = isDate ? 'date' : 'text';
    var extra = isDate ? segmentDateLimits() : ' inputmode="' + (numeric ? 'numeric' : 'decimal') + '"';
    var classes = 'form-control form-control-sm' + (isDate ? '' : (numeric ? ' js-digits' : ' js-decimal'));
    return '<input type="' + type + '"' + extra + ' class="' + classes + '" data-seg="' + key + '" value="' + String(value == null ? '' : value).replace(/"/g, '&quot;') + '">';
  }

  function renderSegmentTable(rows) {
    var body = document.getElementById('gb-segment-body');
    var wrap = document.getElementById('gb-segment-wrap');
    if (!body || !wrap) return;
    if (!rows.length) {
      body.innerHTML = '';
      wrap.hidden = true;
      return;
    }
    wrap.hidden = false;
    body.innerHTML = rows.map(function (row, index) {
      var nights = segmentNights(row);
      return '<tr>'
        + '<td>' + segField('check_in', row.check_in, false, true) + '</td>'
        + '<td>' + segField('check_out', row.check_out, false, true) + '</td>'
        + '<td class="text-nowrap" data-seg-day>' + segmentDay(row.check_in) + '</td>'
        + '<td data-seg-nights>' + (nights > 0 ? String(nights) : '—') + '</td>'
        + '<td>' + segField('single_rooms', row.single_rooms, true, false) + '</td>'
        + '<td>' + segField('single_rate', row.single_rate, false, false) + '</td>'
        + '<td>' + segField('double_rooms', row.double_rooms, true, false) + '</td>'
        + '<td>' + segField('double_rate', row.double_rate, false, false) + '</td>'
        + '<td>' + segField('triple_rooms', row.triple_rooms, true, false) + '</td>'
        + '<td>' + segField('triple_rate', row.triple_rate, false, false) + '</td>'
        + '<td class="stay-calc" data-seg-revenue>0.00</td>'
        + '<td class="stay-calc gb-seg-bb" data-seg-bb>0.00</td>'
        + '<td class="stay-calc gb-seg-dinner" data-seg-dinner>0.00</td>'
        + '<td><button type="button" class="btn btn-sm btn-outline-secondary" data-remove-segment="' + index + '" aria-label="Remove date range"><i class="bi bi-x-lg"></i></button></td>'
        + '</tr>';
    }).join('');
    body.querySelectorAll('[data-seg]').forEach(function (input) {
      var tr = input.closest('tr');
      var index = Array.prototype.indexOf.call(body.children, tr);
      input.name = 'stay_segments[' + index + '][' + input.getAttribute('data-seg') + ']';
    });
  }

  function setSegmentText(id, value) {
    var el = document.getElementById(id);
    if (el) el.textContent = value;
  }

  function syncSegmentMealColumns() {
    var basis = selectedBasis();
    var showBreakfast = basis === 'BB' || basis === 'DBB';
    var showDinner = basis === 'DBB';
    document.querySelectorAll('.gb-seg-bb').forEach(function (el) { el.hidden = !showBreakfast; });
    document.querySelectorAll('.gb-seg-dinner').forEach(function (el) { el.hidden = !showDinner; });
  }

  function rememberRoomCards() {
    if (segmentSnapshot || loadedWithSegments) return;
    segmentSnapshot = {
      single_rooms: field('gb_single_rooms') ? field('gb_single_rooms').value : '',
      single_rate: field('gb_single_rate') ? field('gb_single_rate').value : '',
      double_rooms: field('gb_double_rooms') ? field('gb_double_rooms').value : '',
      double_rate: field('gb_double_rate') ? field('gb_double_rate').value : '',
      triple_rooms: field('gb_triple_rooms') ? field('gb_triple_rooms').value : '',
      triple_rate: field('gb_triple_rate') ? field('gb_triple_rate').value : ''
    };
  }

  function restoreRoomCards() {
    if (!segmentSnapshot) return;
    Object.keys(segmentSnapshot).forEach(function (key) {
      var el = field('gb_' + key);
      if (el) el.value = segmentSnapshot[key];
    });
    segmentSnapshot = null;
    unlockRoomFields();
  }

  function applySegmentHints() {
    setHint('gb_rooms_hint', 'Each date range is calculated on its own. Room counts are rooms × nights, and each rate is that room type’s revenue divided by its room nights.');
    setHint('gb_total_rns_hint', 'Sum of each date range’s rooms × nights.');
    setHint('gb_total_revenue_hint', 'Sum of each date range: Nights × ((Single × rate) + (Double × rate) + (Triple × rate)).');
    setHint('gb_bb_revenue_hint', 'Sum of each date range: Nights × covers × breakfast rate. Covers = (Single × 1) + (Double × 2) + (Triple × 3).');
    setHint('gb_dinner_revenue_hint', 'Sum of each date range: Nights × covers × dinner rate. Covers = (Single × 1) + (Double × 2) + (Triple × 3).');
  }

  function updateSegments(rows) {
    var basis = selectedBasis();
    var breakfastRate = (basis === 'BB' || basis === 'DBB') ? num('gb_breakfast_rate') : 0;
    var dinnerRate = basis === 'DBB' ? num('gb_dinner_rate') : 0;
    var totals = {
      nights: 0,
      revenue: 0,
      bb: 0,
      dinner: 0,
      singleRn: 0,
      singleRev: 0,
      doubleRn: 0,
      doubleRev: 0,
      tripleRn: 0,
      tripleRev: 0,
      rooms: 0
    };
    var body = document.getElementById('gb-segment-body');
    var trs = body ? body.querySelectorAll('tr') : [];
    rows.forEach(function (row, index) {
      var fig = segmentFigure(row);
      var bb = fig.covers * breakfastRate;
      var dinner = fig.covers * dinnerRate;
      var tr = trs[index];
      if (tr) {
        var dayCell = tr.querySelector('[data-seg-day]');
        var nightsCell = tr.querySelector('[data-seg-nights]');
        var revenueCell = tr.querySelector('[data-seg-revenue]');
        var bbCell = tr.querySelector('[data-seg-bb]');
        var dinnerCell = tr.querySelector('[data-seg-dinner]');
        if (dayCell) dayCell.textContent = fig.day;
        if (nightsCell) nightsCell.textContent = fig.nights > 0 ? String(fig.nights) : '—';
        if (revenueCell) revenueCell.textContent = money(fig.revenue);
        if (bbCell) bbCell.textContent = money(bb);
        if (dinnerCell) dinnerCell.textContent = money(dinner);
      }
      if (fig.nights <= 0) return;
      totals.nights += fig.nights;
      totals.revenue += fig.revenue;
      totals.bb += bb;
      totals.dinner += dinner;
      totals.singleRn += fig.singleRn;
      totals.singleRev += fig.singleRev;
      totals.doubleRn += fig.doubleRn;
      totals.doubleRev += fig.doubleRev;
      totals.tripleRn += fig.tripleRn;
      totals.tripleRev += fig.tripleRev;
      totals.rooms += fig.singleRn + fig.doubleRn + fig.tripleRn;
    });
    setSegmentText('gb-segment-nights', String(totals.nights));
    setSegmentText('gb-segment-single-rooms', String(Math.round(totals.singleRn)));
    setSegmentText('gb-segment-single-rate', money(weightedRate(totals.singleRn, totals.singleRev)));
    setSegmentText('gb-segment-double-rooms', String(Math.round(totals.doubleRn)));
    setSegmentText('gb-segment-double-rate', money(weightedRate(totals.doubleRn, totals.doubleRev)));
    setSegmentText('gb-segment-triple-rooms', String(Math.round(totals.tripleRn)));
    setSegmentText('gb-segment-triple-rate', money(weightedRate(totals.tripleRn, totals.tripleRev)));
    setSegmentText('gb-segment-revenue', money(totals.revenue));
    setSegmentText('gb-segment-bb', money(totals.bb));
    setSegmentText('gb-segment-dinner', money(totals.dinner));
    var note = document.getElementById('gb-segment-note');
    if (note) {
      var overlap = segmentsOverlap(rows);
      note.hidden = !overlap;
      note.textContent = overlap
        ? 'Some date ranges overlap. Each range is still calculated on its own, and both are included in the totals.'
        : '';
    }
    syncSegmentMealColumns();
    return totals;
  }

  function applySegmentRoomTotals(totals) {
    [
      ['single', totals.singleRn, totals.singleRev],
      ['double', totals.doubleRn, totals.doubleRev],
      ['triple', totals.tripleRn, totals.tripleRev]
    ].forEach(function (item) {
      var roomsEl = field('gb_' + item[0] + '_rooms');
      var rateEl = field('gb_' + item[0] + '_rate');
      if (roomsEl) {
        roomsEl.value = String(Math.round(item[1]));
        roomsEl.readOnly = true;
      }
      if (rateEl) {
        rateEl.value = money(weightedRate(item[1], item[2]));
        rateEl.readOnly = true;
      }
    });
  }

  function recalc() {
    var segments = readSegmentRows();
    var active = segments.filter(function (row) { return segmentNights(row) > 0; });
    if (active.length) {
      rememberRoomCards();
      var totals = updateSegments(segments);
      applySegmentRoomTotals(totals);
      applySegmentHints();
      renderDailyBreakdown(null);
      writeRevenue(totals.rooms, totals.revenue, { bb: totals.bb, dinner: totals.dinner });
      return;
    }

    if (document.getElementById('gb-segment-body')) {
      updateSegments(segments);
    }
    if (segmentSnapshot) restoreRoomCards();
    var rows = dailyRows();
    if (rows.length) {
      var summary = summariseDates(rows);
      applyDailyRoomTotals(summary);
      renderDailyBreakdown(summary);
      syncDailyFields(rows);
      writeRevenue(summary.rooms, summary.revenue, mealTotals(summary));
      return;
    }

    unlockRoomFields();
    resetAggregateHints();
    renderDailyBreakdown(null);
    var nights = num('gb_nights');
    var single = num('gb_single_rooms');
    var doubleRooms = num('gb_double_rooms');
    var triple = num('gb_triple_rooms');
    var totalRev = (
      (single * num('gb_single_rate'))
      + (doubleRooms * num('gb_double_rate'))
      + (triple * num('gb_triple_rate'))
    ) * nights;
    writeRevenue(
      (single + doubleRooms + triple) * nights,
      totalRev
    );
  }

  function copyEnquiryRooms() {
    var nights = document.getElementById('nights');
    var targetNights = field('gb_nights');
    if (nights && targetNights && nights.value !== '') {
      targetNights.value = nights.value;
    }
    if (dailyRows().length) {
      recalc();
    }
  }

  function localToday() {
    var now = new Date();
    var month = String(now.getMonth() + 1).padStart(2, '0');
    var day = String(now.getDate()).padStart(2, '0');
    return now.getFullYear() + '-' + month + '-' + day;
  }

  function selectedPaymentTerm() {
    var checked = form.querySelector('.js-payment-term:checked');
    return checked ? checked.value : '';
  }

  function shiftDate(iso, days) {
    if (!iso) return '';
    var parts = String(iso).split('-');
    if (parts.length !== 3) return '';
    var date = new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]));
    if (isNaN(date.getTime())) return '';
    date.setDate(date.getDate() + days);
    var month = String(date.getMonth() + 1).padStart(2, '0');
    var day = String(date.getDate()).padStart(2, '0');
    return date.getFullYear() + '-' + month + '-' + day;
  }

  var paymentDueReady = false;

  function updatePaymentDue() {
    var wrap = field('gb_payment_term_days_wrap');
    var daysEl = field('gb_payment_term_days');
    var due = field('gb_payment_due_date');
    var hint = field('gb_payment_due_hint');
    var term = selectedPaymentTerm();

    if (wrap) wrap.hidden = term === '';
    if (!due) return;

    function showDue(value) {
      due.value = value;
      due.dispatchEvent(new Event('input', { bubbles: true }));
    }

    if (term === '') {
      if (paymentDueReady) showDue('');
      if (hint) hint.textContent = 'Select a payment term and enter the number of days.';
      return;
    }

    if (!daysEl || daysEl.value === '') {
      showDue('');
      if (hint) hint.textContent = 'Enter the number of days.';
      return;
    }

    var days = parseInt(daysEl.value, 10);
    if (isNaN(days)) {
      showDue('');
      return;
    }

    var base = field(term === 'Pre Arrival' ? 'gb_check_in' : 'gb_check_out');
    showDue(base && base.value ? shiftDate(base.value, term === 'Pre Arrival' ? -days : days) : '');
    if (hint) {
      hint.textContent = term === 'Pre Arrival'
        ? 'Arrival date minus the number of days.'
        : 'Departure date plus the number of days.';
    }
  }

  form.querySelectorAll('.js-payment-term').forEach(function (box) {
    box.addEventListener('change', function () {
      if (box.checked) {
        form.querySelectorAll('.js-payment-term').forEach(function (other) {
          if (other !== box) other.checked = false;
        });
      }
      updatePaymentDue();
    });
  });

  var paymentDaysEl = field('gb_payment_term_days');
  if (paymentDaysEl) {
    paymentDaysEl.addEventListener('input', updatePaymentDue);
    paymentDaysEl.addEventListener('change', updatePaymentDue);
  }

  function syncDepartureMin() {
    var arrival = field('gb_check_in');
    var departure = field('gb_check_out');
    if (!arrival || !departure) return;
    if (!arrival.value) {
      departure.removeAttribute('min');
      return;
    }
    var minDeparture = shiftDate(arrival.value, 1);
    if (!minDeparture) return;
    departure.min = minDeparture;
    if (departure.value && departure.value < minDeparture) {
      departure.value = '';
      departure.dispatchEvent(new Event('input', { bubbles: true }));
      departure.dispatchEvent(new Event('change', { bubbles: true }));
    }
  }

  var arrivalEl = field('gb_check_in');
  if (arrivalEl) {
    arrivalEl.min = localToday();
    arrivalEl.addEventListener('change', function () {
      updateDay();
      syncDepartureMin();
      updatePaymentDue();
      limitSegmentDates();
    });
    arrivalEl.addEventListener('input', function () {
      updateDay();
      syncDepartureMin();
      updatePaymentDue();
      limitSegmentDates();
    });
  }
  var departureEl = field('gb_check_out');
  if (departureEl) {
    departureEl.addEventListener('change', function () {
      updatePaymentDue();
      limitSegmentDates();
    });
    departureEl.addEventListener('input', function () {
      updatePaymentDue();
      limitSegmentDates();
    });
  }
  syncDepartureMin();
  updatePaymentDue();
  paymentDueReady = true;

  function selectedHasCommission() {
    var checked = form.querySelector('.js-has-commission:checked');
    return checked ? checked.value : '';
  }

  function updateCommissionPayable() {
    var wrap = field('gb_commission_payable_wrap');
    var status = field('gb_commission_payable_status');
    var percentWrap = field('gb_commission_percentage_wrap');
    var percent = field('gb_commission_percentage');
    var hasCommission = selectedHasCommission() === '1';
    if (wrap) wrap.hidden = !hasCommission;
    if (percentWrap) percentWrap.hidden = !hasCommission;
    if (!hasCommission && status) status.value = '';
    if (!hasCommission && percent) percent.value = '';
  }

  form.querySelectorAll('.js-has-commission').forEach(function (box) {
    box.addEventListener('change', function () {
      if (box.checked) {
        form.querySelectorAll('.js-has-commission').forEach(function (other) {
          if (other !== box) other.checked = false;
        });
      }
      updateCommissionPayable();
    });
  });
  updateCommissionPayable();

  form.querySelectorAll('.gb-calc').forEach(function (el) {
    el.addEventListener('input', recalc);
    el.addEventListener('change', recalc);
  });
  renderSegmentTable(segmentSeed());
  var addSegmentBtn = document.getElementById('gb-add-stay-segment');
  if (addSegmentBtn) {
    addSegmentBtn.addEventListener('click', function () {
      var rows = readSegmentRows();
      if (!rows.length) {
        rows.push({
          check_in: field('gb_check_in') ? field('gb_check_in').value : '',
          check_out: field('gb_check_out') ? field('gb_check_out').value : '',
          single_rooms: field('gb_single_rooms') ? field('gb_single_rooms').value : '',
          single_rate: field('gb_single_rate') ? field('gb_single_rate').value : '',
          double_rooms: field('gb_double_rooms') ? field('gb_double_rooms').value : '',
          double_rate: field('gb_double_rate') ? field('gb_double_rate').value : '',
          triple_rooms: field('gb_triple_rooms') ? field('gb_triple_rooms').value : '',
          triple_rate: field('gb_triple_rate') ? field('gb_triple_rate').value : ''
        });
      } else {
        var previous = rows[rows.length - 1];
        rows.push({
          check_in: '',
          check_out: '',
          single_rooms: previous.single_rooms || '',
          single_rate: previous.single_rate || '',
          double_rooms: previous.double_rooms || '',
          double_rate: previous.double_rate || '',
          triple_rooms: previous.triple_rooms || '',
          triple_rate: previous.triple_rate || ''
        });
      }
      renderSegmentTable(rows);
      recalc();
    });
  }
  var segmentBody = document.getElementById('gb-segment-body');
  if (segmentBody) {
    segmentBody.addEventListener('input', function (event) {
      var input = event.target;
      if (!input || !input.classList) return;
      if (input.classList.contains('js-digits')) {
        input.value = input.value.replace(/\D/g, '');
      }
      if (input.classList.contains('js-decimal')) {
        var cleaned = input.value.replace(/[^\d.]/g, '');
        var parts = cleaned.split('.');
        input.value = parts.length > 1 ? parts[0] + '.' + parts.slice(1).join('') : parts[0];
      }
      recalc();
    });
    segmentBody.addEventListener('change', recalc);
    segmentBody.addEventListener('click', function (event) {
      var button = event.target.closest('[data-remove-segment]');
      if (!button) return;
      var rows = readSegmentRows();
      var index = parseInt(button.getAttribute('data-remove-segment'), 10);
      if (!isNaN(index)) rows.splice(index, 1);
      renderSegmentTable(rows);
      recalc();
    });
  }
  var stayList = document.getElementById('stay-date-list');
  if (stayList) {
    stayList.addEventListener('input', recalc);
    stayList.addEventListener('change', recalc);
  }
  ['check_in', 'check_out'].forEach(function (id) {
    var el = document.getElementById(id);
    if (!el) return;
    el.addEventListener('change', recalc);
    el.addEventListener('input', recalc);
  });
  updateDay();
  recalc();

  var openBtn = document.getElementById('show-group-booking');
  var section = document.getElementById('group-booking-section');
  function openSection() {
    if (!section) return;
    copyEnquiryRooms();
    recalc();
    section.classList.remove('d-none');
    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
  if (openBtn) {
    openBtn.addEventListener('click', openSection);
  }
  if (section && !section.classList.contains('d-none') && window.location.hash === '#group-booking-section') {
    section.scrollIntoView({ block: 'start' });
  }
})();
</script>
<?php /**PATH E:\Working\hotel_reservation_portal\resources\views/enquiries/partials/group-booking-scripts.blade.php ENDPATH**/ ?>