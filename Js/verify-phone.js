(() => {
  const inputs = Array.from(document.querySelectorAll('.otp-input'));
  const btn    = document.getElementById('otp-continue-btn');
  const error  = document.getElementById('otp-error');

  if (!inputs.length || !btn) return;

  const isDigit = (v) => /^[0-9]$/.test(v);

  function checkComplete() {
    const code = inputs.map(i => i.value).join('');
    const ok   = code.length === inputs.length;

    btn.disabled = !ok;
    btn.classList.toggle('is-active', ok);

    // don’t touch PHP error if it already exists, just hide when ok
    if (error && !error.textContent) {
      error.hidden = ok;
    }
  }

  inputs[0].focus();

  inputs.forEach((inp, idx) => {
    // Only allow digits & auto-move to next
    inp.addEventListener('input', (e) => {
      let v = e.target.value.replace(/\D/g, '');
      e.target.value = v.slice(0, 1);
      if (v && idx < inputs.length - 1) {
        inputs[idx + 1].focus();
      }
      checkComplete();
    });

    // backspace → previous box
    inp.addEventListener('keydown', (e) => {
      if (e.key === 'Backspace' && !inp.value && idx > 0) {
        inputs[idx - 1].focus();
        inputs[idx - 1].value = '';
        e.preventDefault();
      }
    });

    // prevent non-digits on key input
    inp.addEventListener('beforeinput', (e) => {
      if (e.data && !isDigit(e.data)) {
        e.preventDefault();
      }
    });

    // paste whole code into any box
    inp.addEventListener('paste', (e) => {
      const txt = (e.clipboardData || window.clipboardData)
        .getData('text')
        .replace(/\D/g, '')
        .slice(0, inputs.length);

      if (!txt) return;
      e.preventDefault();

      inputs.forEach((i, j) => {
        i.value = txt[j] || '';
      });

      (txt.length < inputs.length ? inputs[txt.length] : inputs[inputs.length - 1]).focus();
      checkComplete();
    });
  });

  window.addEventListener('DOMContentLoaded', checkComplete);
})();
