/**
 * Hotels module client-side validation.
 * Shows messages on keyup / input / change / blur, and blocks submit if invalid.
 */
(function (window, document) {
  'use strict';

  var EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  function trim(value) {
    return String(value == null ? '' : value).trim();
  }

  function select2Container(el) {
    if (!el.classList.contains('select2')) return null;
    var next = el.nextElementSibling;
    if (next && next.classList.contains('select2-container')) return next;
    return null;
  }

  function ensureFeedback(el) {
    var host = el.parentElement;
    if (!host) return null;

    var feedback = host.querySelector(':scope > .js-client-error');
    var anchor = select2Container(el) || el;

    if (feedback) {
      if (feedback.previousElementSibling !== anchor) {
        anchor.insertAdjacentElement('afterend', feedback);
      }
      return feedback;
    }

    feedback = document.createElement('div');
    feedback.className = 'invalid-feedback js-client-error d-block';
    feedback.style.display = 'none';
    anchor.insertAdjacentElement('afterend', feedback);

    return feedback;
  }

  function setError(el, message) {
    el.classList.add('is-invalid');
    el.setAttribute('aria-invalid', 'true');

    var feedback = ensureFeedback(el);
    if (feedback) {
      feedback.textContent = message;
      feedback.style.display = 'block';
    }

    var container = select2Container(el);
    if (container) {
      var selection = container.querySelector('.select2-selection');
      if (selection) selection.classList.add('is-invalid');
    }
  }

  function clearError(el) {
    el.classList.remove('is-invalid');
    el.removeAttribute('aria-invalid');

    var feedback = el.parentElement
      ? el.parentElement.querySelector(':scope > .js-client-error')
      : null;
    if (feedback) {
      feedback.textContent = '';
      feedback.style.display = 'none';
    }

    var container = select2Container(el);
    if (container) {
      var selection = container.querySelector('.select2-selection');
      if (selection) selection.classList.remove('is-invalid');
    }
  }

  function pairExists(pairs, code, name) {
    var codeKey = String(code || '').trim().toLowerCase();
    var nameKey = String(name || '').trim().toLowerCase();
    if (!codeKey || !nameKey) return false;

    return pairs.some(function (pair) {
      return pair.code === codeKey && pair.name === nameKey;
    });
  }

  function validateField(el, rules, form, pairs) {
    if (!el || !rules) return true;

    var value = trim(el.value);
    var empty = value === '';
    var required = typeof rules.required === 'function' ? !!rules.required() : !!rules.required;

    if (required && empty) {
      setError(el, rules.requiredMessage || 'This field is required.');
      return false;
    }

    if (empty) {
      clearError(el);
      return true;
    }

    if (rules.max != null && value.length > rules.max) {
      setError(el, 'Must be ' + rules.max + ' characters or fewer.');
      return false;
    }

    if (rules.email && !EMAIL_RE.test(value)) {
      setError(el, 'Enter a valid email address.');
      return false;
    }

    if (rules.integer) {
      if (!/^\d+$/.test(value)) {
        setError(el, rules.integerMessage || 'Enter a whole number.');
        return false;
      }
      var intVal = parseInt(value, 10);
      if (rules.min != null && intVal < rules.min) {
        setError(el, 'Must be at least ' + rules.min + '.');
        return false;
      }
      if (rules.maxNum != null && intVal > rules.maxNum) {
        setError(el, 'Must be ' + rules.maxNum + ' or less.');
        return false;
      }
    }

    if (rules.uniquePair) {
      var codeEl = form.querySelector('[name="code"]');
      var nameEl = form.querySelector('[name="name"]');
      var codeVal = codeEl ? trim(codeEl.value) : '';
      var nameVal = nameEl ? trim(nameEl.value) : '';
      if (codeVal && nameVal && pairExists(pairs, codeVal, nameVal)) {
        setError(el, 'This code and name combination already exists.');
        return false;
      }
    }

    clearError(el);
    return true;
  }

  function buildRules() {
    return {
      code: { required: true, max: 20, uniquePair: true, requiredMessage: 'Hotel code is required.' },
      name: { required: true, max: 255, uniquePair: true, requiredMessage: 'Hotel name is required.' },
      company_id: { required: true, requiredMessage: 'Please select a company.' },
      city: { required: true, max: 255, requiredMessage: 'City is required.' },
      rooms: {
        required: true,
        integer: true,
        min: 0,
        maxNum: 99999,
        requiredMessage: 'Number of rooms is required.',
        integerMessage: 'Rooms must be a whole number.'
      },
      manager_name: { max: 255 },
      status: { required: true, requiredMessage: 'Status is required.' },
      phone: { required: true, max: 15, requiredMessage: 'Phone is required.' },
      email: { required: true, email: true, max: 255, requiredMessage: 'Email is required.' },
      notes: { max: 5000 }
    };
  }

  function init(form) {
    if (!form || form.dataset.hotelValidationInit === '1') return;
    form.dataset.hotelValidationInit = '1';
    form.setAttribute('novalidate', 'novalidate');

    var pairs = [];
    try {
      pairs = JSON.parse(form.getAttribute('data-existing-pairs') || '[]');
    } catch (e) {
      pairs = [];
    }

    var rulesMap = buildRules();

    function fields() {
      return Object.keys(rulesMap)
        .map(function (name) {
          return form.querySelector('[name="' + name + '"]');
        })
        .filter(Boolean);
    }

    function runField(el) {
      return validateField(el, rulesMap[el.getAttribute('name')], form, pairs);
    }

    function runCodeNamePair() {
      var codeEl = form.querySelector('[name="code"]');
      var nameEl = form.querySelector('[name="name"]');
      var codeOk = codeEl ? runField(codeEl) : true;
      var nameOk = nameEl ? runField(nameEl) : true;
      return codeOk && nameOk;
    }

    fields().forEach(function (el) {
      ['keyup', 'input', 'change', 'blur'].forEach(function (evt) {
        el.addEventListener(evt, function () {
          if (el.name === 'code' || el.name === 'name') {
            runCodeNamePair();
            return;
          }
          runField(el);
        });
      });
    });

    var rooms = form.querySelector('#rooms');
    if (rooms) {
      rooms.addEventListener('input', function () {
        var cleaned = rooms.value.replace(/\D+/g, '');
        if (rooms.value !== cleaned) {
          rooms.value = cleaned;
        }
      });
      rooms.addEventListener('keypress', function (e) {
        if (e.ctrlKey || e.metaKey || e.altKey || e.key.length > 1) return;
        if (!/\d/.test(e.key)) e.preventDefault();
      });
      rooms.addEventListener('paste', function (e) {
        e.preventDefault();
        var text = (e.clipboardData || window.clipboardData).getData('text') || '';
        var start = rooms.selectionStart || 0;
        var end = rooms.selectionEnd || 0;
        var digits = text.replace(/\D+/g, '');
        rooms.value = rooms.value.slice(0, start) + digits + rooms.value.slice(end);
        runField(rooms);
      });
    }

    if (window.jQuery) {
      window.jQuery(form).find('select.select2').on('change.select2', function () {
        runField(this);
      });
    }

    form.addEventListener('submit', function (e) {
      var firstInvalid = null;
      fields().forEach(function (el) {
        if (!runField(el) && !firstInvalid) firstInvalid = el;
      });
      if (firstInvalid) {
        e.preventDefault();
        firstInvalid.focus();
        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    });
  }

  function boot() {
    document.querySelectorAll('form.hotel-form').forEach(init);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }

  window.HotelValidation = { init: init, boot: boot };
})(window, document);
