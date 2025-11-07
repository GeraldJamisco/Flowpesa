document.addEventListener('DOMContentLoaded', () => {
  const first = sessionStorage.getItem('fp_first_passcode');
  if (!first) {
    // user jumped here directly; go back to create
    location.replace('set-passcode.html');
    return;
  }

  const dots = document.getElementById('dots');
  const errorEl = document.getElementById('error');
  const clearKey = document.getElementById('clear');
  const enterKey = document.getElementById('enter');
  const PASSCODE_LENGTH = 6;
  let code = "";

  // render 6 dots
  for (let i = 0; i < PASSCODE_LENGTH; i++) {
    const d = document.createElement('div');
    d.className = 'dot';
    dots.appendChild(d);
  }
  const render = () => {
    [...dots.children].forEach((d,i) => d.classList.toggle('active', i < code.length));
  };

  const push = (d) => { if (code.length < PASSCODE_LENGTH) { code += d; render(); } };
  const pop  = () => { if (code) { code = code.slice(0,-1); render(); } };

  function finish() {
    if (code.length !== PASSCODE_LENGTH) return;
    if (code !== first) {
      // mismatch: show error + shake + reset entry
      errorEl.hidden = false;
      dots.classList.remove('shake'); // restart animation
      // trigger reflow to allow re-adding the class
      // eslint-disable-next-line no-unused-expressions
      dots.offsetHeight;
      dots.classList.add('shake');
      code = "";
      render();
      return;
    }
    // success — clear temp, proceed (e.g., to Verify ID)
    sessionStorage.removeItem('fp_first_passcode');
    // TODO: send finalized code securely to backend here
    location.href = 'verify-id-citizenship.html';
  }

  // on-screen keypad
  document.querySelectorAll('.key:not(.key-action)').forEach(k =>
    k.addEventListener('click', () => { errorEl.hidden = true; push(k.textContent); })
  );
  clearKey.addEventListener('click', () => { errorEl.hidden = true; pop(); });
  enterKey.addEventListener('click', finish);

  // hardware keyboard
  document.addEventListener('keydown', (e) => {
    if (/^[0-9]$/.test(e.key)) { e.preventDefault(); errorEl.hidden = true; push(e.key); }
    else if (e.key === 'Backspace') { e.preventDefault(); errorEl.hidden = true; pop(); }
    else if (e.key === 'Enter') { e.preventDefault(); finish(); }
  });

  render();
});
