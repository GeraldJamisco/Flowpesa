// confirm-passcode.js
document.addEventListener('DOMContentLoaded', () => {
  const form          = document.getElementById('passcode-form');
  const passcodeField = document.getElementById('passcode-field');
  const dotsContainer = document.getElementById('dots');
  const clearKey      = document.getElementById('clear');
  const enterKey      = document.getElementById('enter');
  const keypadKeys    = document.querySelectorAll('.key:not(.key-action)');

  if (!form || !passcodeField || !dotsContainer) return;

  const PASSCODE_LENGTH = 6;
  let passcode = "";
  let isSubmitting = false;

  // render 6 dots
  for (let i = 0; i < PASSCODE_LENGTH; i++) {
    const dot = document.createElement('div');
    dot.className = 'dot';
    dotsContainer.appendChild(dot);
  }

  function renderDots() {
    [...dotsContainer.children].forEach((d, i) => {
      d.classList.toggle('active', i < passcode.length);
    });
  }

  function pushDigit(d) {
    if (isSubmitting) return;
    if (passcode.length >= PASSCODE_LENGTH) return;
    passcode += d;
    renderDots();
  }

  function popDigit() {
    if (isSubmitting) return;
    if (!passcode.length) return;
    passcode = passcode.slice(0, -1);
    renderDots();
  }

  function submitIfComplete() {
    if (isSubmitting) return;
    if (passcode.length !== PASSCODE_LENGTH) return;

    isSubmitting = true;
    passcodeField.value = passcode;
    form.submit();
  }

  // on-screen keypad
  keypadKeys.forEach(btn => {
    btn.addEventListener('click', () => {
      const d = btn.textContent.trim();
      if (/^\d$/.test(d)) pushDigit(d);
      submitIfComplete();
    });
  });

  if (clearKey) {
    clearKey.addEventListener('click', () => {
      popDigit();
    });
  }

  if (enterKey) {
    enterKey.addEventListener('click', () => {
      submitIfComplete();
    });
  }

  // physical keyboard support
  document.addEventListener('keydown', (e) => {
    if (/^[0-9]$/.test(e.key)) {
      e.preventDefault();
      pushDigit(e.key);
      submitIfComplete();
    } else if (e.key === 'Backspace') {
      e.preventDefault();
      popDigit();
    } else if (e.key === 'Enter') {
      e.preventDefault();
      submitIfComplete();
    }
  });

  renderDots();
});
