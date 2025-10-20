// --- helpers ---
const $ = (s, r = document) => r.querySelector(s);
const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));

const chips = $$('.auth-stepper .auth-chip');
const step1 = $('#step1');
const step2 = $('#step2');
const step3 = $('#step3');
const step4 = $('#step4');

const emailEl = $('#email');
const phoneEl = $('#phone');
const pwd = $('#password');
const confirmPwd = $('#confirm');
const agree = $('#agree');
const otpTarget = $('#otpTarget');

function setStep(i) {
  chips.forEach((c, idx) => c.classList.toggle('auth-chip--active', idx === i));
  step1.style.display = i === 0 ? 'block' : 'none';
  step2.style.display = i === 1 ? 'block' : 'none';
  step3.style.display = i === 2 ? 'block' : 'none';
  step4.style.display = i === 3 ? 'block' : 'none';
}

// initial
setStep(0);

// step 1 -> step 2
$('#continue')?.addEventListener('click', () => {
  // basic UI validation
  const hasEmail = emailEl.value.trim().length > 0;
  const hasPhone = phoneEl.value.trim().length > 0;
  if (!hasEmail && !hasPhone) return alert('Enter email or phone');
  if (!pwd.value || pwd.value !== confirmPwd.value) return alert('Passwords do not match');
  if (!agree.checked) return alert('Please agree to the Terms & Privacy');

  otpTarget.textContent = hasEmail ? emailEl.value.trim() : phoneEl.value.trim();
  setStep(1);

  // Example backend call (uncomment and point to your API)
  // fetch('/api/register.php', { method:'POST', headers:{'Content-Type':'application/json'},
  //   body: JSON.stringify({ full_name: $('#fullName').value.trim(), email: hasEmail ? emailEl.value.trim() : null,
  //                          phone: hasPhone ? phoneEl.value.trim() : null, country: $('#country').value, password: pwd.value })
  // }).then(r=>r.json()).then(json => { /* handle OTP flow with json.user_id */ });
});

// step 2 -> step 3
$('#verify')?.addEventListener('click', () => {
  // Example OTP verification call goes here
  setStep(2);
});

// step 3 -> step 4
$('#pinNext')?.addEventListener('click', () => {
  // Example PIN save call goes here
  setStep(3);
});

// password eye toggles
$('#togglePwd')?.addEventListener('click', () => {
  pwd.type = pwd.type === 'password' ? 'text' : 'password';
});
$('#toggleConfirm')?.addEventListener('click', () => {
  confirmPwd.type = confirmPwd.type === 'password' ? 'text' : 'password';
});

// OTP auto-advance (step2 + step3 PIN)
$$('#step2 .otp input, #step3 .otp input').forEach((el) => {
  el.addEventListener('input', (e) => {
    if (e.target.value) e.target.nextElementSibling?.focus();
  });
  el.addEventListener('keydown', (e) => {
    if (e.key === 'Backspace' && !e.target.value) e.target.previousElementSibling?.focus();
  });
});
