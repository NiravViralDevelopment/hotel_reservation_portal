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
    var rnsDisplay = field('gb_total_rns_display');
    var rnsInput = field('gb_total_rns');
    var revDisplay = field('gb_total_revenue_display');
    var revInput = field('gb_total_revenue');
    if (rnsDisplay) rnsDisplay.value = String(totalRns);
    if (rnsInput) rnsInput.value = String(totalRns);
    if (revInput) revInput.value = totalRev.toFixed(2);
    if (revDisplay) revDisplay.value = formatPounds(totalRev);
  }

  var arrivalEl = field('gb_check_in');
  if (arrivalEl) {
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
