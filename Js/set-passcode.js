document.addEventListener('DOMContentLoaded', () => {
  const form          = document.getElementById('passcode-form');
  const dotsContainer = document.getElementById('dots');
  const clearKey      = document.getElementById('clear');
  const enterKey      = document.getElementById('enter');
  const digitKeys     = document.querySelectorAll('.key:not(.key-action)');
  const PASSCODE_LENGTH = 6;

  if (!dotsContainer) return;

  let passcode = '';

  // Build the 6 dots
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

  // When we have 6 digits, store and go to confirm page
  const handleComplete = () => {
    if (passcode.length !== PASSCODE_LENGTH) return;

    // Save first passcode for the next page
    sessionStorage.setItem('fp_first_passcode', passcode);

    // Go to confirm-passcode step
    window.location.href = 'confirm-passcode.php';
  };

  // On-screen keypad
  digitKeys.forEach((btn) => {
    btn.addEventListener('click', () => {
      const d = btn.textContent.trim();
      if (/^[0-9]$/.test(d)) pushDigit(d);
    });
  });

  if (clearKey) {
    clearKey.addEventListener('click', () => {
      popDigit();
    });
  }

  if (enterKey) {
    enterKey.addEventListener('click', (e) => {
      e.preventDefault();
      handleComplete();
    });
  }

  // Support physical keyboard too
  document.addEventListener('keydown', (e) => {
    if (/^[0-9]$/.test(e.key)) {
      e.preventDefault();
      pushDigit(e.key);
    } else if (e.key === 'Backspace') {
      e.preventDefault();
      popDigit();
    } else if (e.key === 'Enter') {
      e.preventDefault();
      handleComplete();
    }
  });

  // If there *is* a form, stop it from actually posting
  if (form) {
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      handleComplete();
    });
  }

  refreshDots();
});
