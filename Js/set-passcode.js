document.addEventListener('DOMContentLoaded', () => {
  const dotsContainer = document.getElementById('dots');
  const clearKey = document.getElementById('clear');
  const enterKey = document.getElementById('enter');
  const keypadKeys = document.querySelectorAll('.key:not(.key-action)');
  const PASSCODE_LENGTH = 6;
  let passcode = "";

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
    sessionStorage.setItem('fp_first_passcode', passcode); // save first entry
    location.href = 'confirm-passcode.html';
  }
}


  // on-screen keypad
  keypadKeys.forEach(btn => btn.addEventListener('click', () => pushDigit(btn.textContent)));
  clearKey.addEventListener('click', popDigit);
  enterKey.addEventListener('click', submitIfComplete);

  // physical keyboard support (desktop/laptop)
  document.addEventListener('keydown', (e) => {
    if (/^[0-9]$/.test(e.key)) {
      e.preventDefault();
      pushDigit(e.key);
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
