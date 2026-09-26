/**
 * Shared layout injector — sidebar, header, common modals
 */
(function () {
  'use strict';

  var NAV = [
    { section: 'Main', items: [
      { href: 'dashboard.html', icon: 'speedometer2', label: 'Dashboard' },
      { href: 'hotels.html', icon: 'building', label: 'Hotels' },
      { href: 'companies.html', icon: 'briefcase', label: 'Companies' },
      { href: 'travel-agencies.html', icon: 'airplane', label: 'Travel Agencies' },
      { href: 'contacts.html', icon: 'person-lines-fill', label: 'Contacts' }
    ]},
    { section: 'Bookings', items: [
      { href: 'group-bookings.html', icon: 'calendar-check', label: 'Group Bookings' },
      { href: 'enquiries.html', icon: 'chat-square-text', label: 'Enquiries' },
      { href: 'cancelled-bookings.html', icon: 'x-circle', label: 'Cancelled Bookings' },
      { href: 'arrivals.html', icon: 'box-arrow-in-right', label: 'Arrivals' },
      { href: 'departures.html', icon: 'box-arrow-right', label: 'Departures' },
      { href: 'calendar.html', icon: 'calendar3', label: 'Calendar' }
    ]},
    { section: 'Finance', items: [
      { href: 'revenue.html', icon: 'currency-pound', label: 'Revenue' },
      { href: 'reports.html', icon: 'file-earmark-bar-graph', label: 'Reports' },
      { href: 'documents.html', icon: 'folder2-open', label: 'Documents' }
    ]},
    { section: 'Administration', items: [
      { href: 'users.html', icon: 'people', label: 'Users' },
      { href: 'roles.html', icon: 'shield-check', label: 'Roles' },
      { href: 'audit-logs.html', icon: 'journal-text', label: 'Audit Logs' },
      { href: 'settings.html', icon: 'gear', label: 'Settings' }
    ]}
  ];

  function currentPage() {
    return window.location.pathname.split('/').pop() || 'dashboard.html';
  }

  function buildSidebar() {
    var page = currentPage();
    var html = '<div class="sidebar-overlay" id="sidebarOverlay"></div>' +
      '<aside class="app-sidebar" id="appSidebar">' +
      '<div class="sidebar-brand"><div class="sidebar-brand-icon"><i class="bi bi-building"></i></div>' +
      '<span class="sidebar-brand-text">HGBMS</span></div><nav class="sidebar-nav">';

    NAV.forEach(function (group) {
      html += '<div class="nav-section-label">' + group.section + '</div>';
      group.items.forEach(function (item) {
        var active = page === item.href ? ' active' : '';
        html += '<a href="' + item.href + '" class="sidebar-link' + active + '" title="' + item.label + '">' +
          '<i class="bi bi-' + item.icon + '"></i><span class="sidebar-link-text">' + item.label + '</span></a>';
      });
    });

    html += '</nav><div class="sidebar-footer">' +
      '<a href="profile.html" class="sidebar-link' + (page === 'profile.html' ? ' active' : '') + '"><i class="bi bi-person-circle"></i><span class="sidebar-link-text">Profile</span></a>' +
      '<a href="index.html" class="sidebar-link"><i class="bi bi-box-arrow-left"></i><span class="sidebar-link-text">Logout</span></a>' +
      '</div></aside>';
    return html;
  }

  function buildHeader() {
    return '<header class="app-header">' +
      '<button class="header-toggle" data-sidebar-toggle aria-label="Toggle sidebar"><i class="bi bi-list"></i></button>' +
      '<div class="header-search"><i class="bi bi-search"></i><input type="search" placeholder="Search bookings, hotels, agencies…" aria-label="Global search"></div>' +
      '<div class="header-actions">' +
      '<button class="header-action-btn" data-theme-toggle data-bs-toggle="tooltip" title="Toggle theme"><i class="bi bi-moon-fill"></i></button>' +
      '<button class="header-action-btn" data-bs-toggle="offcanvas" data-bs-target="#filtersOffcanvas" title="Quick filters"><i class="bi bi-funnel"></i></button>' +
      '<div class="dropdown"><button class="header-action-btn" data-bs-toggle="dropdown" aria-label="Notifications"><i class="bi bi-bell"></i><span class="badge-dot"></span></button>' +
      '<div class="dropdown-menu dropdown-menu-end notification-dropdown p-0">' +
      '<div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom"><span class="fw-semibold">Notifications</span><a href="#" class="small">Mark all read</a></div>' +
      '<div id="notificationList"></div>' +
      '<div class="text-center py-2 border-top"><a href="#" class="small fw-semibold">View all</a></div></div></div>' +
      '<div class="dropdown"><button class="header-user dropdown-toggle" data-bs-toggle="dropdown">' +
      '<div class="header-user-avatar">RW</div><div class="header-user-info"><div class="header-user-name">Richard Whitmore</div>' +
      '<div class="header-user-role">Group Reservations Manager</div></div></button>' +
      '<ul class="dropdown-menu dropdown-menu-end">' +
      '<li><a class="dropdown-item" href="profile.html"><i class="bi bi-person"></i> My Profile</a></li>' +
      '<li><a class="dropdown-item" href="settings.html"><i class="bi bi-gear"></i> Settings</a></li>' +
      '<li><hr class="dropdown-divider"></li>' +
      '<li><a class="dropdown-item" href="audit-logs.html"><i class="bi bi-journal-text"></i> Audit Logs</a></li>' +
      '<li><hr class="dropdown-divider"></li>' +
      '<li><a class="dropdown-item text-danger" href="index.html"><i class="bi bi-box-arrow-left"></i> Sign Out</a></li>' +
      '</ul></div></div></header>';
  }

  function buildModals() {
    return '<div class="offcanvas offcanvas-end" tabindex="-1" id="filtersOffcanvas">' +
      '<div class="offcanvas-header border-bottom"><h5 class="offcanvas-title"><i class="bi bi-funnel me-2"></i>Advanced Filters</h5>' +
      '<button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button></div>' +
      '<div class="offcanvas-body" id="globalFiltersBody"><p class="text-secondary small">Use page-specific filters or apply global filters below.</p>' +
      '<div class="mb-3"><label class="form-label fw-semibold">Hotel</label><select class="form-select filter-field" data-filter="hotel"><option value="">All Hotels</option></select></div>' +
      '<div class="mb-3"><label class="form-label fw-semibold">Agency</label><select class="form-select filter-field" data-filter="agency"><option value="">All Agencies</option></select></div>' +
      '<div class="d-grid gap-2"><button type="button" class="btn btn-accent" data-bs-dismiss="offcanvas">Apply</button><button type="button" class="btn btn-outline-secondary" id="clearGlobalFilters">Clear</button></div></div></div>' +
      '<div class="modal fade" id="exportModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">' +
      '<div class="modal-header"><h5 class="modal-title"><i class="bi bi-download me-2"></i>Export Data</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>' +
      '<div class="modal-body"><div class="mb-3"><label class="form-label fw-semibold">Format</label><select class="form-select"><option>Excel (.xlsx)</option><option>CSV (.csv)</option><option>PDF</option></select></div></div>' +
      '<div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>' +
      '<button type="button" class="btn btn-accent" data-bs-dismiss="modal"><i class="bi bi-download me-1"></i>Export</button></div></div></div></div>' +
      '<div class="modal fade" id="deleteModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">' +
      '<div class="modal-header"><h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>' +
      '<div class="modal-body"><p>Are you sure you want to delete this record? This action cannot be undone.</p></div>' +
      '<div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>' +
      '<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Delete</button></div></div></div></div>';
  }

  window.HGBMS_LAYOUT = {
    init: function () {
      var root = document.getElementById('hgbms-root');
      var content = document.getElementById('page-content');
      if (!root || !content) return;

      var inner = content.innerHTML;
      root.innerHTML = buildSidebar() +
        '<div class="app-main">' + buildHeader() +
        '<main class="app-content">' + inner + '</main></div>';

      /* Modals/offcanvas must NOT be flex children of app-wrapper */
      if (!document.getElementById('exportModal')) {
        document.body.insertAdjacentHTML('beforeend', buildModals());
      }

      content.remove();

      /* Remove any orphaned tooltip nodes */
      document.querySelectorAll('body > .tooltip').forEach(function (el) { el.remove(); });

      if (typeof HGBMS_DATA !== 'undefined' && HGBMS_DATA.notifications) {
        var nl = document.getElementById('notificationList');
        if (nl) {
          nl.innerHTML = HGBMS_DATA.notifications.map(function (n) {
            return '<div class="notification-item' + (n.unread ? ' unread' : '') + '">' +
              '<div class="notification-item-title">' + n.title + '</div>' +
              '<div class="notification-item-text">' + n.text + '</div>' +
              '<div class="notification-item-time">' + n.time + '</div></div>';
          }).join('');
        }
      }

      setTimeout(function () {
        document.dispatchEvent(new CustomEvent('hgbms:layout-ready'));
      }, 0);
    }
  };

  document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('hgbms-root')) {
      HGBMS_LAYOUT.init();
    }
  });
})();
