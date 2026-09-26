/**
 * Shared page utilities — tables, search, pagination, badges
 */
(function () {
  'use strict';

  window.HGBMS = window.HGBMS || {};

  HGBMS.formatCurrency = function (n) {
    return '£' + Number(n).toLocaleString('en-GB', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
  };

  HGBMS.badge = function (status, map) {
    var m = map || {
      Confirmed: 'confirmed', Active: 'active', Paid: 'paid', Provisional: 'pending',
      Pending: 'pending', Partial: 'partial', Unpaid: 'unpaid', Cancelled: 'cancelled',
      New: 'enquiry', 'Follow Up': 'pending', Quoted: 'partial', Lost: 'cancelled',
      Inactive: 'inactive', Open: 'enquiry', Closed: 'inactive'
    };
    var cls = m[status] || 'pending';
    return '<span class="badge-status badge-' + cls + '">' + status + '</span>';
  };

  HGBMS.actionsDropdown = function (items) {
    var html = '<div class="dropdown"><button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button><ul class="dropdown-menu dropdown-menu-end">';
    items.forEach(function (it) {
      var cls = it.danger ? ' text-danger' : '';
      html += '<li><a class="dropdown-item' + cls + '" href="' + (it.href || '#') + '"' +
        (it.modal ? ' data-bs-toggle="modal" data-bs-target="#' + it.modal + '"' : '') + '>' +
        '<i class="bi bi-' + it.icon + '"></i> ' + it.label + '</a></li>';
    });
    return html + '</ul></div>';
  };

  HGBMS.pageHeader = function (opts) {
    var crumbs = (opts.breadcrumbs || []).map(function (c, i, arr) {
      if (i === arr.length - 1) return '<li class="breadcrumb-item active">' + c.label + '</li>';
      return '<li class="breadcrumb-item"><a href="' + c.href + '">' + c.label + '</a></li>';
    }).join('');

    var actions = (opts.actions || []).map(function (a) {
      if (a.primary) return '<a href="' + a.href + '" class="btn btn-accent btn-sm"><i class="bi bi-' + a.icon + '"></i> ' + a.label + '</a>';
      if (a.export) return '<button class="btn btn-outline-secondary btn-sm" data-export><i class="bi bi-download"></i> ' + a.label + '</button>';
      if (a.refresh) return '<button class="btn btn-outline-secondary btn-sm" data-refresh><i class="bi bi-arrow-clockwise"></i> ' + a.label + '</button>';
      if (a.modal) return '<button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#' + a.modal + '"><i class="bi bi-' + a.icon + '"></i> ' + a.label + '</button>';
      return '<a href="' + (a.href || '#') + '" class="btn btn-outline-secondary btn-sm"><i class="bi bi-' + a.icon + '"></i> ' + a.label + '</a>';
    }).join('');

    return '<div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">' +
      '<div><nav aria-label="breadcrumb"><ol class="breadcrumb mb-1">' + crumbs + '</ol></nav>' +
      '<h1 class="page-title">' + opts.title + '</h1>' +
      (opts.subtitle ? '<p class="page-subtitle">' + opts.subtitle + '</p>' : '') + '</div>' +
      (actions ? '<div class="d-flex gap-2 flex-wrap">' + actions + '</div>' : '') + '</div>';
  };

  HGBMS.tableToolbar = function (opts) {
    return '<div class="table-toolbar">' +
      '<div class="input-group search-input" style="max-width:280px">' +
      '<span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>' +
      '<input type="search" class="form-control border-start-0 table-search" placeholder="' + (opts.searchPlaceholder || 'Search…') + '" data-table="' + opts.tableId + '"></div>' +
      '<button class="btn btn-outline-secondary btn-sm" data-bs-toggle="offcanvas" data-bs-target="#filtersOffcanvas"><i class="bi bi-funnel"></i> Filters</button>' +
      '<button class="btn btn-outline-secondary btn-sm" data-refresh><i class="bi bi-arrow-clockwise"></i> Refresh</button>' +
      '<button class="btn btn-outline-secondary btn-sm" data-export><i class="bi bi-download"></i> Export</button>' +
      (opts.extra || '') + '</div>';
  };

  HGBMS.tableFooter = function (total, showing) {
    return '<div class="table-footer">' +
      '<span>Showing <strong>1</strong> to <strong>' + showing + '</strong> of <strong>' + total + '</strong> entries</span>' +
      '<nav><ul class="pagination pagination-sm mb-0">' +
      '<li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>' +
      '<li class="page-item active"><a class="page-link" href="#">1</a></li>' +
      '<li class="page-item"><a class="page-link" href="#">2</a></li>' +
      '<li class="page-item"><a class="page-link" href="#">3</a></li>' +
      '<li class="page-item"><a class="page-link" href="#">Next</a></li></ul></nav></div>';
  };

  HGBMS.initTableSearch = function () {
    document.querySelectorAll('.table-search').forEach(function (input) {
      input.addEventListener('input', function () {
        var q = input.value.toLowerCase();
        var table = document.getElementById(input.dataset.table);
        if (!table) return;
        table.querySelectorAll('tbody tr').forEach(function (row) {
          row.style.display = row.textContent.toLowerCase().indexOf(q) >= 0 ? '' : 'none';
        });
      });
    });
  };

  HGBMS.populateSelects = function () {
    if (typeof HGBMS_DATA === 'undefined') return;
    document.querySelectorAll('select[data-source="hotels"]').forEach(function (sel) {
      HGBMS_DATA.hotels.forEach(function (h) {
        sel.innerHTML += '<option>' + h.name + '</option>';
      });
    });
    document.querySelectorAll('select[data-source="agencies"]').forEach(function (sel) {
      HGBMS_DATA.agencies.forEach(function (a) {
        sel.innerHTML += '<option>' + a.name + '</option>';
      });
    });
  };

  document.addEventListener('DOMContentLoaded', function () {
    HGBMS.initTableSearch();
    HGBMS.populateSelects();
  });
})();