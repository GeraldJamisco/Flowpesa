document.addEventListener('DOMContentLoaded', () => {
  const dotsContainer = document.getElementById('dots');
  const error = document.getElementById('error');

  const clearKey = document.getElementById('clear');
  const enterKey = document.getElementById('enter');
  const keypadKeys = document.querySelectorAll('.key:not(.key-action)');

  const PASSCODE_LENGTH = 6;
  let passcode = "";

  // get first passcode from session
  const first = sessionStorage.getItem('fp_first_passcode');
  if (!first) {
    // user skipped first page
    location.href = "set-passcode.php";
    return;
  }

  // create 6 dots
  for (let i = 0; i < PASSCODE_LENGTH; i++) {
    const dot = document.createElement('div');
    dot.className = 'dot';
    dotsContainer.appendChild(dot);
  }

  function renderDots() {
    [...dotsContainer.children].forEach((d, i) =>
      d.classList.toggle('active', i < passcode.length)
    );
  }

  function pushDigit(d) {
    if (passcode.length >= PASSCODE_LENGTH) return;
    passcode += d;
    renderDots();
  }

  function popDigit() {
    if (!passcode.length) return;
    passcode = passcode.slice(0, -1);
    renderDots();
  }

  function submitIfComplete() {
    if (passcode.length === PASSCODE_LENGTH) {
      if (passcode === first) {
        // good!
        sessionStorage.removeItem('fp_first_passcode');

        // SEND TO BACKEND HERE (create-passcode.php)
        fetch("create-passcode.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ passcode })
        })
        .then(res => res.json())
        .then(d => {
          if (d.status === "ok") {
            location.href = "verify-id-type.php";
          } else {
            error.textContent = "Server error. Try again.";
            error.hidden = false;
          }
        });

      } else {
        // mismatch!
        error.hidden = false;
        passcode = "";
        renderDots();
      }
    }
  }

  // on-screen keypad
  keypadKeys.forEach(btn =>
    btn.addEventListener('click', () => pushDigit(btn.textContent))
  );

  clearKey.addEventListener('click', popDigit);
  enterKey.addEventListener('click', submitIfComplete);

  // keyboard support
  document.addEventListener('keydown', (e) => {
    if (/^[0-9]$/.test(e.key)) {
      e.preventDefault();
      pushDigit(e.key);
    }
    else if (e.key === 'Backspace') {
      e.preventDefault();
      popDigit();
    }
    else if (e.key === 'Enter') {
      e.preventDefault();
      submitIfComplete();
    }
  });

  renderDots();
});
