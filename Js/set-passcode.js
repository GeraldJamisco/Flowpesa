document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('passcode-form');
  const hiddenField = document.getElementById('passcode-field');
  const dotsContainer = document.getElementById('dots');
  const clearKey = document.getElementById('clear');
  const enterKey = document.getElementById('enter');
  const digitKeys = document.querySelectorAll('.key:not(.key-action)');
  const PASSCODE_LENGTH = 6;

  if (!form || !hiddenField || !dotsContainer) return;

  let passcode = '';

  for (let i = 0; i < PASSCODE_LENGTH; i += 1) {
    const dot = document.createElement('div');
    dot.className = 'dot';
    dotsContainer.appendChild(dot);
  }

  const refreshDots = () => {
    [...dotsContainer.children].forEach((dot, idx) => {
      dot.classList.toggle('active', idx < passcode.length);
    });
  };

  const pushDigit = (digit) => {
    if (passcode.length >= PASSCODE_LENGTH) return;
    passcode += digit;
    refreshDots();
  };

  const popDigit = () => {
    if (!passcode.length) return;
    passcode = passcode.slice(0, -1);
    refreshDots();
  };

  const syncAndSubmit = () => {
    if (passcode.length !== PASSCODE_LENGTH) {
      return false;
    }
    hiddenField.value = passcode;
    return true;
  };

  digitKeys.forEach((btn) => {
    btn.addEventListener('click', () => pushDigit(btn.textContent.trim()));
  });

  if (clearKey) {
    clearKey.addEventListener('click', popDigit);
  }

  if (enterKey) {
    enterKey.addEventListener('click', (e) => {
      if (!syncAndSubmit()) {
        e.preventDefault();
      }
    });
  }

  form.addEventListener('submit', (e) => {
    if (!syncAndSubmit()) {
      e.preventDefault();
    }
  });

  document.addEventListener('keydown', (e) => {
    if (/^[0-9]$/.test(e.key)) {
      e.preventDefault();
      pushDigit(e.key);
    } else if (e.key === 'Backspace') {
      e.preventDefault();
      popDigit();
    } else if (e.key === 'Enter') {
      if (!syncAndSubmit()) {
        e.preventDefault();
      }
    }
  });

  refreshDots();
});
