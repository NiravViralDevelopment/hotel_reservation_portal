/**
 * Hotel Group Booking Management System — Shared UI Logic
 * Minimal JS for layout, theme, sidebar, and table interactions.
 * Backend-ready: replace dummy handlers with API calls later.
 */

(function () {
  'use strict';

  const STORAGE_KEY_THEME = 'hgbms_theme';
  const STORAGE_KEY_SIDEBAR = 'hgbms_sidebar_collapsed';

  /* ---- Theme Toggle ---- */
  function initTheme() {
    const saved = localStorage.getItem(STORAGE_KEY_THEME);
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const theme = saved || (prefersDark ? 'dark' : 'light');
    document.documentElement.setAttribute('data-theme', theme);
    updateThemeIcon(theme);
  }

  function toggleTheme() {
    const current = document.documentElement.getAttribute('data-theme') || 'light';
    const next = current === 'light' ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem(STORAGE_KEY_THEME, next);
    updateThemeIcon(next);
  }

  function updateThemeIcon(theme) {
    document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
      const icon = btn.querySelector('i');
      if (icon) {
        icon.className = theme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
      }
    });
  }

  /* ---- Sidebar ---- */
  function initSidebar() {
    const sidebar = document.getElementById('appSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (!sidebar) return;

    function isMobile() {
      return window.innerWidth < 992;
    }

    function closeMobileSidebar() {
      sidebar.classList.remove('mobile-open');
      if (overlay) overlay.classList.remove('show');
      document.body.classList.remove('sidebar-mobile-open');
    }

    function applySidebarMode() {
      if (isMobile()) {
        sidebar.classList.remove('collapsed');
        closeMobileSidebar();
      } else {
        const collapsed = localStorage.getItem(STORAGE_KEY_SIDEBAR) === 'true';
        sidebar.classList.toggle('collapsed', collapsed);
        closeMobileSidebar();
      }
    }

    applySidebarMode();

    document.querySelectorAll('[data-sidebar-toggle]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        if (isMobile()) {
          const opening = !sidebar.classList.contains('mobile-open');
          sidebar.classList.toggle('mobile-open');
          if (overlay) overlay.classList.toggle('show');
          document.body.classList.toggle('sidebar-mobile-open', opening);
        } else {
          sidebar.classList.toggle('collapsed');
          localStorage.setItem(
            STORAGE_KEY_SIDEBAR,
            sidebar.classList.contains('collapsed')
          );
        }
      });
    });

    sidebar.querySelectorAll('.sidebar-link').forEach(function (link) {
      link.addEventListener('click', function () {
        if (isMobile()) closeMobileSidebar();
      });
    });

    if (overlay) {
      overlay.addEventListener('click', closeMobileSidebar);
    }

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeMobileSidebar();
    });

    window.addEventListener('resize', function () {
      applySidebarMode();
    });
  }

  /* ---- Active Nav Link ---- */
  function setActiveNav() {
    const path = window.location.pathname.split('/').pop() || 'dashboard.html';
    document.querySelectorAll('.sidebar-link').forEach(function (link) {
      const href = link.getAttribute('href');
      if (href && href.split('/').pop() === path) {
        link.classList.add('active');
      }
    });
  }

  /* ---- Tooltips (never on sidebar links) ---- */
  function initTooltips() {
    if (typeof bootstrap === 'undefined' || !bootstrap.Tooltip) return;
    document.querySelectorAll('body > .tooltip').forEach(function (el) { el.remove(); });
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
      if (el.closest('.app-sidebar')) return;
      bootstrap.Tooltip.getOrCreateInstance(el);
    });
  }

  /* ---- Table Sort (client-side dummy) ---- */
  function initTableSort() {
    document.querySelectorAll('table[data-sortable]').forEach(function (table) {
      table.querySelectorAll('thead th[data-sort]').forEach(function (th) {
        th.addEventListener('click', function () {
          const col = th.cellIndex;
          const tbody = table.querySelector('tbody');
          const rows = Array.from(tbody.querySelectorAll('tr'));
          const dir = th.dataset.sortDir === 'asc' ? 'desc' : 'asc';

          table.querySelectorAll('thead th[data-sort]').forEach(function (h) {
            delete h.dataset.sortDir;
            h.querySelector('.sort-icon')?.remove();
          });

          th.dataset.sortDir = dir;
          const icon = document.createElement('i');
          icon.className = 'bi bi-chevron-' + (dir === 'asc' ? 'up' : 'down') + ' sort-icon ms-1';
          icon.style.fontSize = '0.65rem';
          th.appendChild(icon);

          rows.sort(function (a, b) {
            const aVal = a.cells[col]?.textContent.trim() || '';
            const bVal = b.cells[col]?.textContent.trim() || '';
            const aNum = parseFloat(aVal.replace(/[^0-9.-]/g, ''));
            const bNum = parseFloat(bVal.replace(/[^0-9.-]/g, ''));
            if (!isNaN(aNum) && !isNaN(bNum)) {
              return dir === 'asc' ? aNum - bNum : bNum - aNum;
            }
            return dir === 'asc'
              ? aVal.localeCompare(bVal)
              : bVal.localeCompare(aVal);
          });

          rows.forEach(function (row) {
            tbody.appendChild(row);
          });
        });
      });
    });
  }

  /* ---- Refresh Button (UI only) ---- */
  function initRefreshButtons() {
    document.querySelectorAll('[data-refresh]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        const icon = btn.querySelector('i');
        if (icon) {
          icon.classList.add('spin-animation');
          setTimeout(function () {
            icon.classList.remove('spin-animation');
          }, 800);
        }
      });
    });
  }

  /* ---- Export Button (UI only) ---- */
  function initExportButtons() {
    document.querySelectorAll('[data-export]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        const modal = document.getElementById('exportModal');
        if (modal && typeof bootstrap !== 'undefined') {
          new bootstrap.Modal(modal).show();
        }
      });
    });
  }

  /* ---- Login Form ---- */
  function initLogin() {
    const form = document.getElementById('loginForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      const email = form.querySelector('#loginEmail');
      const password = form.querySelector('#loginPassword');

      form.classList.add('was-validated');

      if (!email.value || !password.value) return;

      const btn = form.querySelector('[type="submit"]');
      const originalText = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Signing in…';

      setTimeout(function () {
        window.location.href = 'dashboard.html';
      }, 1200);
    });
  }

  /* ---- Mark notifications read ---- */
  function initNotifications() {
    document.querySelectorAll('.notification-item').forEach(function (item) {
      item.addEventListener('click', function () {
        item.classList.remove('unread');
      });
    });
  }

  /* ---- Init ---- */
  document.addEventListener('DOMContentLoaded', function () {
    initTheme();
    initLogin();
    initNotifications();
    document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
      btn.addEventListener('click', toggleTheme);
    });
    if (document.getElementById('hgbms-root')) {
      document.addEventListener('hgbms:layout-ready', function () {
        initSidebar();
        setActiveNav();
        initTooltips();
        initTableSort();
        initRefreshButtons();
        initExportButtons();
      }, { once: true });
    } else {
      initSidebar();
      setActiveNav();
      initTooltips();
      initTableSort();
      initRefreshButtons();
      initExportButtons();
    }
  });
})();

/* Spin animation for refresh */
(function () {
  const style = document.createElement('style');
  style.textContent =
    '@keyframes hgbms-spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }' +
    '.spin-animation { animation: hgbms-spin 0.8s linear; display: inline-block; }';
  document.head.appendChild(style);
})();
