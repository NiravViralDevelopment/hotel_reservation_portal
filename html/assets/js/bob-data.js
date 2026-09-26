/**
 * BOB sheet — Dashboard & Revenue KPI wiring from Excel
 */
(function () {
  'use strict';

  function fc(n, dec) {
    dec = dec === undefined ? 0 : dec;
    return '£' + Number(n || 0).toLocaleString('en-GB', { minimumFractionDigits: dec, maximumFractionDigits: dec });
  }

  function fnum(n, dec) {
    dec = dec === undefined ? 0 : dec;
    return Number(n || 0).toLocaleString('en-GB', { minimumFractionDigits: dec, maximumFractionDigits: dec });
  }

  function setText(id, val) {
    var el = document.getElementById(id);
    if (el) el.textContent = val;
  }

  window.HGBMS_BOB = {
    load: function (data) {
      if (!data || !data.bob) return;
      if (typeof HGBMS_DATA !== 'undefined') {
        HGBMS_DATA.bob = data.bob;
        HGBMS_DATA.bobTotal = data.bobTotal;
        HGBMS_DATA.monthlyRevenue = data.bob.map(function (m) {
          return { month: m.month.slice(0, 3), value: m.bob2026 || m.revenue };
        });
        if (data.bobTotal) {
          HGBMS_DATA.stats.revenue = data.bobTotal.bob2026;
          HGBMS_DATA.stats.averageAdr = data.bobTotal.adr2026;
          HGBMS_DATA.stats.roomNights = data.bobTotal.roomNights2026;
          HGBMS_DATA.stats.breakfastRevenue = data.bobTotal.breakfast2026;
          HGBMS_DATA.stats.dinnerRevenue = data.bobTotal.dinner2026;
        }
        if (data.meta && data.meta.uniqueBlockCount) {
          HGBMS_DATA.stats.activeGroups = data.meta.uniqueBlockCount;
        }
      }
    },

    renderDashboard: function () {
      var t = (typeof EXCEL_DATA !== 'undefined' && EXCEL_DATA.bobTotal) ? EXCEL_DATA.bobTotal : null;
      if (!t) return;
      setText('statRevenue', fc(t.bob2026));
      setText('statAdr', fc(t.adr2026, 2));
      setText('statRoomNights', fnum(t.roomNights2026));
      setText('statBreakfast', fc(t.breakfast2026));
      setText('statDinner', fc(t.dinner2026));
      setText('statActiveGroups', fnum(EXCEL_DATA.meta ? EXCEL_DATA.meta.uniqueBlockCount : 349));

      var bars = document.getElementById('revenueChartBars');
      var labels = document.getElementById('revenueChartLabels');
      if (bars && EXCEL_DATA.bob) {
        var max = Math.max.apply(null, EXCEL_DATA.bob.map(function (m) { return m.bob2026; }));
        bars.innerHTML = EXCEL_DATA.bob.map(function (m) {
          return '<div class="chart-bar" style="height:' + Math.round((m.bob2026 / max) * 100) + '%" title="' + fc(m.bob2026) + '"></div>';
        }).join('');
        labels.innerHTML = EXCEL_DATA.bob.map(function (m) {
          return '<span>' + m.month.slice(0, 3) + '</span>';
        }).join('');
      }
    },

    renderRevenuePage: function () {
      var t = EXCEL_DATA && EXCEL_DATA.bobTotal;
      if (t) {
        setText('revTotalRevenue', fc(t.bob2026));
        setText('revAdr', fc(t.adr2026, 2));
        setText('revRoomNights', fnum(t.roomNights2026));
        setText('revBreakfast', fc(t.breakfast2026));
        setText('revDinner', fc(t.dinner2026));
        setText('revBobVariance', fc(t.bobVariance));
      }

      var tb = document.getElementById('bobTableBody');
      if (!tb || !EXCEL_DATA.bob) return;
      var rows = EXCEL_DATA.bob.map(function (m) {
        return '<tr><td class="fw-semibold">' + m.month + '</td>' +
          '<td class="text-end">' + fc(m.bob2026) + '</td><td class="text-end">' + fc(m.bob2025) + '</td>' +
          '<td class="text-end">' + fc(m.bobVariance) + '</td><td class="text-end">' + fc(m.stlyBob2025) + '</td>' +
          '<td class="text-end">' + fc(m.adr2026, 2) + '</td><td class="text-end">' + fc(m.adr2025, 2) + '</td>' +
          '<td class="text-end">' + fc(m.adrVariance, 2) + '</td><td class="text-end">' + fc(m.adrStly2025, 2) + '</td>' +
          '<td class="text-center">' + fnum(m.roomNights2026) + '</td><td class="text-center">' + fnum(m.roomNights2025) + '</td>' +
          '<td class="text-center">' + fnum(m.stlyRoomNights2025) + '</td>' +
          '<td class="text-end">' + fc(m.stlyBreakfastRevenue) + '</td><td class="text-end">' + fc(m.breakfast2026) + '</td>' +
          '<td class="text-end">' + fc(m.dinner2026) + '</td><td class="text-end">' + fnum(m.totalDinnerCovers, 2) + '</td></tr>';
      }).join('');
      if (t) {
        rows += '<tr class="table-light fw-semibold"><td>Total</td>' +
          '<td class="text-end">' + fc(t.bob2026) + '</td><td class="text-end">' + fc(t.bob2025) + '</td>' +
          '<td class="text-end">' + fc(t.bobVariance) + '</td><td class="text-end">' + fc(t.stlyBob2025) + '</td>' +
          '<td class="text-end">' + fc(t.adr2026, 2) + '</td><td class="text-end">' + fc(t.adr2025, 2) + '</td>' +
          '<td class="text-end">' + fc(t.adrVariance, 2) + '</td><td class="text-end">' + fc(t.adrStly2025, 2) + '</td>' +
          '<td class="text-center">' + fnum(t.roomNights2026) + '</td><td class="text-center">' + fnum(t.roomNights2025) + '</td>' +
          '<td class="text-center">' + fnum(t.stlyRoomNights2025) + '</td>' +
          '<td class="text-end">' + fc(t.stlyBreakfastRevenue) + '</td><td class="text-end">' + fc(t.breakfast2026) + '</td>' +
          '<td class="text-end">' + fc(t.dinner2026) + '</td><td class="text-end">' + fnum(t.totalDinnerCovers, 2) + '</td></tr>';
      }
      tb.innerHTML = rows;

      var chart = document.getElementById('revPageChart');
      var lbl = document.getElementById('revPageLabels');
      if (chart && EXCEL_DATA.bob) {
        var max = Math.max.apply(null, EXCEL_DATA.bob.map(function (m) { return m.bob2026; }));
        chart.innerHTML = EXCEL_DATA.bob.map(function (m) {
          return '<div class="chart-bar" style="height:' + Math.round((m.bob2026 / max) * 100) + '%" title="' + fc(m.bob2026) + '"></div>';
        }).join('');
        lbl.innerHTML = EXCEL_DATA.bob.map(function (m) {
          return '<span>' + m.month.slice(0, 3) + '</span>';
        }).join('');
      }
    }
  };

  if (typeof EXCEL_DATA !== 'undefined') {
    HGBMS_BOB.load(EXCEL_DATA);
  }

  document.addEventListener('DOMContentLoaded', function () {
    if (document.body.dataset.page === 'dashboard') {
      HGBMS_BOB.renderDashboard();
    }
  });

  document.addEventListener('hgbms:layout-ready', function () {
    if (document.getElementById('bobTableBody')) {
      HGBMS_BOB.renderRevenuePage();
    }
  }, { once: true });
})();
