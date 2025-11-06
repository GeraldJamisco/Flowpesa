const form  = document.getElementById('email-form');
const input = document.getElementById('email');
const clear = document.querySelector('.clear-btn');
const error = document.getElementById('email-error');
const btn   = document.getElementById('email-continue');

// simple, robust email pattern
const emailRx = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/i;

function validate() {
  const val = input.value.trim();
  const ok  = emailRx.test(val);

  btn.disabled = !ok;
  btn.classList.toggle('is-active', ok);

  error.hidden = ok;
  if (!ok && val.length) error.textContent = 'Enter a valid email.';

  clear.hidden = val.length === 0;
}

input.addEventListener('input', validate);
clear.addEventListener('click', () => {
  input.value = '';
  input.focus();
  validate();
});

form.addEventListener('submit', (e) => {
  e.preventDefault();
  validate();
  if (btn.disabled) return;

  // TODO: Call your API to send an OTP or magic link
  // await fetch('/api/auth/email-begin', {...})

  // Go to next step (Verify ID)
  location.href = 'verify-id.html';
});

window.addEventListener('DOMContentLoaded', () => {
  input.focus();
  validate();
});
