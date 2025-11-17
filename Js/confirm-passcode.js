// confirm-passcode.js
document.addEventListener('DOMContentLoaded', () => {
  const dotsContainer = document.getElementById('dots');
  const error        = document.getElementById('error');

  const clearKey   = document.getElementById('clear');
  const enterKey   = document.getElementById('enter');
  const keypadKeys = document.querySelectorAll('.key:not(.key-action)');

  const PASSCODE_LENGTH = 6;
  let passcode    = "";
  let isSubmitting = false;

  // 1) get first passcode from session (from set-passcode page)
  const first = sessionStorage.getItem('fp_first_passcode');
  if (!first) {
    // user somehow landed here without creating passcode first
    location.href = "set-passcode.php";
    return;
  }

  // 2) render 6 dots
  if (dotsContainer) {
    for (let i = 0; i < PASSCODE_LENGTH; i++) {
      const dot = document.createElement('div');
      dot.className = 'dot';
      dotsContainer.appendChild(dot);
    }
  }

  function renderDots() {
    if (!dotsContainer) return;
    [...dotsContainer.children].forEach((d, i) => {
      d.classList.toggle('active', i < passcode.length);
    });
  }

  function showError(msg) {
    if (!error) return;
    error.textContent = msg;
    error.hidden = false;
  }

  function clearError() {
    if (!error) return;
    error.hidden = true;
    error.textContent = "";
  }

  function pushDigit(d) {
    if (isSubmitting) return;
    if (passcode.length >= PASSCODE_LENGTH) return;
    passcode += d;
    clearError();
    renderDots();
  }

  function popDigit() {
    if (isSubmitting) return;
    if (!passcode.length) return;
    passcode = passcode.slice(0, -1);
    clearError();
    renderDots();
  }

  function submitIfComplete() {
    if (isSubmitting) return;
    if (passcode.length !== PASSCODE_LENGTH) return;

    // 3) check match with first passcode
    if (passcode !== first) {
      showError("Passcodes don’t match. Try again.");
      passcode = "";
      renderDots();
      return;
    }

    // 4) send to backend to be saved in DB
    isSubmitting = true;

    fetch("create-passcode.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ passcode })
    })
    .then(res => res.json().catch(() => ({})))
    .then(data => {
      isSubmitting = false;

      if (data.status === "ok") {
        // cleanup session key
        sessionStorage.removeItem('fp_first_passcode');
        // go to next step
        location.href = "verify-id-type.php";
      } else {
        const msg = data.message || "Server error. Please try again.";
        showError(msg);
        // allow user to re-enter in case something changed
        passcode = "";
        renderDots();
      }
    })
    .catch(() => {
      isSubmitting = false;
      showError("Network error. Check your connection and try again.");
    });
  }

  // 5) on-screen keypad
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

  // 6) physical keyboard support
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
