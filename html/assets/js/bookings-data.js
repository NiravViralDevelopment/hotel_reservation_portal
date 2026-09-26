/**
 * Group Bookings — Excel data merge, aggregation, listing pagination, detail render
 */
(function () {
  'use strict';

  var PAGE_SIZE = 50;

  function esc(s) {
    return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/"/g, '&quot;');
  }

  function fc(n) {
    if (window.HGBMS && HGBMS.formatCurrency) return HGBMS.formatCurrency(n);
    return '£' + Number(n || 0).toLocaleString('en-GB', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function fcm(n) {
    return '£' + Number(n || 0).toLocaleString('en-GB', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function badge(s) {
    if (window.HGBMS && HGBMS.badge) return HGBMS.badge(s);
    return '<span class="badge-status badge-pending">' + esc(s) + '</span>';
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

  function hasBreakfast(plan) {
    return /BB|HB|FB|DBB/i.test(plan || '');
  }

  function hasDinner(plan) {
    return /HB|FB|DBB/i.test(plan || '');
  }

  function mapBooking(b) {
    return {
      ref: b.blockId || b.ref,
      blockId: b.blockId || b.ref,
      group: b.agency || b.group || b.client,
      company: b.client,
      client: b.client,
      agency: b.agency,
      contact: b.contact,
      email: b.email,
      hotel: 'Brighton Harbour Hotel',
      arrival: b.arrival,
      departure: b.departure,
      arrivalDay: b.arrivalDay,
      nights: b.nights,
      rooms: b.rooms || b.totalRNs,
      pax: b.pax,
      totalRNs: b.totalRNs,
      mealPlan: b.mealPlan || 'BB',
      breakfast: hasBreakfast(b.mealPlan),
      dinner: hasDinner(b.mealPlan),
      adr: b.adr,
      revenue: b.revenue,
      bbRevenue: b.bbRevenue,
      dinnerRevenue: b.dinnerRevenue,
      nettRev: b.nettRev,
      paymentStatus: b.paymentStatusDisplay || 'Partial',
      paymentStatusRaw: b.paymentStatus,
      bookingStatus: b.bookingStatus || (b.status === 'DEF' ? 'Confirmed' : b.status),
      status: b.status,
      created: b.contractSent || b.contractRecd || b.created,
      contractSent: b.contractSent,
      contractRecd: b.contractRecd,
      savedDoc: b.savedDoc,
      paymentTerm: b.paymentTerm,
      dueDate: b.dueDate,
      cxlPolicy: b.cxlPolicy,
      cxlDueDate: b.cxlDueDate,
      cxlDate: b.cxlDate,
      commission: b.commission,
      singleRNs: b.singleRNs,
      singleRate: b.singleRate,
      doubleRNs: b.doubleRNs,
      doubleRate: b.doubleRate,
      tripleRNs: b.tripleRNs,
      tripleRate: b.tripleRate,
      update: b.update,
      roomingStatus: b.roomingStatus,
      invoiceStatus: b.invoiceStatus,
      invoiceDate: b.invoiceDate,
      invoiceAmount: b.invoiceAmount,
      commissionPayable: b.commissionPayable,
      updatesExtra: b.updatesExtra,
      operaCrossCheckRate: b.operaCrossCheckRate,
      sheet: b.sheet
    };
  }

  function aggregateByBlock(bookings) {
    var map = {};
    bookings.forEach(function (b) {
      if (!b.blockId) return;
      var mapped = mapBooking(b);
      if (!map[b.blockId]) {
        map[b.blockId] = mapped;
        map[b.blockId].dailyRows = [mapped];
        map[b.blockId]._dailyCount = 1;
      } else {
        var a = map[b.blockId];
        a.totalRNs += mapped.totalRNs;
        a.revenue += mapped.revenue;
        a.bbRevenue += mapped.bbRevenue;
        a.dinnerRevenue += mapped.dinnerRevenue;
        a.nettRev += mapped.nettRev;
        a.rooms += mapped.rooms;
        a.pax += mapped.pax;
        a.nights = Math.max(a.nights || 0, mapped.nights || 0);
        a.departure = mapped.departure || a.departure;
        if (mapped.update && a.update.indexOf(mapped.update) < 0) {
          a.update = a.update ? a.update + ' | ' + mapped.update : mapped.update;
        }
        a.dailyRows.push(mapped);
        a._dailyCount = a.dailyRows.length;
      }
    });
    return Object.keys(map).map(function (k) {
      var a = map[k];
      if (a.totalRNs > 0) a.adr = Math.round((a.revenue / a.totalRNs) * 100) / 100;
      return a;
    }).sort(function (x, y) {
      return String(x.arrival).localeCompare(String(y.arrival));
    });
  }

  function getFilteredList() {
    var list = window.HGBMS_BOOKINGS.list || [];
    var input = document.querySelector('.table-search[data-table="bookingsTable"]');
    if (!input || !input.value.trim()) return list;
    var q = input.value.toLowerCase();
    return list.filter(function (b) {
      return [
        b.blockId, b.agency, b.client, b.contact, b.email, b.status,
        b.paymentStatusRaw, b.mealPlan, b.sheet
      ].join(' ').toLowerCase().indexOf(q) >= 0;
    });
  }

  function renderPagination(total, page, pageSize) {
    var nav = document.getElementById('bookingsPagination');
    if (!nav) return;
    var pages = Math.max(1, Math.ceil(total / pageSize));
    page = Math.min(Math.max(1, page), pages);
    var html = '';
    html += '<li class="page-item' + (page <= 1 ? ' disabled' : '') + '">' +
      '<a class="page-link" href="#" data-page="' + (page - 1) + '">Previous</a></li>';
    var start = Math.max(1, page - 2);
    var end = Math.min(pages, start + 4);
    start = Math.max(1, end - 4);
    for (var p = start; p <= end; p++) {
      html += '<li class="page-item' + (p === page ? ' active' : '') + '">' +
        '<a class="page-link" href="#" data-page="' + p + '">' + p + '</a></li>';
    }
    if (end < pages) {
      html += '<li class="page-item disabled"><span class="page-link">…</span></li>';
      html += '<li class="page-item"><a class="page-link" href="#" data-page="' + pages + '">' + pages + '</a></li>';
    }
    html += '<li class="page-item' + (page >= pages ? ' disabled' : '') + '">' +
      '<a class="page-link" href="#" data-page="' + (page + 1) + '">Next</a></li>';
    nav.innerHTML = html;
    window.HGBMS_BOOKINGS.currentPage = page;
  }

  function rowHtml(b) {
    var ref = encodeURIComponent(b.ref);
    return '<tr>' +
      '<td><a href="group-booking-detail.html?ref=' + ref + '" class="fw-semibold">' + esc(b.blockId) + '</a></td>' +
      '<td>' + esc(b.arrival) + '</td>' +
      '<td>' + esc(b.departure) + '</td>' +
      '<td>' + esc(b.arrivalDay) + '</td>' +
      '<td class="text-center">' + (b.nights || 0) + '</td>' +
      '<td>' + esc(b.client) + '</td>' +
      '<td>' + esc(b.agency) + '</td>' +
      '<td>' + esc(b.contact) + '</td>' +
      '<td>' + trunc(b.email, 22) + '</td>' +
      '<td>' + badge(b.status) + '</td>' +
      '<td>' + esc(b.contractSent) + '</td>' +
      '<td>' + esc(b.contractRecd) + '</td>' +
      '<td class="text-center">' + (b.savedDoc ? 'X' : '—') + '</td>' +
      '<td>' + trunc(b.paymentTerm, 18) + '</td>' +
      '<td>' + esc(b.dueDate) + '</td>' +
      '<td>' + trunc(b.paymentStatusRaw, 28) + '</td>' +
      '<td>' + trunc(b.cxlPolicy, 18) + '</td>' +
      '<td>' + esc(b.cxlDueDate) + '</td>' +
      '<td>' + esc(b.cxlDate) + '</td>' +
      '<td>' + esc(b.commission) + '</td>' +
      '<td class="text-center">' + (b.singleRNs || 0) + '</td>' +
      '<td class="text-end">' + fcm(b.singleRate) + '</td>' +
      '<td class="text-center">' + (b.doubleRNs || 0) + '</td>' +
      '<td class="text-end">' + fcm(b.doubleRate) + '</td>' +
      '<td class="text-center">' + (b.tripleRNs || 0) + '</td>' +
      '<td class="text-end">' + fcm(b.tripleRate) + '</td>' +
      '<td class="text-center">' + (b.totalRNs || 0) + '</td>' +
      '<td class="text-end fw-semibold">' + fc(b.revenue) + '</td>' +
      '<td class="text-end">' + fc(b.bbRevenue) + '</td>' +
      '<td class="text-end">' + fc(b.dinnerRevenue) + '</td>' +
      '<td class="text-end">' + fc(b.nettRev) + '</td>' +
      '<td>' + esc(b.mealPlan) + '</td>' +
      '<td>' + trunc(b.update, 24) + '</td>' +
      '<td>' + esc(b.roomingStatus) + '</td>' +
      '<td>' + esc(b.invoiceStatus) + '</td>' +
      '<td>' + esc(b.invoiceDate) + '</td>' +
      '<td class="text-end">' + (b.invoiceAmount ? fc(b.invoiceAmount) : '—') + '</td>' +
      '<td>' + trunc(b.commissionPayable, 20) + '</td>' +
      '<td class="text-end">' + (b.operaCrossCheckRate ? fcm(b.operaCrossCheckRate) : '—') + '</td>' +
      '<td class="text-center"><span class="badge bg-secondary-subtle text-secondary">' + (b._dailyCount || 1) + '</span></td>' +
      '<td>' + actions([
        { icon: 'eye', label: 'View', href: 'group-booking-detail.html?ref=' + ref },
        { icon: 'pencil', label: 'Edit', href: '#', modal: 'bookingFormModal' },
        { icon: 'file-earmark-pdf', label: 'Export PDF', href: '#' }
      ]) + '</td></tr>';
  }

  window.HGBMS_BOOKINGS = {
    currentPage: 1,
    list: [],
    byRef: {},
    dailyByBlock: {},

    load: function (excelData) {
      if (!excelData || !excelData.bookings) return;
      var aggregated = aggregateByBlock(excelData.bookings);
      var daily = excelData.bookings.map(mapBooking);
      var dailyByBlock = {};
      daily.forEach(function (d) {
        if (!dailyByBlock[d.blockId]) dailyByBlock[d.blockId] = [];
        dailyByBlock[d.blockId].push(d);
      });
      this.list = aggregated;
      this.byRef = {};
      this.dailyByBlock = dailyByBlock;
      aggregated.forEach(function (b) {
        window.HGBMS_BOOKINGS.byRef[b.ref] = b;
      });
      if (typeof HGBMS_DATA !== 'undefined') {
        HGBMS_DATA.bookings = aggregated;
        HGBMS_DATA.bookingsDaily = daily;
        HGBMS_DATA.bookingsMeta = excelData.meta;
      }
    },

    renderList: function (page) {
      page = page || this.currentPage || 1;
      var tb = document.getElementById('bookingsTableBody');
      if (!tb) return;
      var list = getFilteredList();
      var total = list.length;
      var pages = Math.max(1, Math.ceil(total / PAGE_SIZE));
      page = Math.min(Math.max(1, page), pages);
      var start = (page - 1) * PAGE_SIZE;
      var slice = list.slice(start, start + PAGE_SIZE);
      tb.innerHTML = slice.map(rowHtml).join('');
      renderPagination(total, page, PAGE_SIZE);
      var footer = document.getElementById('bookingsTableFooter');
      if (footer) {
        footer.innerHTML = 'Showing <strong>' + (total ? start + 1 : 0) + '</strong> to <strong>' +
          (start + slice.length) + '</strong> of <strong>' + total + '</strong> block entries' +
          ' (<strong>559</strong> daily rows in workbook) — Group Tracker 2026.xlsx';
      }
      var lbl = document.getElementById('bookingsCountLabel');
      if (lbl) lbl.textContent = total;
    },

    renderDetail: function () {
      var params = new URLSearchParams(window.location.search);
      var ref = params.get('ref') || params.get('blockId');
      var b = ref ? this.byRef[ref] : null;
      if (!b) {
        var title = document.getElementById('detailTitle');
        if (title) title.textContent = 'Booking not found';
        return;
      }

      function set(id, val) {
        var el = document.getElementById(id);
        if (el) el.textContent = val || '—';
      }
      function setHtml(id, val) {
        var el = document.getElementById(id);
        if (el) el.innerHTML = val || '—';
      }

      document.title = (b.blockId || b.ref) + ' | Group Booking';
      set('detailTitle', b.agency || b.group);
      set('detailSubtitle', 'Block Id ' + b.blockId + ' · ' + b.client);
      set('detailRef', b.blockId);
      set('detailGroup', b.agency || b.group);
      set('detailClient', b.client);
      set('detailAgency', b.agency);
      set('detailContact', b.contact);
      set('detailEmail', b.email);
      set('detailStatus', b.status);
      set('detailArrival', b.arrival);
      set('detailArrivalDay', b.arrivalDay);
      set('detailDeparture', b.departure);
      set('detailNights', String(b.nights || 0));
      set('detailTotalRNs', String(b.totalRNs || 0));
      set('detailMealPlan', b.mealPlan || 'BB');
      set('detailSavedDoc', b.savedDoc ? 'Yes (X)' : 'No');
      set('detailAdr', fcm(b.adr));
      set('detailRevenue', fc(b.revenue));
      set('detailNettRev', fc(b.nettRev));
      set('detailBbRev', fc(b.bbRevenue));
      set('detailDinnerRev', fc(b.dinnerRevenue));
      set('detailPaymentTerm', b.paymentTerm);
      set('detailDueDate', b.dueDate);
      set('detailPaymentRaw', b.paymentStatusRaw);
      set('detailPaymentDisplay', b.paymentStatus);
      set('detailCxlPolicy', b.cxlPolicy);
      set('detailCxlDue', b.cxlDueDate);
      set('detailCxlDate', b.cxlDate);
      set('detailCommission', b.commission);
      set('detailContractSent', b.contractSent);
      set('detailContractRecd', b.contractRecd);
      set('detailRoomingStatus', b.roomingStatus);
      set('detailInvoiceStatus', b.invoiceStatus);
      set('detailInvoiceDate', b.invoiceDate);
      set('detailInvoiceAmount', b.invoiceAmount ? fc(b.invoiceAmount) : '—');
      set('detailCommissionPayable', b.commissionPayable);
      set('detailOperaCrossCheck', b.operaCrossCheckRate ? fcm(b.operaCrossCheckRate) : '—');
      set('detailUpdate', b.update);
      set('detailSheet', b.sheet);
      set('detailDailyCount', String(b._dailyCount || (this.dailyByBlock[b.blockId] || []).length));

      var notes = document.getElementById('detailInternalNotes');
      if (notes) notes.value = b.update || '';

      set('detailBreadcrumb', b.blockId);
      setHtml('detailStatusBadge', badge(b.bookingStatus) + ' <span class="ms-1">' + badge(b.status) + '</span>');
      setHtml('detailPaymentBadge', badge(b.paymentStatus));

      var roomBody = document.getElementById('detailRoomBody');
      if (roomBody) {
        var rows = '';
        if (b.singleRNs) rows += '<tr><td>Single</td><td class="text-center">' + b.singleRNs + '</td><td class="text-end">' + fcm(b.singleRate) + '</td><td>—</td></tr>';
        if (b.doubleRNs) rows += '<tr><td>Double/Twin</td><td class="text-center">' + b.doubleRNs + '</td><td class="text-end">' + fcm(b.doubleRate) + '</td><td>—</td></tr>';
        if (b.tripleRNs) rows += '<tr><td>Triple</td><td class="text-center">' + b.tripleRNs + '</td><td class="text-end">' + fcm(b.tripleRate) + '</td><td>—</td></tr>';
        roomBody.innerHTML = rows || '<tr><td colspan="4" class="text-muted">No room type breakdown on aggregated block</td></tr>';
      }

      var dailyBody = document.getElementById('detailDailyBody');
      if (dailyBody) {
        var daily = this.dailyByBlock[b.blockId] || b.dailyRows || [];
        dailyBody.innerHTML = daily.map(function (d) {
          return '<tr><td>' + esc(d.sheet) + '</td><td>' + esc(d.arrival) + '</td><td>' + esc(d.departure) + '</td>' +
            '<td class="text-center">' + (d.nights || 0) + '</td><td class="text-center">' + (d.totalRNs || 0) + '</td>' +
            '<td class="text-end">' + fc(d.revenue) + '</td><td>' + esc(d.mealPlan) + '</td><td>' + trunc(d.update, 40) + '</td></tr>';
        }).join('');
      }

      var mealPane = document.getElementById('detailMealContent');
      if (mealPane) {
        mealPane.innerHTML = '<div class="row g-3">' +
          '<div class="col-md-4"><div class="info-card"><div class="info-card-label">BB/DBB Basis</div><div class="info-card-value">' + esc(b.mealPlan) + '</div></div></div>' +
          '<div class="col-md-4"><div class="info-card"><div class="info-card-label">BB Revenue (Nett £8.33)</div><div class="info-card-value">' + fc(b.bbRevenue) + '</div></div></div>' +
          '<div class="col-md-4"><div class="info-card"><div class="info-card-label">Dinner Revenue</div><div class="info-card-value">' + fc(b.dinnerRevenue) + '</div></div></div>' +
          '</div>';
      }

      var payPane = document.getElementById('detailPaymentContent');
      if (payPane) {
        payPane.innerHTML = '<div class="row g-3 mb-3">' +
          '<div class="col-md-3"><div class="info-card"><div class="info-card-label">Payment Term</div><div class="info-card-value">' + esc(b.paymentTerm) + '</div></div></div>' +
          '<div class="col-md-3"><div class="info-card"><div class="info-card-label">Due Date</div><div class="info-card-value">' + esc(b.dueDate) + '</div></div></div>' +
          '<div class="col-md-3"><div class="info-card"><div class="info-card-label">Payment Status</div><div class="info-card-value small">' + esc(b.paymentStatusRaw) + '</div></div></div>' +
          '<div class="col-md-3"><div class="info-card"><div class="info-card-label">Display Status</div><div class="info-card-value">' + badge(b.paymentStatus) + '</div></div></div>' +
          '</div><div class="row g-3">' +
          '<div class="col-md-4"><div class="info-card"><div class="info-card-label">Invoice Status</div><div class="info-card-value">' + esc(b.invoiceStatus) + '</div></div></div>' +
          '<div class="col-md-4"><div class="info-card"><div class="info-card-label">Invoice Date</div><div class="info-card-value">' + esc(b.invoiceDate) + '</div></div></div>' +
          '<div class="col-md-4"><div class="info-card"><div class="info-card-label">Invoice Amount</div><div class="info-card-value">' + (b.invoiceAmount ? fc(b.invoiceAmount) : '—') + '</div></div></div>' +
          '</div>';
      }
    }
  };

  if (typeof EXCEL_DATA !== 'undefined') {
    HGBMS_BOOKINGS.load(EXCEL_DATA);
  }

  document.addEventListener('click', function (e) {
    var link = e.target.closest('#bookingsPagination a[data-page]');
    if (!link) return;
    e.preventDefault();
    var page = parseInt(link.getAttribute('data-page'), 10);
    if (page >= 1) HGBMS_BOOKINGS.renderList(page);
  });

  document.addEventListener('input', function (e) {
    if (e.target.matches('.table-search[data-table="bookingsTable"]')) {
      HGBMS_BOOKINGS.renderList(1);
    }
  });

  document.addEventListener('DOMContentLoaded', function () {
    if (document.body.dataset.page === 'booking-detail') {
      var runDetail = function () { HGBMS_BOOKINGS.renderDetail(); };
      if (document.getElementById('hgbms-root')) {
        document.addEventListener('hgbms:layout-ready', runDetail, { once: true });
      } else {
        runDetail();
      }
    }
  });
})();
