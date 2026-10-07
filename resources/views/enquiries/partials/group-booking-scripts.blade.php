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

  function applyDailyRoomTotals(rows) {
    ['single', 'double', 'triple'].forEach(function (type) {
      var rooms = 0;
      var rate = 0;
      rows.forEach(function (row) {
        rooms += parseAmount(row[type + '_rooms']);
        rate += parseAmount(row[type + '_rate']);
      });
      var roomsEl = field('gb_' + type + '_rooms');
      var rateEl = field('gb_' + type + '_rate');
      if (roomsEl) {
        roomsEl.value = String(Math.round(rooms));
        roomsEl.readOnly = true;
      }
      if (rateEl) {
        rateEl.value = money(rate);
        rateEl.readOnly = true;
      }
    });
    setHint('gb_rooms_hint', 'Room and rate totals are the sum of each stay date.');
    setHint('gb_total_rns_hint', '(Single + Double + Triple) × nights');
    setHint('gb_total_revenue_hint', '((Single × rate) + (Double × rate) + (Triple × rate)) × nights');
    setHint('gb_bb_revenue_hint', '(Single × 10 + Double × 20 + Triple × 30)');
  }

  function writeRevenue(totalRns, totalRev, bbRevenue) {
    var dinnerRevenue = 0;
    var nettRev = ((totalRev * 100) / 120) - bbRevenue;
    var rnsDisplay = field('gb_total_rns_display');
    var rnsInput = field('gb_total_rns');
    if (rnsDisplay) rnsDisplay.value = String(totalRns);
    if (rnsInput) rnsInput.value = String(totalRns);
    setMoney('gb_total_revenue', 'gb_total_revenue_display', totalRev);
    setMoney('gb_bb_revenue', 'gb_bb_revenue_display', bbRevenue);
    setMoney('gb_dinner_revenue', 'gb_dinner_revenue_display', dinnerRevenue);
    setMoney('gb_nett_rev_ex_vat', 'gb_nett_rev_ex_vat_display', nettRev);
  }

  function recalc() {
    var rows = dailyRows();
    if (rows.length) {
      applyDailyRoomTotals(rows);
      syncDailyFields(rows);
      var nights = num('gb_nights');
      var single = num('gb_single_rooms');
      var doubleRooms = num('gb_double_rooms');
      var triple = num('gb_triple_rooms');
      var totalRev = (
        (single * num('gb_single_rate'))
        + (doubleRooms * num('gb_double_rate'))
        + (triple * num('gb_triple_rate'))
      ) * nights;
      var bbRevenue = 0;
      rows.forEach(function (row) {
        [['single', 10], ['double', 20], ['triple', 30]].forEach(function (pair) {
          bbRevenue += parseAmount(row[pair[0] + '_rooms']) * pair[1];
        });
      });
      writeRevenue((single + doubleRooms + triple) * nights, totalRev, bbRevenue);
      return;
    }

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
      totalRev,
      ((single * 10) + (doubleRooms * 20) + (triple * 30)) * nights
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

  var arrivalEl = field('gb_check_in');
  if (arrivalEl) {
    arrivalEl.min = localToday();
    arrivalEl.addEventListener('change', function () {
      updateDay();
      updatePaymentDue();
    });
    arrivalEl.addEventListener('input', function () {
      updateDay();
      updatePaymentDue();
    });
  }
  var departureEl = field('gb_check_out');
  if (departureEl) {
    departureEl.addEventListener('change', updatePaymentDue);
    departureEl.addEventListener('input', updatePaymentDue);
  }
  updatePaymentDue();
  paymentDueReady = true;

  function selectedHasCommission() {
    var checked = form.querySelector('.js-has-commission:checked');
    return checked ? checked.value : '';
  }

  function updateCommissionPayable() {
    var wrap = field('gb_commission_payable_wrap');
    var status = field('gb_commission_payable_status');
    var hasCommission = selectedHasCommission() === '1';
    if (wrap) wrap.hidden = !hasCommission;
    if (!hasCommission && status) status.value = '';
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
