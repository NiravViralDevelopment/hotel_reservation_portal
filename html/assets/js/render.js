/**
 * Page renderers — populate tables from HGBMS_DATA
 */
(function () {
  'use strict';

  function fc(n) {
    if (window.HGBMS && HGBMS.formatCurrency) return HGBMS.formatCurrency(n);
    return '£' + Number(n || 0).toLocaleString('en-GB', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
  }
  function badge(s) {
    if (window.HGBMS && HGBMS.badge) return HGBMS.badge(s);
    return '<span class="badge-status badge-pending">' + (s || '') + '</span>';
  }
  function actions(items) {
    if (window.HGBMS && HGBMS.actionsDropdown) return HGBMS.actionsDropdown(items);
    return '';
  }

  var RENDERERS = {
    hotels: function () {
      var tb = document.getElementById('hotelsTableBody');
      if (!tb) return;
      tb.innerHTML = HGBMS_DATA.hotels.map(function (h) {
        return '<tr><td class="fw-semibold"><a href="hotel-detail.html?id=' + h.code + '">' + h.name + '</a></td>' +
          '<td><code>' + h.code + '</code></td><td>' + h.city + '</td><td>' + h.country + '</td>' +
          '<td class="text-center">' + h.rooms + '</td><td>' + badge(h.status) + '</td>' +
          '<td>' + h.manager + '</td><td>' + h.phone + '</td><td>' + h.email + '</td>' +
          '<td>' + actions([
            { icon: 'eye', label: 'View', href: 'hotel-detail.html?id=' + h.code },
            { icon: 'pencil', label: 'Edit', href: '#' },
            { icon: 'trash', label: 'Delete', modal: 'deleteModal', danger: true }
          ]) + '</td></tr>';
      }).join('');
    },

    companies: function () {
      var tb = document.getElementById('companiesTableBody');
      if (!tb) return;
      tb.innerHTML = HGBMS_DATA.companies.map(function (c) {
        return '<tr><td class="fw-semibold"><a href="company-detail.html?id=' + c.id + '">' + c.name + '</a></td>' +
          '<td>' + c.regNumber + '</td><td>' + c.city + '</td><td>' + c.country + '</td>' +
          '<td class="text-center">' + c.hotels + '</td><td>' + c.contacts + '</td>' +
          '<td>' + fc(c.revenue) + '</td><td>' + badge(c.status) + '</td>' +
          '<td>' + actions([
            { icon: 'eye', label: 'View', href: 'company-detail.html?id=' + c.id },
            { icon: 'pencil', label: 'Edit', href: '#' },
            { icon: 'trash', label: 'Delete', modal: 'deleteModal', danger: true }
          ]) + '</td></tr>';
      }).join('');
    },

    agencies: function () {
      var tb = document.getElementById('agenciesTableBody');
      if (!tb) return;
      tb.innerHTML = HGBMS_DATA.agencies.map(function (a) {
        return '<tr><td class="fw-semibold">' + a.name + '</td><td>' + a.code + '</td>' +
          '<td>' + a.contact + '</td><td>' + a.email + '</td><td>' + a.phone + '</td>' +
          '<td>' + a.city + '</td><td class="text-center">' + a.bookings + '</td>' +
          '<td>' + fc(a.revenue) + '</td><td>' + badge(a.status) + '</td>' +
          '<td>' + actions([
            { icon: 'eye', label: 'View', href: '#' },
            { icon: 'pencil', label: 'Edit', href: '#' },
            { icon: 'person-plus', label: 'Add Contact', href: 'contacts.html' }
          ]) + '</td></tr>';
      }).join('');
    },

    contacts: function () {
      var tb = document.getElementById('contactsTableBody');
      if (!tb) return;
      tb.innerHTML = HGBMS_DATA.contacts.map(function (c) {
        return '<tr><td>' + c.company + '</td><td>' + c.agency + '</td><td class="fw-semibold">' + c.name + '</td>' +
          '<td><a href="mailto:' + c.email + '">' + c.email + '</a></td><td>' + c.phone + '</td>' +
          '<td>' + c.position + '</td><td>' + c.country + '</td>' +
          '<td><span class="text-truncate d-inline-block" style="max-width:120px" title="' + c.notes + '">' + c.notes + '</span></td>' +
          '<td>' + actions([
            { icon: 'eye', label: 'View', href: '#' },
            { icon: 'pencil', label: 'Edit', href: '#' },
            { icon: 'envelope', label: 'Email', href: 'mailto:' + c.email }
          ]) + '</td></tr>';
      }).join('');
    },

    bookings: function () {
      if (window.HGBMS_BOOKINGS && HGBMS_BOOKINGS.renderList) {
        HGBMS_BOOKINGS.renderList(1);
      }
    },

    enquiries: function () {
      if (window.HGBMS_ENQUIRIES && HGBMS_ENQUIRIES.renderList) {
        HGBMS_ENQUIRIES.renderList(1);
      }
    },

    cancelled: function () {
      if (window.HGBMS_CANCELLED && HGBMS_CANCELLED.renderList) {
        HGBMS_CANCELLED.renderList(1);
      }
    },

    arrivals: function () {
      ['todayArrivalsBody', 'tomorrowArrivalsBody', 'weekArrivalsBody'].forEach(function (id, idx) {
        var tb = document.getElementById(id);
        if (!tb) return;
        var key = ['today', 'tomorrow', 'week'][idx];
        var data = HGBMS_DATA.arrivals[key] || [];
        tb.innerHTML = data.map(function (a) {
          return '<tr><td><a href="group-booking-detail.html?ref=' + a.ref + '" class="fw-semibold">' + a.ref + '</a></td>' +
            '<td>' + a.group + '</td><td>' + a.hotel + '</td><td>' + a.agency + '</td>' +
            '<td>' + a.arrival + '</td><td class="text-center">' + a.rooms + '</td><td class="text-center">' + a.pax + '</td>' +
            '<td>' + badge(a.status) + '</td></tr>';
        }).join('');
      });
    },

    departures: function () {
      ['todayDeparturesBody', 'upcomingDeparturesBody'].forEach(function (id, idx) {
        var tb = document.getElementById(id);
        if (!tb) return;
        var key = idx === 0 ? 'today' : 'upcoming';
        var data = HGBMS_DATA.departures[key] || [];
        tb.innerHTML = data.map(function (d) {
          return '<tr><td><a href="group-booking-detail.html?ref=' + d.ref + '" class="fw-semibold">' + d.ref + '</a></td>' +
            '<td>' + d.group + '</td><td>' + d.hotel + '</td><td>' + d.agency + '</td>' +
            '<td>' + d.departure + '</td><td class="text-center">' + d.rooms + '</td><td class="text-center">' + d.pax + '</td>' +
            '<td>' + badge(d.status) + '</td></tr>';
        }).join('');
      });
    },

    users: function () {
      var tb = document.getElementById('usersTableBody');
      if (!tb) return;
      tb.innerHTML = HGBMS_DATA.users.map(function (u) {
        return '<tr><td><div class="d-flex align-items-center gap-2"><div class="avatar-sm bg-brand">' + u.initials + '</div><span class="fw-semibold">' + u.name + '</span></div></td>' +
          '<td>' + u.email + '</td><td>' + u.role + '</td><td>' + u.department + '</td>' +
          '<td>' + u.lastLogin + '</td><td>' + badge(u.status) + '</td>' +
          '<td>' + actions([
            { icon: 'pencil', label: 'Edit', href: '#' },
            { icon: 'shield', label: 'Permissions', href: 'roles.html' },
            { icon: 'trash', label: 'Deactivate', modal: 'deleteModal', danger: true }
          ]) + '</td></tr>';
      }).join('');
    },

    audit: function () {
      var tb = document.getElementById('auditTableBody');
      if (!tb) return;
      tb.innerHTML = HGBMS_DATA.auditLogs.map(function (l) {
        return '<tr><td>' + l.timestamp + '</td><td>' + l.user + '</td><td>' + l.action + '</td>' +
          '<td>' + l.module + '</td><td><code class="small">' + l.details + '</code></td>' +
          '<td>' + l.ip + '</td></tr>';
      }).join('');
    },

    documents: function () {
      var tb = document.getElementById('documentsTableBody');
      if (!tb) return;
      tb.innerHTML = HGBMS_DATA.documents.map(function (d) {
        return '<tr><td><i class="bi bi-file-earmark-' + d.icon + ' me-2 text-accent"></i>' + d.name + '</td>' +
          '<td>' + d.category + '</td><td>' + d.booking + '</td><td>' + d.uploadedBy + '</td>' +
          '<td>' + d.date + '</td><td>' + d.size + '</td>' +
          '<td>' + actions([
            { icon: 'eye', label: 'Preview', href: '#' },
            { icon: 'download', label: 'Download', href: '#' },
            { icon: 'trash', label: 'Delete', modal: 'deleteModal', danger: true }
          ]) + '</td></tr>';
      }).join('');
    }
  };

  document.addEventListener('DOMContentLoaded', function () {
    var page = document.body.dataset.page;
    function run() {
      if (page && RENDERERS[page]) RENDERERS[page]();
      if (window.HGBMS) {
        if (HGBMS.initTableSearch) HGBMS.initTableSearch();
        if (HGBMS.populateSelects) HGBMS.populateSelects();
      }
    }
    if (document.getElementById('hgbms-root')) {
      document.addEventListener('hgbms:layout-ready', function () { setTimeout(run, 10); }, { once: true });
    } else {
      run();
    }
  });
})();
