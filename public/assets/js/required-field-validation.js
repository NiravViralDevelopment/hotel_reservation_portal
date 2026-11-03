/**
 * Instant required-field validation for forms that do not already
 * have a module validator. Same message style as the Hotels module:
 * invalid-feedback on keyup / input / change / blur, and on submit.
 */
(function (window, document) {
  'use strict';

  var EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  function trim(value) {
    return String(value == null ? '' : value).trim();
  }

  function cssEscape(value) {
    if (window.CSS && typeof window.CSS.escape === 'function') {
      return window.CSS.escape(value);
    }
    return String(value).replace(/["\\]/g, '\\$&');
  }

  function hasModuleValidation(form) {
    return form.id === 'statusMasterForm'
      || form.dataset.hotelValidationInit === '1'
      || form.dataset.companyValidationInit === '1'
      || form.dataset.userValidationInit === '1'
      || form.dataset.travelAgencyValidationInit === '1'
      || form.dataset.enquiryValidationInit === '1';
  }

  function associatedLabel(el) {
    var form = el.form;
    if (el.id && form) {
      var found = form.querySelector('label[for="' + cssEscape(el.id) + '"]');
      if (found) return found;
    }
    return el.closest('label');
  }

  function labelHasStar(el) {
    var label = associatedLabel(el);
    if (!label) return false;
    var star = label.querySelector('.text-danger');
    return !!(star && star.textContent.indexOf('*') !== -1);
  }

  function labelText(el) {
    var label = associatedLabel(el);
    if (!label) return '';
    var clone = label.cloneNode(true);
    clone.querySelectorAll('.text-danger, .field-auto-badge, i').forEach(function (node) {
      node.remove();
    });
    return clone.textContent.replace(/\*/g, '').replace(/\s+/g, ' ').trim();
  }

  function isCandidate(el) {
    if (!el || !el.form) return false;
    var tag = el.tagName;
    if (tag !== 'INPUT' && tag !== 'SELECT' && tag !== 'TEXTAREA') return false;
    var type = (el.getAttribute('type') || '').toLowerCase();
    if (type === 'hidden' || type === 'submit' || type === 'button' || type === 'reset' || type === 'image') {
      return false;
    }
    if (el.required || labelHasStar(el)) return true;
    return false;
  }

  function isInactive(el) {
    if (el.disabled) return true;
    return !!el.closest('[hidden]');
  }

  function isEmpty(el) {
    var type = (el.getAttribute('type') || '').toLowerCase();
    if (type === 'file') return !el.files || el.files.length === 0;
    if (type === 'checkbox' || type === 'radio') return !el.checked;
    return trim(el.value) === '';
  }

  function select2Container(el) {
    if (!el.classList || !el.classList.contains('select2')) return null;
    var next = el.nextElementSibling;
    if (next && next.classList.contains('select2-container')) return next;
    return null;
  }

  function anchorElement(el) {
    var wrap = el.closest('.password-field, .date-placeholder-wrap, .form-floating');
    if (wrap) return wrap;
    return select2Container(el) || el;
  }

  function fieldKey(el) {
    if (!el.dataset.requiredFeedbackKey) {
      var base = el.id || el.getAttribute('name') || 'field';
      el.dataset.requiredFeedbackKey = base + '-' + Math.random().toString(36).slice(2, 8);
    }
    return el.dataset.requiredFeedbackKey;
  }

  function directFeedback(root) {
    if (!root) return [];
    return Array.prototype.slice.call(root.querySelectorAll(':scope > .invalid-feedback'));
  }

  function findFeedback(el) {
    var key = el.dataset.requiredFeedbackKey;
    var parent = el.parentElement;
    var local = directFeedback(parent);
    var i;
    for (i = 0; i < local.length; i++) {
      if (key && local[i].getAttribute('data-for') === key) return local[i];
      if (local[i].classList.contains('js-client-error')) return local[i];
    }
    // Placeholder message already in the markup. Reuse it so Bootstrap
    // does not show that note and a second script note together.
    for (i = 0; i < local.length; i++) {
      if (!local[i].classList.contains('d-block')) return local[i];
    }

    var anchor = anchorElement(el);
    var host = anchor.parentElement;
    if (!host || host === parent) return null;
    var outside = directFeedback(host);
    for (i = 0; i < outside.length; i++) {
      if (key && outside[i].getAttribute('data-for') === key) return outside[i];
    }
    return null;
  }

  function hideExtraFeedback(el, keep) {
    function hide(nodes) {
      for (var i = 0; i < nodes.length; i++) {
        if (nodes[i] === keep) continue;
        if (nodes[i].classList.contains('d-block') && !nodes[i].classList.contains('js-client-error')) continue;
        nodes[i].style.display = 'none';
      }
    }
    hide(directFeedback(el.parentElement));
    var anchor = anchorElement(el);
    if (anchor.parentElement && anchor.parentElement !== el.parentElement) {
      hide(directFeedback(anchor.parentElement));
    }
  }

  function ensureFeedback(el) {
    var feedback = findFeedback(el);
    if (feedback) {
      feedback.classList.add('js-client-error');
      feedback.setAttribute('data-for', fieldKey(el));
      return feedback;
    }

    var anchor = anchorElement(el);
    var host = anchor.parentElement;
    if (!host) return null;

    feedback = document.createElement('div');
    feedback.className = 'invalid-feedback js-client-error d-block';
    feedback.style.display = 'none';
    feedback.setAttribute('data-for', fieldKey(el));
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
      hideExtraFeedback(el, feedback);
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

    var feedback = findFeedback(el);
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

  function requiredMessage(el) {
    var text = labelText(el);
    if (!text) return 'This field is required.';
    return text + ' is required.';
  }

  function validate(el) {
    if (!el || !isCandidate(el)) return true;

    if (isInactive(el)) {
      clearError(el);
      return true;
    }

    if (isEmpty(el)) {
      setError(el, requiredMessage(el));
      return false;
    }

    var value = trim(el.value);
    var type = (el.getAttribute('type') || '').toLowerCase();

    if ((type === 'email' || el.getAttribute('inputmode') === 'email') && !EMAIL_RE.test(value)) {
      setError(el, 'Enter a valid email address.');
      return false;
    }

    var minLength = parseInt(el.getAttribute('minlength') || '', 10);
    if (!isNaN(minLength) && minLength > 0 && value.length < minLength) {
      setError(el, 'Must be at least ' + minLength + ' characters.');
      return false;
    }

    if (el.validity && (el.validity.patternMismatch || el.validity.rangeUnderflow || el.validity.rangeOverflow || el.validity.stepMismatch || el.validity.badInput)) {
      setError(el, 'Enter a valid value.');
      return false;
    }

    clearError(el);
    return true;
  }

  function init(form) {
    if (!form || form.dataset.requiredFieldValidationInit === '1') return;
    if (hasModuleValidation(form)) return;

    var fields = Array.prototype.filter.call(form.elements, isCandidate);
    if (!fields.length) return;

    form.dataset.requiredFieldValidationInit = '1';
    form.setAttribute('novalidate', 'novalidate');

    fields.forEach(function (el) {
      ['keyup', 'input', 'change', 'blur'].forEach(function (evt) {
        el.addEventListener(evt, function () {
          validate(el);
        });
      });
    });

    if (window.jQuery) {
      window.jQuery(form).find('select.select2').each(function () {
        var select = this;
        if (!isCandidate(select)) return;
        window.jQuery(select).on('change.select2.requiredField', function () {
          validate(select);
        });
      });
    }

    form.addEventListener('submit', function (e) {
      var firstInvalid = null;
      fields.forEach(function (el) {
        if (!validate(el) && !firstInvalid) firstInvalid = el;
      });
      if (firstInvalid) {
        e.preventDefault();
        firstInvalid.focus();
        if (firstInvalid.scrollIntoView) {
          firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      }
    });
  }

  function boot() {
    document.querySelectorAll('form').forEach(init);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})(window, document);
