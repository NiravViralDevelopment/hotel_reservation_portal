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

  function updateDay() {
    var arrival = document.getElementById('check_in');
    var dayEl = document.getElementById('day');
    if (!arrival || !dayEl) return;
    if (!arrival.value) {
      dayEl.value = '';
      return;
    }
    var date = new Date(arrival.value + 'T00:00:00');
    if (isNaN(date.getTime())) {
      dayEl.value = '';
      return;
    }
    dayEl.value = weekdays[date.getDay()];
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
    arrivalEl.addEventListener('change', updateDay);
    arrivalEl.addEventListener('input', updateDay);
  }

  document.querySelectorAll('.enquiry-calc').forEach(function (el) {
    el.addEventListener('input', recalcRevenue);
    el.addEventListener('change', recalcRevenue);
  });

  updateDay();
  recalcRevenue();
})();
</script>
