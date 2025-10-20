// js/auth.js

// Password eye toggle
const toggle = document.getElementById('togglePwd');
const pwd = document.getElementById('password');
if (toggle && pwd) {
  toggle.addEventListener('click', () => {
    pwd.type = pwd.type === 'password' ? 'text' : 'password';
  });
}

// Optional: login handler (wire to backend later)
/*
const loginBtn = document.getElementById('loginBtn');
const emailEl = document.getElementById('email');
loginBtn?.addEventListener('click', async () => {
  const email = emailEl.value.trim();
  const password = pwd.value;
  if (!email || !password) return alert('Please enter your email and password.');

  // const res = await fetch('/api/login.php', { ... });
  // handle response, save token, redirect to dashboard.html
});
*/
