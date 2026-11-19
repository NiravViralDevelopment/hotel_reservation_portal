/**
 * Profile page client-side validation.
 * Shows messages on keyup / input / change / blur, and blocks submit if invalid.
 */
(function (window, document) {
  'use strict';

  var NAME_RE = /^[A-Za-z][A-Za-z\s'.-]*$/;
  var PHONE_RE = /^[0-9+\s()-]*$/;
  var DIGITS_RE = /\d/;

  function trim(value) {
    return String(value == null ? '' : value).trim();
  }

  function feedbackHost(el) {
    var wrap = el.closest('.password-field');
    return wrap ? wrap.parentElement : el.parentElement;
  }

  function ensureFeedback(el) {
    var host = feedbackHost(el);
    if (!host) return null;

    var feedback = host.querySelector(':scope > .js-client-error');
    if (feedback) return feedback;

    feedback = document.createElement('div');
    feedback.className = 'invalid-feedback js-client-error d-block';
    feedback.style.display = 'none';

    var wrap = el.closest('.password-field');
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

    var host = feedbackHost(el);
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
      setError(el, rules.maxMessage || ('Must be ' + rules.max + ' characters or fewer.'));
      return false;
    }

    if (rules.minLength != null && value.length < rules.minLength) {
      setError(el, rules.minLengthMessage || ('Must be at least ' + rules.minLength + ' characters.'));
      return false;
    }

    if (rules.nameOnly && (!NAME_RE.test(value) || DIGITS_RE.test(value))) {
      setError(el, rules.nameMessage || 'Name can only contain letters.');
      return false;
    }

    if (rules.phoneOnly) {
      var digits = value.replace(/\D/g, '');
      if (!PHONE_RE.test(value) || digits.length === 0) {
        setError(el, rules.phoneMessage || 'Phone can only contain numbers.');
        return false;
      }
      if (rules.minDigits != null && digits.length < rules.minDigits) {
        setError(el, rules.minDigitsMessage || ('Enter at least ' + rules.minDigits + ' digits.'));
        return false;
      }
      if (rules.maxDigits != null && digits.length > rules.maxDigits) {
        setError(el, rules.maxDigitsMessage || ('Phone must be ' + rules.maxDigits + ' digits or fewer.'));
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

  function profileRules() {
    return {
      name: {
        required: true,
        max: 30,
        nameOnly: true,
        requiredMessage: 'Name is required.',
        maxMessage: 'Name must be 30 characters or fewer.',
        nameMessage: 'Numbers are not allowed in name.'
      },
      phone: {
        phoneOnly: true,
        minDigits: 7,
        maxDigits: 15,
        phoneMessage: 'Phone can only contain numbers.',
        minDigitsMessage: 'Enter a valid phone number.',
        maxDigitsMessage: 'Phone must be 15 digits or fewer.'
      },
      job_title: { max: 255, maxMessage: 'Job title must be 255 characters or fewer.' }
    };
  }

  function passwordRules(form) {
    return {
      current_password: {
        required: true,
        requiredMessage: 'Current password is required.'
      },
      password: {
        required: true,
        minLength: 8,
        minLengthMessage: 'Password must be at least 8 characters.',
        requiredMessage: 'New password is required.'
      },
      password_confirmation: {
        required: true,
        matchField: 'password',
        matchMessage: 'Password confirmation does not match.',
        requiredMessage: 'Confirm the new password.'
      }
    };
  }

  function init(form, rulesMap) {
    if (!form || form.dataset.profileValidationInit === '1') return;
    form.dataset.profileValidationInit = '1';
    form.setAttribute('novalidate', 'novalidate');

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
          if (el.name === 'name') {
            var cleaned = el.value.replace(/[0-9]/g, '');
            if (el.value !== cleaned) el.value = cleaned;
          }

          if (el.name === 'phone') {
            var phoneCleaned = el.value.replace(/[^\d+\s()-]/g, '');
            if (el.value !== phoneCleaned) el.value = phoneCleaned;
          }

          runField(el);

          if (el.name === 'password') {
            var confirm = form.querySelector('[name="password_confirmation"]');
            if (confirm && trim(confirm.value)) runField(confirm);
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
    var profileForm = document.getElementById('profile-details-form');
    if (profileForm) init(profileForm, profileRules());

    var passwordForm = document.getElementById('profile-password-form');
    if (passwordForm) init(passwordForm, passwordRules(passwordForm));
  }

  window.ProfileValidation = { init: init, boot: boot };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})(window, document);
