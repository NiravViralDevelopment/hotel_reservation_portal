/**
 * Cancelled Bookings — full Excel column listing (313 rows)
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

  function badge(s) {
    if (window.HGBMS && HGBMS.badge) return HGBMS.badge(s);
    return '<span class="badge-status badge-cancelled">' + esc(s) + '</span>';
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
    var list = window.HGBMS_CANCELLED.list || [];
    var input = document.querySelector('.table-search[data-table="cancelledTable"]');
    if (!input || !input.value.trim()) return list;
    var q = input.value.toLowerCase();
    return list.filter(function (b) {
      return [b.blockId, b.client, b.agency, b.contact, b.email, b.cxlPolicy, b.update].join(' ').toLowerCase().indexOf(q) >= 0;
    });
  }

  function renderPagination(total, page) {
    var nav = document.getElementById('cancelledPagination');
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
    window.HGBMS_CANCELLED.currentPage = page;
  }

  function rowHtml(b) {
    return '<tr>' +
      '<td class="fw-semibold">' + esc(b.blockId) + '</td>' +
      '<td>' + esc(b.arrival) + '</td>' +
      '<td>' + esc(b.departure) + '</td>' +
      '<td>' + esc(b.arrivalDay) + '</td>' +
      '<td class="text-center">' + (b.nights || 0) + '</td>' +
      '<td>' + esc(b.client) + '</td>' +
      '<td>' + esc(b.agency) + '</td>' +
      '<td>' + esc(b.contact) + '</td>' +
      '<td>' + trunc(b.email, 20) + '</td>' +
      '<td>' + badge(b.status || 'Cancelled') + '</td>' +
      '<td>' + esc(b.contractSent) + '</td>' +
      '<td>' + esc(b.contractRecd) + '</td>' +
      '<td class="text-center">' + (b.savedDoc ? 'X' : '—') + '</td>' +
      '<td>' + trunc(b.paymentTerm, 16) + '</td>' +
      '<td>' + esc(b.dueDate) + '</td>' +
      '<td>' + trunc(b.paymentStatus, 24) + '</td>' +
      '<td>' + trunc(b.cxlPolicy, 16) + '</td>' +
      '<td>' + esc(b.cxlDueDate) + '</td>' +
      '<td>' + esc(b.cxlDate) + '</td>' +
      '<td>' + esc(b.commission) + '</td>' +
      '<td class="text-center">' + (b.singleRNs || 0) + '</td>' +
      '<td class="text-end">' + fc(b.singleRate) + '</td>' +
      '<td class="text-center">' + (b.doubleRNs || 0) + '</td>' +
      '<td class="text-end">' + fc(b.doubleRate) + '</td>' +
      '<td class="text-center">' + (b.tripleRNs || 0) + '</td>' +
      '<td class="text-end">' + fc(b.tripleRate) + '</td>' +
      '<td class="text-center">' + (b.totalRNs || 0) + '</td>' +
      '<td class="text-end text-danger fw-semibold">' + fc(b.revenueLost) + '</td>' +
      '<td class="text-end">' + fc(b.bbRevenue) + '</td>' +
      '<td class="text-end">' + fc(b.dinnerRevenue) + '</td>' +
      '<td class="text-end">' + fc(b.nettRev) + '</td>' +
      '<td>' + esc(b.mealPlan) + '</td>' +
      '<td>' + trunc(b.update, 20) + '</td>' +
      '<td class="text-end">' + (b.cityTax ? fc(b.cityTax) : '—') + '</td>' +
      '<td>' + esc(b.rooming) + '</td>' +
      '<td>' + esc(b.invoiceStatus) + '</td>' +
      '<td>' + esc(b.invoiceDate) + '</td>' +
      '<td class="text-end">' + (b.invoiceAmount ? fc(b.invoiceAmount) : '—') + '</td>' +
      '<td>' + trunc(b.commissionPayable, 16) + '</td>' +
      '<td>' + trunc(b.commissionPayable2, 16) + '</td>' +
      '<td>' + actions([{ icon: 'eye', label: 'View', href: 'group-booking-detail.html?ref=' + encodeURIComponent(b.blockId) }]) + '</td>' +
      '</tr>';
  }

  window.HGBMS_CANCELLED = {
    currentPage: 1,
    list: [],

    load: function (data) {
      if (!data || !data.cancelledBookings) return;
      this.list = data.cancelledBookings;
      if (typeof HGBMS_DATA !== 'undefined') {
        HGBMS_DATA.cancelledBookings = data.cancelledBookings;
      }
    },

    renderStats: function () {
      var list = this.list;
      var totalRev = list.reduce(function (s, b) { return s + (b.revenueLost || 0); }, 0);
      var totalRNs = list.reduce(function (s, b) { return s + (b.totalRNs || 0); }, 0);
      var el;
      el = document.getElementById('cxlStatCount');
      if (el) el.textContent = list.length;
      el = document.getElementById('cxlStatRevenue');
      if (el) el.textContent = fc(totalRev);
      el = document.getElementById('cxlStatRNs');
      if (el) el.textContent = totalRNs.toLocaleString('en-GB');
      el = document.getElementById('cxlCountLabel');
      if (el) el.textContent = list.length;
    },

    renderList: function (page) {
      page = page || this.currentPage || 1;
      var tb = document.getElementById('cancelledTableBody');
      if (!tb) return;
      var list = getFilteredList();
      var total = list.length;
      var pages = Math.max(1, Math.ceil(total / PAGE_SIZE));
      page = Math.min(Math.max(1, page), pages);
      var start = (page - 1) * PAGE_SIZE;
      var slice = list.slice(start, start + PAGE_SIZE);
      tb.innerHTML = slice.map(rowHtml).join('');
      renderPagination(total, page);
      var footer = document.getElementById('cancelledTableFooter');
      if (footer) {
        footer.innerHTML = 'Page <strong>' + page + '</strong> of <strong>' + pages + '</strong> — showing ' +
          '<strong>' + (total ? start + 1 : 0) + '</strong>–<strong>' + (start + slice.length) + '</strong> of ' +
          '<strong>' + total + '</strong> cancelled entries (50 per page)';
      }
      this.renderStats();
    }
  };

  if (typeof EXCEL_DATA !== 'undefined') {
    HGBMS_CANCELLED.load(EXCEL_DATA);
  }

  document.addEventListener('click', function (e) {
    var link = e.target.closest('#cancelledPagination a[data-page]');
    if (!link || link.closest('.page-item.disabled')) return;
    e.preventDefault();
    var page = parseInt(link.getAttribute('data-page'), 10);
    if (page >= 1) {
      HGBMS_CANCELLED.renderList(page);
      var card = document.getElementById('cancelledTableWrapper');
      if (card) card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
  });

  document.addEventListener('input', function (e) {
    if (e.target.matches('.table-search[data-table="cancelledTable"]')) {
      HGBMS_CANCELLED.renderList(1);
    }
  });
})();
