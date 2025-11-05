<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Flowpesa — Verify phone</title>
  <link rel="stylesheet" href="css/vars.css" />
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/auth-override.css" />
</head>
<body class="theme-dark">
  <header class="top-bar">
    <div class="top">
      <button class="icon-btn" type="button" onclick="history.back()" aria-label="Go back">
        <svg width="24" height="24" viewBox="0 0 24 24" aria-hidden="true">
          <path d="M15 18L9 12l6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
      <h1 class="top-title">Sign Up</h1>
      <span class="top-ghost" aria-hidden="true"></span>
    </div>
  </header>

  <main class="screen">
    <section class="intro">
      <h2 class="heading">6-digit code</h2>
      <p class="subheading" id="otp-help">Code sent to <span id="msisdn">+256 *** *** ***</span> unless you already have an account</p>
    </section>

    <form class="card" id="otp-form" novalidate>
      <div class="digit-code-input" role="group" aria-label="6-digit code">
        <input class="otp-input" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" aria-label="Digit 1">
        <input class="otp-input" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" aria-label="Digit 2">
        <input class="otp-input" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" aria-label="Digit 3">
        <input class="otp-input" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" aria-label="Digit 4">
        <input class="otp-input" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" aria-label="Digit 5">
        <input class="otp-input" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" aria-label="Digit 6">
      </div>

      <p class="error" id="otp-error" role="alert" hidden></p>

      <div class="stack-under">
        <p class="subheading"><span id="resend-text">Resend code in 00:30</span></p>
        <p class="subheading">Already have an account? <a class="link" href="/login">Log in</a></p>
      </div>

      <button id="verify-btn" class="regular-button" type="submit" disabled>
        <span class="button-label">Continue</span>
      </button>
    </form>
  </main>

  <script>
    // show the masked number from ?msisdn query
    const params = new URLSearchParams(location.search);
    const msisdn = params.get('msisdn') || '';
    const show = s => s.replace(/^(\+\d{1,3})\s?(\d{1,3}).*(\d{2})$/,'$1 $2••• ••$3');
    if (msisdn) document.getElementById('msisdn').textContent = show(msisdn);

    // OTP behavior
    const inputs = Array.from(document.querySelectorAll('.otp-input'));
    const btn = document.getElementById('verify-btn');
    const err = document.getElementById('otp-error');

    inputs[0].focus();

    function check() {
      const code = inputs.map(i => i.value).join('');
      const ok = code.length === inputs.length;
      btn.disabled = !ok;
      btn.classList.toggle('is-active', ok);
      if (!ok) err.hidden = true;
      return ok;
    }

    inputs.forEach((inp, i) => {
      inp.addEventListener('input', e => {
        e.target.value = e.target.value.replace(/\D/g,'').slice(0,1);
        if (e.target.value && i < inputs.length - 1) inputs[i+1].focus();
        check();
      });
      inp.addEventListener('keydown', e => {
        if (e.key === 'Backspace' && !inp.value && i > 0) {
          inputs[i-1].value = '';
          inputs[i-1].focus();
          e.preventDefault();
        }
      });
      inp.addEventListener('paste', e => {
        const t = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g,'').slice(0,6);
        if (!t) return;
        e.preventDefault();
        inputs.forEach((x,j)=> x.value = t[j] || '');
        inputs[Math.min(t.length,6)-1].focus();
        check();
      });
    });

    document.getElementById('otp-form').addEventListener('submit', e => {
      e.preventDefault();
      if (!check()) return;
      // TODO: verify OTP with backend; on success:
      // location.href = 'set-passcode.html';
      alert('OTP OK (wire backend here)');
    });

    // simple resend countdown
    const resendText = document.getElementById('resend-text');
    let s = 30;
    const timer = setInterval(() => {
      s -= 1;
      resendText.textContent = s > 0 ? `Resend code in 00:${String(s).padStart(2,'0')}` : 'Resend code';
      if (s <= 0) clearInterval(timer);
    }, 1000);
  </script>
</body>
</html>
