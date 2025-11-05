// js/create-account.js
(() => {
  const form = document.getElementById('signup-form');
  const phone = document.getElementById('phone');
  const cc    = document.getElementById('country-code');
  const error = document.getElementById('phone-error');
  const btn   = document.getElementById('continue-btn');
  const clearBtn = document.querySelector('.clear-btn');

  // Guards if elements not found
  if (!form || !phone || !cc || !btn) return;

  const ugPattern   = /^0?\d{9}$/;    // UG local
  const intlDigits  = /^\d{6,14}$/;   // generic fallback

  function validate() {
    const raw = phone.value.replace(/\s+/g,'');
    const ok  = (cc.value === '+256') ? ugPattern.test(raw) : intlDigits.test(raw);

    btn.disabled = !ok;
    btn.classList.toggle('is-active', ok);

    if (error) {
      error.hidden = ok;
      if (!ok) error.textContent = 'Enter a valid mobile number.';
    }

    if (clearBtn) clearBtn.hidden = phone.value.length === 0;
  }

  // Optional pretty spacing for the phone input (visual only)
  function formatPhoneVisual(value) {
    const digits = value.replace(/\D/g,'');
    // Example groups for TR/UG style "55 999 88 77"
    return digits
      .replace(/^(\d{2})(\d{0,3})(\d{0,2})(\d{0,2}).*$/, (_,a,b,c,d) =>
        [a,b,c,d].filter(Boolean).join(' ')
      );
  }

  phone.addEventListener('input', (e) => {
    // visual formatting; remove if you don’t want spaces while typing
    const cursorEnd = e.target.selectionEnd;
    const old = e.target.value;
    const formatted = formatPhoneVisual(old);
    if (formatted !== old) {
      e.target.value = formatted;
      // let the browser place the caret; if it feels jumpy, skip this
      e.target.setSelectionRange(formatted.length, formatted.length);
    }
    validate();
  });

  cc.addEventListener('change', validate);

  if (clearBtn) {
    clearBtn.addEventListener('click', () => {
      phone.value = '';
      phone.focus();
      validate();
    });
  }

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    validate();
    if (btn.disabled) return;

    const msisdn = cc.value + phone.value.replace(/\s+/g,'');
    // navigate to OTP
    location.href = 'verify-phone.html?msisdn=' + encodeURIComponent(msisdn);
  });

  window.addEventListener('DOMContentLoaded', () => {
    phone.focus();
    validate();
  });
})();
