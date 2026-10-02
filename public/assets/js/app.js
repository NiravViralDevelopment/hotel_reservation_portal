/**
 * Hotel Group Booking Management System — Shared UI Logic
 * Minimal JS for layout, sidebar, and table interactions.
 * Backend-ready: replace dummy handlers with API calls later.
 */

(function () {
  'use strict';

  const STORAGE_KEY_THEME = 'hgbms_theme';
  const STORAGE_KEY_SIDEBAR = 'hgbms_sidebar_collapsed';

  /* ---- Theme (light only) ---- */
  function initTheme() {
    localStorage.removeItem(STORAGE_KEY_THEME);
    document.documentElement.setAttribute('data-theme', 'light');
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

  /* ---- Active Nav Link (Blade marks .active; keep for static HTML) ---- */
  function setActiveNav() {
    if (document.querySelector('.sidebar-link.active')) return;
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

  /* ---- Server sort without a full page reload ---- */
  function sortablePanels(root) {
    return Array.from((root || document).querySelectorAll('.card')).filter(function (card) {
      return card.querySelector('.sortable-link');
    });
  }

  function syncSortFields(card, fresh) {
    var form = card.querySelector('form');
    var newForm = fresh.querySelector('form');
    if (!form || !newForm) return;

    ['sort', 'dir'].forEach(function (name) {
      var next = newForm.querySelector('input[type="hidden"][name="' + name + '"]');
      var current = form.querySelector('input[type="hidden"][name="' + name + '"]');
      if (!next) return;
      if (!current) {
        current = document.createElement('input');
        current.type = 'hidden';
        current.name = name;
        form.appendChild(current);
      }
      current.value = next.value;
    });
  }

  function replaceSortedPanel(card, fresh) {
    var oldWrap = card.querySelector('.table-wrapper');
    var newWrap = fresh.querySelector('.table-wrapper');
    if (oldWrap && newWrap) {
      oldWrap.replaceWith(document.importNode(newWrap, true));
    }

    var oldFooter = card.querySelector('.table-footer');
    var newFooter = fresh.querySelector('.table-footer');
    if (oldFooter && newFooter) {
      oldFooter.replaceWith(document.importNode(newFooter, true));
    } else if (oldFooter && !newFooter) {
      oldFooter.remove();
    } else if (!oldFooter && newFooter) {
      card.appendChild(document.importNode(newFooter, true));
    }

    syncSortFields(card, fresh);
  }

  var liveSortRequest = 0;

  function loadSortedTable(url, card, push) {
    var panels = sortablePanels(document);
    var index = panels.indexOf(card);
    if (index < 0 || !url) {
      window.location.href = url;
      return;
    }

    var requestId = ++liveSortRequest;
    card.classList.add('is-table-loading');

    fetch(url, {
      credentials: 'same-origin',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'text/html'
      }
    }).then(function (res) {
      if (!res.ok) throw new Error('Sort request failed');
      return res.text();
    }).then(function (html) {
      if (requestId !== liveSortRequest) return;
      var doc = new DOMParser().parseFromString(html, 'text/html');
      var fresh = sortablePanels(doc)[index];
      if (!fresh) throw new Error('Sorted table missing');
      replaceSortedPanel(card, fresh);
      if (push) history.pushState({ liveTableSort: true }, '', url);
    }).catch(function () {
      window.location.href = url;
    }).finally(function () {
      if (requestId === liveSortRequest) {
        card.classList.remove('is-table-loading');
      }
    });
  }

  function initLiveTableSort() {
    document.addEventListener('click', function (e) {
      var link = e.target.closest && e.target.closest('a.sortable-link');
      if (!link) return;
      if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

      var card = link.closest('.card');
      if (!card || !link.href) return;

      e.preventDefault();
      loadSortedTable(link.href, card, true);
    });

    window.addEventListener('popstate', function () {
      var panels = sortablePanels(document);
      if (!panels.length) return;
      loadSortedTable(window.location.href, panels[0], false);
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

  /* ---- Password show/hide ---- */
  function initPasswordToggles() {
    // Auto-wrap any password input that does not already have an eye button
    document.querySelectorAll('input[type="password"]').forEach(function (input) {
      if (!input.id) {
        input.id = 'pwd_' + Math.random().toString(36).slice(2, 9);
      }

      if (input.closest('.password-field') && input.parentElement.querySelector('[data-password-toggle]')) {
        return;
      }

      if (document.querySelector('[data-password-toggle="' + input.id + '"]')) {
        return;
      }

      const wrap = document.createElement('div');
      wrap.className = 'password-field';
      input.parentNode.insertBefore(wrap, input);
      wrap.appendChild(input);

      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'password-toggle';
      btn.setAttribute('data-password-toggle', input.id);
      btn.setAttribute('aria-label', 'Show password');
      btn.setAttribute('title', 'Show password');
      btn.innerHTML = '<i class="bi bi-eye"></i>';
      wrap.appendChild(btn);
    });

    document.querySelectorAll('[data-password-toggle]').forEach(function (btn) {
      if (btn.dataset.bound === '1') return;
      btn.dataset.bound = '1';

      btn.addEventListener('click', function () {
        const inputId = btn.getAttribute('data-password-toggle');
        const input = document.getElementById(inputId);
        if (!input) return;

        const showing = input.type === 'text';
        input.type = showing ? 'password' : 'text';

        const icon = btn.querySelector('i');
        if (icon) {
          icon.className = showing ? 'bi bi-eye' : 'bi bi-eye-slash';
        }

        btn.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
        btn.setAttribute('title', showing ? 'Show password' : 'Hide password');
      });
    });
  }

  /* ---- Login Form (static HTML only; Laravel forms submit normally) ---- */
  function initLogin() {
    const form = document.getElementById('loginForm');
    if (!form || form.hasAttribute('data-laravel-auth')) return;

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      const email = form.querySelector('#loginEmail');
      const password = form.querySelector('#loginPassword');

      form.classList.add('was-validated');

      if (!email.value || !password.value) return;

      const btn = form.querySelector('[type="submit"]');
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

  /* ---- Sliding toaster ---- */
  function ensureToaster() {
    var el = document.getElementById('hgbmsToaster');
    if (el) return el;
    el = document.createElement('div');
    el.id = 'hgbmsToaster';
    el.className = 'hgbms-toaster';
    el.setAttribute('aria-live', 'polite');
    el.setAttribute('aria-atomic', 'true');
    document.body.appendChild(el);
    return el;
  }

  function toastMeta(type) {
    var map = {
      success: { title: 'Success', icon: 'bi-check-circle-fill' },
      error: { title: 'Error', icon: 'bi-x-circle-fill' },
      warning: { title: 'Warning', icon: 'bi-exclamation-triangle-fill' },
      info: { title: 'Info', icon: 'bi-info-circle-fill' }
    };
    return map[type] || map.info;
  }

  window.HGBMS = window.HGBMS || {};
  window.HGBMS.toast = function (message, type, options) {
    if (!message) return;
    type = type || 'success';
    options = options || {};
    var duration = typeof options.duration === 'number' ? options.duration : 4200;
    var meta = toastMeta(type);
    var toaster = ensureToaster();

    var toast = document.createElement('div');
    toast.className = 'hgbms-toast hgbms-toast-' + type;
    toast.setAttribute('role', 'status');
    toast.innerHTML =
      '<div class="hgbms-toast-icon"><i class="bi ' + meta.icon + '"></i></div>' +
      '<div class="hgbms-toast-body">' +
      '<div class="hgbms-toast-title">' + meta.title + '</div>' +
      '<div class="hgbms-toast-message"></div>' +
      '</div>' +
      '<button type="button" class="hgbms-toast-close" aria-label="Close"><i class="bi bi-x-lg"></i></button>';

    toast.querySelector('.hgbms-toast-message').textContent = message;
    toaster.appendChild(toast);

    requestAnimationFrame(function () {
      toast.classList.add('is-visible');
    });

    var hideTimer = null;
    function dismiss() {
      if (hideTimer) clearTimeout(hideTimer);
      toast.classList.remove('is-visible');
      toast.classList.add('is-hiding');
      setTimeout(function () {
        toast.remove();
      }, 320);
    }

    toast.querySelector('.hgbms-toast-close').addEventListener('click', dismiss);
    if (duration > 0) {
      hideTimer = setTimeout(dismiss, duration);
    }
  };

  function initFlashToasts() {
    var items = window.HGBMS_FLASH_TOASTS || [];
    items.forEach(function (item, index) {
      setTimeout(function () {
        window.HGBMS.toast(item.message, item.type || 'success');
      }, index * 120);
    });
  }

  /* ---- Select2 ---- */
  function sortSelectOptions($el) {
    const el = $el[0];
    if (!el || el.tagName !== 'SELECT') return;

    const selected = $el.val();
    const options = Array.from(el.options);
    const blanks = [];
    const values = [];

    options.forEach(function (opt) {
      if (opt.value === '' || opt.disabled) {
        blanks.push(opt);
      } else {
        values.push(opt);
      }
    });

    values.sort(function (a, b) {
      return a.text.trim().localeCompare(b.text.trim(), undefined, { sensitivity: 'base', numeric: true });
    });

    el.innerHTML = '';
    blanks.concat(values).forEach(function (opt) {
      el.appendChild(opt);
    });

    if (selected !== null && selected !== undefined) {
      $el.val(selected);
    }
  }

  function resolveSelect2Width($el) {
    const custom = $el.data('width');
    if (custom) return custom;

    const inToolbar = $el.closest('.table-toolbar, .header-actions, .page-header').length > 0;
    if (!inToolbar) return '100%';

    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    ctx.font = '14px Inter, system-ui, sans-serif';

    let maxText = 0;
    Array.from($el[0].options).forEach(function (opt) {
      maxText = Math.max(maxText, ctx.measureText(opt.text.trim()).width);
    });

    // padding for clear (x) + arrow + borders
    const px = Math.ceil(Math.min(Math.max(maxText + 72, 150), 380));
    $el.css('width', px + 'px');
    return 'style';
  }

  function initSelect2() {
    if (typeof jQuery === 'undefined' || typeof jQuery.fn.select2 === 'undefined') return;

    jQuery('select.select2').each(function () {
      const $el = jQuery(this);
      if ($el.hasClass('select2-hidden-accessible')) {
        $el.select2('destroy');
      }

      sortSelectOptions($el);

      const placeholder = $el.find('option[value=""]').first().text() || 'Select…';
      const width = resolveSelect2Width($el);

      $el.select2({
        theme: 'bootstrap-5',
        width: width === 'style' ? 'style' : width,
        dropdownAutoWidth: true,
        placeholder: placeholder,
        allowClear: $el.find('option[value=""]').length > 0,
        dropdownParent: $el.closest('.modal, .offcanvas').length
          ? $el.closest('.modal, .offcanvas')
          : jQuery(document.body),
        minimumResultsForSearch: 0,
      });
    });
  }

  function initDatePlaceholders() {
    document.querySelectorAll('.date-placeholder-wrap input[type="date"]').forEach(function (input) {
      var wrap = input.closest('.date-placeholder-wrap');
      if (!wrap) return;

      function sync() {
        wrap.classList.toggle('has-value', input.value !== '');
      }

      input.addEventListener('input', sync);
      input.addEventListener('change', sync);
      sync();
    });
  }

  /* ---- Init ---- */
  document.addEventListener('DOMContentLoaded', function () {
    initTheme();
    initLogin();
    initPasswordToggles();
    initNotifications();
    initFlashToasts();
    initSelect2();
    initDatePlaceholders();
    if (document.getElementById('hgbms-root')) {
      document.addEventListener('hgbms:layout-ready', function () {
        initSidebar();
        setActiveNav();
        initTooltips();
        initTableSort();
        initLiveTableSort();
        initRefreshButtons();
        initExportButtons();
        initSelect2();
      }, { once: true });
    } else {
      initSidebar();
      setActiveNav();
      initTooltips();
      initTableSort();
      initLiveTableSort();
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
