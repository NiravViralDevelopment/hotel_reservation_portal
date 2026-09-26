/**
 * Enquiries — full Excel columns (Enquiries 2026 + 2027), pagination
 */
(function () {
  'use strict';

  var PAGE_SIZE = 50;

  function esc(s) {
    return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/"/g, '&quot;');
  }

  function fc(n) {
    return '£' + Number(n || 0).toLocaleString('en-GB', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function actions(items) {
    if (window.HGBMS && HGBMS.actionsDropdown) return HGBMS.actionsDropdown(items);
    return '';
  }

  function trunc(s, len) {
    s = String(s || '');
    if (s.length <= len) return esc(s);
    return '<span title="' + esc(s) + '">' + esc(s.slice(0, len)) + '…</span>';
  }

  function getFilteredList() {
    var list = window.HGBMS_ENQUIRIES.list || [];
    var yearSel = document.getElementById('enqYearFilter');
    if (yearSel && yearSel.value) {
      list = list.filter(function (e) { return e.year === yearSel.value; });
    }
    var input = document.querySelector('.table-search[data-table="enquiriesTable"]');
    if (!input || !input.value.trim()) return list;
    var q = input.value.toLowerCase();
    return list.filter(function (e) {
      return [e.ref, e.groupName, e.email, e.remarks, e.cxlPolicy, e.sourceSheet, e.basis].join(' ').toLowerCase().indexOf(q) >= 0;
    });
  }

  function renderPagination(total, page) {
    var nav = document.getElementById('enquiriesPagination');
    if (!nav) return;
    var pages = Math.max(1, Math.ceil(total / PAGE_SIZE));
    page = Math.min(Math.max(1, page), pages);
    var html = '';
    html += '<li class="page-item' + (page <= 1 ? ' disabled' : '') + '"><a class="page-link" href="#" data-page="' + (page - 1) + '">&laquo; Prev</a></li>';
    var start = Math.max(1, page - 2);
    var end = Math.min(pages, start + 4);
    start = Math.max(1, end - 4);
    for (var p = start; p <= end; p++) {
      html += '<li class="page-item' + (p === page ? ' active' : '') + '"><a class="page-link" href="#" data-page="' + p + '">' + p + '</a></li>';
    }
    if (end < pages) {
      html += '<li class="page-item disabled"><span class="page-link">…</span></li>';
      html += '<li class="page-item"><a class="page-link" href="#" data-page="' + pages + '">' + pages + '</a></li>';
    }
    html += '<li class="page-item' + (page >= pages ? ' disabled' : '') + '"><a class="page-link" href="#" data-page="' + (page + 1) + '">Next &raquo;</a></li>';
    nav.innerHTML = html;
    window.HGBMS_ENQUIRIES.currentPage = page;
  }

  function rowHtml(e) {
    return '<tr>' +
      '<td class="fw-semibold">' + esc(e.ref) + '</td>' +
      '<td><span class="badge bg-secondary-subtle text-secondary">' + esc(e.year) + '</span></td>' +
      '<td>' + esc(e.date) + '</td>' +
      '<td>' + esc(e.day) + '</td>' +
      '<td class="text-center">' + (e.nights || 0) + '</td>' +
      '<td>' + esc(e.groupName) + '</td>' +
      '<td class="text-center">' + (e.roomsPerNight || 0) + '</td>' +
      '<td class="text-center">' + (e.single || 0) + '</td>' +
      '<td class="text-end">' + fc(e.singleRate) + '</td>' +
      '<td class="text-center">' + (e.double || 0) + '</td>' +
      '<td class="text-end">' + fc(e.doubleRate) + '</td>' +
      '<td class="text-center">' + (e.triple || 0) + '</td>' +
      '<td class="text-end">' + fc(e.tripleRate) + '</td>' +
      '<td>' + esc(e.basis) + '</td>' +
      '<td class="text-end fw-semibold">' + fc(e.totalRevenue) + '</td>' +
      '<td>' + trunc(e.cxlPolicy, 22) + '</td>' +
      '<td>' + esc(e.optionDate) + '</td>' +
      '<td>' + trunc(e.email, 24) + '</td>' +
      '<td>' + trunc(e.remarks, 28) + '</td>' +
      '<td>' + actions([
        { icon: 'eye', label: 'View', href: '#' },
        { icon: 'pencil', label: 'Edit', href: '#' },
        { icon: 'arrow-right-circle', label: 'Convert to Booking', href: 'group-bookings.html' }
      ]) + '</td></tr>';
  }

  window.HGBMS_ENQUIRIES = {
    currentPage: 1,
    list: [],

    load: function (data) {
      if (!data || !data.enquiries) return;
      this.list = data.enquiries;
      if (typeof HGBMS_DATA !== 'undefined') {
        HGBMS_DATA.enquiries = data.enquiries;
      }
    },

    renderStats: function () {
      var list = this.list;
      var y26 = list.filter(function (e) { return e.year === '2026'; }).length;
      var y27 = list.filter(function (e) { return e.year === '2027'; }).length;
      var rev = list.reduce(function (s, e) { return s + (e.totalRevenue || 0); }, 0);
      var set = function (id, val) {
        var el = document.getElementById(id);
        if (el) el.textContent = val;
      };
      set('enqStatTotal', list.length);
      set('enqStat2026', y26);
      set('enqStat2027', y27);
      set('enqStatRevenue', fc(rev));
      set('enqCountLabel', list.length);
    },

    renderList: function (page) {
      page = page || this.currentPage || 1;
      var tb = document.getElementById('enquiriesTableBody');
      if (!tb) return;
      var list = getFilteredList();
      var total = list.length;
      var pages = Math.max(1, Math.ceil(total / PAGE_SIZE));
      page = Math.min(Math.max(1, page), pages);
      var start = (page - 1) * PAGE_SIZE;
      var slice = list.slice(start, start + PAGE_SIZE);
      tb.innerHTML = slice.map(rowHtml).join('');
      renderPagination(total, page);
      var footer = document.getElementById('enquiriesTableFooter');
      if (footer) {
        footer.innerHTML = 'Page <strong>' + page + '</strong> of <strong>' + pages + '</strong> — showing ' +
          '<strong>' + (total ? start + 1 : 0) + '</strong>–<strong>' + (start + slice.length) + '</strong> of ' +
          '<strong>' + total + '</strong> enquiries (50 per page)';
      }
      this.renderStats();
    }
  };

  if (typeof EXCEL_DATA !== 'undefined') {
    HGBMS_ENQUIRIES.load(EXCEL_DATA);
  }

  document.addEventListener('click', function (e) {
    var link = e.target.closest('#enquiriesPagination a[data-page]');
    if (!link || link.closest('.page-item.disabled')) return;
    e.preventDefault();
    var page = parseInt(link.getAttribute('data-page'), 10);
    if (page >= 1) {
      HGBMS_ENQUIRIES.renderList(page);
      var wrap = document.getElementById('enquiriesTableWrapper');
      if (wrap) wrap.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
  });

  document.addEventListener('input', function (e) {
    if (e.target.matches('.table-search[data-table="enquiriesTable"]')) {
      HGBMS_ENQUIRIES.renderList(1);
    }
  });

  document.addEventListener('change', function (e) {
    if (e.target.id === 'enqYearFilter') {
      HGBMS_ENQUIRIES.renderList(1);
    }
  });
})();
