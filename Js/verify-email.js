// Js/verify-email.js
(() => {
  const form    = document.getElementById('email-form');
  const input   = document.getElementById('email');
  const clear   = document.querySelector('.clear-btn');
  const error   = document.getElementById('email-error');
  const counter = document.querySelector('.counter[data-for="email"]');
  const btn     = document.getElementById('email-continue');

  if (!form || !input || !btn) return;

  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  function validate() {
    const value = input.value.trim();
    const len   = value.length;
    const ok    = emailPattern.test(value);

    if (counter) counter.textContent = `${len}/100`;

    btn.disabled = !ok;
    btn.classList.toggle('is-active', ok);

    if (error) {
      // if server already sent an error, keep it until user types something new
      if (len === 0) {
        error.hidden = true;
      } else {
        error.hidden = ok;
        if (!ok) {
          error.textContent = 'Enter a valid email address (e.g., name@domain.com).';
        }
      }
    }

    if (clear) clear.hidden = len === 0;
  }

  input.addEventListener('input', validate);

  if (clear) {
    clear.addEventListener('click', () => {
      input.value = '';
      input.focus();
      validate();
    });
  }

  // IMPORTANT: let the browser submit to send_email.php (no preventDefault here)
  form.addEventListener('submit', () => {
    // no JS handling, PHP does the work
  });

  window.addEventListener('DOMContentLoaded', validate);
})();
