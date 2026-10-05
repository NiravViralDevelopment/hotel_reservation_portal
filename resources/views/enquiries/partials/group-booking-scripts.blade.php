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

  function recalc() {
    var nights = num('gb_nights');
    var single = num('gb_single_rooms');
    var doubleRooms = num('gb_double_rooms');
    var triple = num('gb_triple_rooms');
    var totalRns = (single + doubleRooms + triple) * nights;
    var totalRev = (
      (single * num('gb_single_rate'))
      + (doubleRooms * num('gb_double_rate'))
      + (triple * num('gb_triple_rate'))
    ) * nights;
    var bbRevenue = ((single * 10) + (doubleRooms * 20) + (triple * 30)) * nights;
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

  function copyEnquiryRooms() {
    [
      ['nights', 'gb_nights'],
      ['single_rooms', 'gb_single_rooms'],
      ['single_rate', 'gb_single_rate'],
      ['double_rooms', 'gb_double_rooms'],
      ['double_rate', 'gb_double_rate'],
      ['triple_rooms', 'gb_triple_rooms'],
      ['triple_rate', 'gb_triple_rate']
    ].forEach(function (pair) {
      var source = document.getElementById(pair[0]);
      var target = field(pair[1]);
      if (!source || !target || source.value === '') return;
      target.value = source.value;
    });
  }

  function localToday() {
    var now = new Date();
    var month = String(now.getMonth() + 1).padStart(2, '0');
    var day = String(now.getDate()).padStart(2, '0');
    return now.getFullYear() + '-' + month + '-' + day;
  }

  var arrivalEl = field('gb_check_in');
  if (arrivalEl) {
    arrivalEl.min = localToday();
    arrivalEl.addEventListener('change', updateDay);
    arrivalEl.addEventListener('input', updateDay);
  }
  form.querySelectorAll('.gb-calc').forEach(function (el) {
    el.addEventListener('input', recalc);
    el.addEventListener('change', recalc);
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
