/**
 * Travel agencies module client-side validation.
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

  function validateField(el, rules) {
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

    clearError(el);
    return true;
  }

  function buildRules() {
    return {
      code: { required: true, max: 20, requiredMessage: 'Code is required.' },
      name: { required: true, max: 255, requiredMessage: 'Name is required.' },
      contact_name: { required: true, max: 255, requiredMessage: 'Contact name is required.' },
      email: { required: true, email: true, max: 255, requiredMessage: 'Email is required.' },
      phone: { required: true, max: 15, requiredMessage: 'Phone is required.' },
      city: { max: 255 },
      country: { max: 255 },
      status: { required: true, requiredMessage: 'Status is required.' }
    };
  }

  function init(form) {
    if (!form || form.dataset.travelAgencyValidationInit === '1') return;
    form.dataset.travelAgencyValidationInit = '1';
    form.setAttribute('novalidate', 'novalidate');

    var rulesMap = buildRules();

    function fields() {
      return Object.keys(rulesMap)
        .map(function (name) {
          return form.querySelector('[name="' + name + '"]');
        })
        .filter(Boolean);
    }

    function runField(el) {
      return validateField(el, rulesMap[el.getAttribute('name')]);
    }

    fields().forEach(function (el) {
      ['keyup', 'input', 'change', 'blur'].forEach(function (evt) {
        el.addEventListener(evt, function () {
          runField(el);
        });
      });
    });

    var phone = form.querySelector('[name="phone"]');
    if (phone) {
      phone.addEventListener('input', function () {
        var cleaned = phone.value.replace(/[^\d+\s()-]/g, '');
        if (cleaned.length > 15) cleaned = cleaned.slice(0, 15);
        if (phone.value !== cleaned) phone.value = cleaned;
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
    document.querySelectorAll('form.travel-agency-form').forEach(init);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }

  window.TravelAgencyValidation = { init: init, boot: boot };
})(window, document);
