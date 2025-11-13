(() => {
  const form    = document.getElementById('signup-form');
  const phone   = document.getElementById('phone');
  const cc      = document.getElementById('country-code');
  const errorEl = document.getElementById('phone-error');
  const btn     = document.getElementById('continue-btn');
  const clearBtn= document.querySelector('.clear-btn');

  if (!form || !phone || !cc || !btn) return;

  const ugPattern  = /^0?\d{9}$/;   // UG local
  const intlDigits = /^\d{6,14}$/;  // generic

  function validate() {
    const raw = phone.value.replace(/\s+/g, '');
    const ok  = (cc.value === '+256') ? ugPattern.test(raw) : intlDigits.test(raw);

    btn.disabled = !ok;
    btn.classList.toggle('is-active', ok);

    if (errorEl) {
      // Don’t overwrite PHP error message; just hide when ok
      errorEl.hidden = ok;
    }

    if (clearBtn) {
      clearBtn.hidden = phone.value.length === 0;
    }
  }

  // Optional visual formatting (you can remove if it annoys you)
  function formatPhoneVisual(v) {
    const d = v.replace(/\D/g, '');
    return d.replace(/^(\d{2})(\d{0,3})(\d{0,2})(\d{0,2}).*$/, (_, a, b, c, dd) =>
      [a, b, c, dd].filter(Boolean).join(' ')
    );
  }

  phone.addEventListener('input', (e) => {
    const old = e.target.value;
    const formatted = formatPhoneVisual(old);
    if (formatted !== old) {
      e.target.value = formatted;
      // let browser decide caret; if jumpy you can comment this out
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
    validate();
    if (btn.disabled) {
      e.preventDefault(); // stop submission if invalid
    }
    // if valid => allow normal POST to register.php
  });

  window.addEventListener('DOMContentLoaded', () => {
    phone.focus();
    validate();
  });
})();
