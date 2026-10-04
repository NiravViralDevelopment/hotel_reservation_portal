/**
 * Enquiry module client-side validation.
 * Shows messages immediately on keyup / input / change / blur, and blocks submit if invalid.
 */
(function (window, document) {
  'use strict';

  var EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  function trim(value) {
    return String(value == null ? '' : value).trim();
  }

  function ensureFeedback(el) {
    var wrap = el.closest('.date-placeholder-wrap');
    var host = wrap ? wrap.parentElement : el.parentElement;
    if (!host) return null;

    var feedback = host.querySelector(':scope > .js-client-error');
    if (feedback) return feedback;

    feedback = document.createElement('div');
    feedback.className = 'invalid-feedback js-client-error d-block';
    feedback.style.display = 'none';

    if (wrap) {
      wrap.insertAdjacentElement('afterend', feedback);
    } else {
      el.insertAdjacentElement('afterend', feedback);
    }

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
  }

  function clearError(el) {
    el.classList.remove('is-invalid');
    el.removeAttribute('aria-invalid');

    var wrap = el.closest('.date-placeholder-wrap');
    var host = wrap ? wrap.parentElement : el.parentElement;
    var feedback = host ? host.querySelector(':scope > .js-client-error') : null;
    if (feedback) {
      feedback.textContent = '';
      feedback.style.display = 'none';
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

    if (rules.integer) {
      if (!/^\d+$/.test(value)) {
        setError(el, 'Enter a whole number.');
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

    if (rules.decimal) {
      if (!/^\d+(\.\d+)?$/.test(value)) {
        setError(el, 'Enter a valid number.');
        return false;
      }
      var numVal = parseFloat(value);
      if (rules.min != null && numVal < rules.min) {
        setError(el, 'Must be at least ' + rules.min + '.');
        return false;
      }
      if (rules.maxNum != null && numVal > rules.maxNum) {
        setError(el, 'Must be ' + rules.maxNum + ' or less.');
        return false;
      }
    }

    if (rules.year) {
      if (!/^\d{4}$/.test(value)) {
        setError(el, 'Enter a 4-digit year.');
        return false;
      }
      var year = parseInt(value, 10);
      if (year < 2000 || year > 2100) {
        setError(el, 'Year must be between 2000 and 2100.');
        return false;
      }
    }

    if (rules.afterField) {
      var form = el.form;
      var other = form ? form.querySelector('[name="' + rules.afterField + '"]') : null;
      var otherVal = other ? trim(other.value) : '';
      if (otherVal && value <= otherVal) {
        setError(el, rules.afterMessage || 'Must be after the related date.');
        return false;
      }
    }

    if (rules.afterOrEqualField || rules.afterOrEqualAttr) {
      var afterForm = el.form;
      var afterOther = (rules.afterOrEqualField && afterForm)
        ? afterForm.querySelector('[name="' + rules.afterOrEqualField + '"]')
        : null;
      var afterOtherVal = afterOther ? trim(afterOther.value) : '';
      if (!afterOtherVal && rules.afterOrEqualAttr) {
        afterOtherVal = trim(el.getAttribute(rules.afterOrEqualAttr) || '');
      }
      if (afterOtherVal && value < afterOtherVal) {
        setError(el, rules.afterOrEqualMessage || 'Must be on or after the related date.');
        return false;
      }
    }

    clearError(el);
    return true;
  }

  function buildRules(form) {
    var isCreate = form.getAttribute('data-enquiry-create') === '1';

    return {
      group_name: { required: true, max: 255, requiredMessage: 'Group name is required.' },
      email: {
        required: isCreate,
        email: true,
        max: 255,
        requiredMessage: 'Email ID is required.'
      },
      ref: { max: 255 },
      year: { year: true },
      nights: {
        required: isCreate,
        integer: true,
        min: 1,
        requiredMessage: 'Nights is required.'
      },
      rooms_per_night: {
        required: isCreate,
        integer: true,
        min: 0,
        requiredMessage: 'Total room per night is required.'
      },
      single_rooms: { integer: true, min: 0 },
      double_rooms: { integer: true, min: 0 },
      triple_rooms: { integer: true, min: 0 },
      single_rate: { decimal: true, min: 0 },
      double_rate: { decimal: true, min: 0 },
      triple_rate: { decimal: true, min: 0 },
      total_revenue: { decimal: true, min: 0 },
      remarks: { max: 5000 },
      cxl_policy: { max: 255 },
      basis: { max: 10 },
      check_in: isCreate
        ? { required: true, requiredMessage: 'Arrival date is required.' }
        : {},
      check_out: {
        afterField: 'check_in',
        afterMessage: 'Check-out must be after check-in.'
      },
      tax_percentage: {
        required: function () {
          var hasTax = form.querySelector('#has_tax');
          return !!(hasTax && hasTax.checked);
        },
        decimal: true,
        min: 0,
        maxNum: 100,
        requiredMessage: 'Enter the tax percentage.'
      },
      cancellation_reason: {
        required: function () {
          var cancel = form.querySelector('#cancel_booking');
          return !!(cancel && cancel.checked);
        },
        max: 500,
        requiredMessage: 'Enter a cancellation reason.'
      },
      client_response: { required: true, max: 2000, requiredMessage: 'Enter the remark.' },
      enquiry_date: isCreate
        ? { required: true, requiredMessage: 'Enquiry date is required.' }
        : {},
      response_date: {
        required: isCreate || !!form.querySelector('[name="client_response"]'),
        requiredMessage: 'Response date is required.',
        afterOrEqualField: 'enquiry_date',
        afterOrEqualAttr: 'data-min-date',
        afterOrEqualMessage: 'Response date cannot be before enquiry date.'
      }
    };
  }

  function init(form) {
    if (!form || form.dataset.enquiryValidationInit === '1') return;
    form.dataset.enquiryValidationInit = '1';
    form.setAttribute('novalidate', 'novalidate');

    var rulesMap = buildRules(form);

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

    var cancelEl = form.querySelector('#cancel_booking');
    if (cancelEl) {
      cancelEl.addEventListener('change', function () {
        var reason = form.querySelector('#cancellation_reason');
        if (reason) runField(reason);
      });
    }

    var hasTaxEl = form.querySelector('#has_tax');
    if (hasTaxEl) {
      hasTaxEl.addEventListener('change', function () {
        var taxPct = form.querySelector('#tax_percentage');
        if (taxPct) runField(taxPct);
      });
    }

    var checkInEl = form.querySelector('#check_in');
    if (checkInEl) {
      checkInEl.addEventListener('change', function () {
        var checkOut = form.querySelector('#check_out');
        if (checkOut && checkOut.value) runField(checkOut);
      });
    }

    var enquiryDateEl = form.querySelector('#enquiry_date');
    var responseDateEl = form.querySelector('#response_date');
    function syncResponseDateMin() {
      if (!enquiryDateEl || !responseDateEl) return;
      if (enquiryDateEl.value) {
        responseDateEl.setAttribute('min', enquiryDateEl.value);
        responseDateEl.setAttribute('data-min-date', enquiryDateEl.value);
      } else {
        responseDateEl.removeAttribute('min');
        if (!responseDateEl.getAttribute('data-min-date-fixed')) {
          responseDateEl.removeAttribute('data-min-date');
        }
      }
      if (responseDateEl.value) runField(responseDateEl);
    }
    if (enquiryDateEl) {
      ['change', 'input'].forEach(function (evt) {
        enquiryDateEl.addEventListener(evt, syncResponseDateMin);
      });
      syncResponseDateMin();
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
    document.querySelectorAll('form.enquiry-form').forEach(init);
  }

  window.EnquiryValidation = { init: init, boot: boot };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})(window, document);
