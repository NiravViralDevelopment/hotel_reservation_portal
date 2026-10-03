/**
 * Users module client-side validation.
 * Shows messages on keyup / input / change / blur, and blocks submit if invalid.
 */
(function (window, document) {
  'use strict';

  var EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  function trim(value) {
    return String(value == null ? '' : value).trim();
  }

  function ensureFeedback(el) {
    var wrap = el.closest('.password-field');
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

    var wrap = el.closest('.password-field');
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

    if (rules.minLength != null && value.length < rules.minLength) {
      setError(el, rules.minLengthMessage || ('Must be at least ' + rules.minLength + ' characters.'));
      return false;
    }

    if (rules.email && !EMAIL_RE.test(value)) {
      setError(el, 'Enter a valid email address.');
      return false;
    }

    if (rules.existingEmails) {
      var normalized = value.toLowerCase();
      if (rules.existingEmails.indexOf(normalized) !== -1) {
        setError(el, 'This email is already used.');
        return false;
      }
    }

    if (rules.matchField) {
      var form = el.form;
      var other = form ? form.querySelector('[name="' + rules.matchField + '"]') : null;
      var otherVal = other ? String(other.value || '') : '';
      if (value !== otherVal) {
        setError(el, rules.matchMessage || 'Values do not match.');
        return false;
      }
    }

    clearError(el);
    return true;
  }

  function buildRules(form) {
    var existingEmails = [];
    try {
      existingEmails = JSON.parse(form.getAttribute('data-existing-emails') || '[]');
    } catch (e) {
      existingEmails = [];
    }

    var passwordRequired = form.getAttribute('data-password-required') === '1';

    return {
      name: { required: true, max: 255, requiredMessage: 'Name is required.' },
      email: {
        required: true,
        email: true,
        max: 255,
        existingEmails: existingEmails,
        requiredMessage: 'Email is required.'
      },
      phone: { max: 20 },
      job_title: { max: 255 },
      status: { required: true, requiredMessage: 'Status is required.' },
      password: {
        required: function () {
          if (passwordRequired) return true;
          var confirm = form.querySelector('#password_confirmation');
          return !!(confirm && trim(confirm.value));
        },
        minLength: 8,
        minLengthMessage: 'Password must be at least 8 characters.',
        requiredMessage: 'Password is required.'
      },
      password_confirmation: {
        required: function () {
          var password = form.querySelector('#password');
          return !!(password && trim(password.value));
        },
        matchField: 'password',
        matchMessage: 'Password confirmation does not match.',
        requiredMessage: 'Confirm the password.'
      }
    };
  }

  function init(form) {
    if (!form || form.dataset.userValidationInit === '1') return;
    form.dataset.userValidationInit = '1';
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
          if (el.id === 'password') {
            var confirm = form.querySelector('#password_confirmation');
            if (confirm && confirm.value) runField(confirm);
          }
        });
      });
    });

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
    document.querySelectorAll('form.user-form').forEach(init);
  }

  window.UserValidation = { init: init, boot: boot };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})(window, document);
