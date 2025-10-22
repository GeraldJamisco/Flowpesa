// js/profile.js
document.addEventListener('DOMContentLoaded', () => {
  // Demo user (replace with /api/me later)
  const me = {
    name: 'Gerald',
    email: 'gerald@example.com',
    phone: '+256 700 000000',
    country: 'Uganda',
    tier: 0,
    kycPct: 35,
    avatar: '' // base64 or URL if you have one
  };

  const $ = (s, r=document)=>r.querySelector(s);
  // Fill header & details
  $('#pName').textContent = me.name;
  $('#pEmail').textContent = me.email;
  $('#dName').textContent = me.name;
  $('#dEmail').textContent = me.email;
  $('#dPhone').textContent = me.phone;
  $('#dCountry').textContent = me.country;
  $('#tierBadge').textContent = `Tier ${me.tier}`;
  $('#kycBar').style.width = `${me.kycPct}%`;
  $('#kycNote').textContent = me.kycPct < 100
    ? `KYC ${me.kycPct}% complete — finish to unlock higher limits.`
    : 'KYC complete — you are on the highest limits.';

  // Avatar preview
  const file = $('#avatarInput');
  const img = $('#avatarImg');
  if (me.avatar) img.src = me.avatar;
  file?.addEventListener('change', (e) => {
    const f = e.target.files?.[0];
    if (!f) return;
    const reader = new FileReader();
    reader.onload = () => { img.src = reader.result; };
    reader.readAsDataURL(f);
  });

  // Preferences (UI only)
  $('#prefDark')?.addEventListener('change', (e) => {
    document.documentElement.classList.toggle('theme-dark', e.target.checked);
    // In real app, persist to localStorage and CSS vars.
  });
  $('#prefNotify')?.addEventListener('change', (e) => {
    // UI-only for now
    console.log('Notifications:', e.target.checked ? 'on' : 'off');
  });

  // Actions
  $('#continueKyc')?.addEventListener('click', (e) => {
    e.preventDefault();
    alert('Open KYC flow (UI only for now).');
  });
  $('#editProfile')?.addEventListener('click', (e) => {
    e.preventDefault();
    alert('Open Edit Profile (UI only).');
  });
  $('#changePin')?.addEventListener('click', () => alert('Change PIN (UI only).'));
  $('#resetPwd')?.addEventListener('click', () => alert('Reset password (UI only).'));

  $('#logout')?.addEventListener('click', () => {
    // localStorage.removeItem('token');
    alert('Logged out (UI only). Redirecting to login…');
    location.href = 'login.html';
  });
});
