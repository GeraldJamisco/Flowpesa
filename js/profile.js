// js/profile.js
document.addEventListener('DOMContentLoaded', () => {
  // --- Load user (demo or from localStorage)
  const saved = JSON.parse(localStorage.getItem('me') || 'null');
  const me = saved || {
    name: 'Gerald',
    email: 'gerald@example.com',
    phone: '+256 700 000000',
    country: 'Uganda',
    tier: 0,
    kycPct: 35,
    avatar: ''
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
    const f = e.target.files?.[0]; if (!f) return;
    const reader = new FileReader();
    reader.onload = () => { img.src = reader.result; me.avatar = reader.result; localStorage.setItem('me', JSON.stringify(me)); };
    reader.readAsDataURL(f);
  });

  // Preferences (UI only)
  $('#prefDark')?.addEventListener('change', (e) => {
    document.documentElement.classList.toggle('theme-dark', e.target.checked);
    // Persist if you want:
    // localStorage.setItem('prefDark', JSON.stringify(e.target.checked));
  });
  $('#prefNotify')?.addEventListener('change', (e) => {
    console.log('Notifications:', e.target.checked ? 'on' : 'off');
  });

  // --- Edit Profile Modal
  const modal = $('#editModal');
  const form  = $('#editForm');
  const open  = $('#editProfile');
  const closers = modal?.querySelectorAll('[data-close]');

  function openModal(){
    if (!modal) return;
    // prefill fields
    $('#fName').value    = me.name;
    $('#fEmail').value   = me.email;
    $('#fPhone').value   = me.phone;
    $('#fCountry').value = me.country;
    modal.hidden = false;
    requestAnimationFrame(() => modal.classList.add('show'));
  }
  function closeModal(){
    if (!modal) return;
    modal.classList.remove('show');
    setTimeout(() => { modal.hidden = true; }, 200);
  }

  open?.addEventListener('click', (e)=>{ e.preventDefault(); openModal(); });
  closers?.forEach(btn => btn.addEventListener('click', closeModal));
  document.addEventListener('keydown', (e)=>{ if(e.key === 'Escape' && !modal.hidden) closeModal(); });

  form?.addEventListener('submit', (e) => {
    e.preventDefault();
    // basic validation
    const name = $('#fName').value.trim();
    const email = $('#fEmail').value.trim();
    const phone = $('#fPhone').value.trim();
    const country = $('#fCountry').value;

    if (!name || !email || !phone) {
      alert('Please fill all required fields.');
      return;
    }

    // update data
    me.name = name; me.email = email; me.phone = phone; me.country = country;
    // reflect in UI
    $('#pName').textContent = me.name;
    $('#pEmail').textContent = me.email;
    $('#dName').textContent = me.name;
    $('#dEmail').textContent = me.email;
    $('#dPhone').textContent = me.phone;
    $('#dCountry').textContent = me.country;

    // persist locally (optional)
    localStorage.setItem('me', JSON.stringify(me));

    closeModal();
  });

  // Actions
  $('#continueKyc')?.addEventListener('click', (e) => {
    e.preventDefault();
    alert('Open KYC flow (UI only for now).');
  });
  $('#changePin')?.addEventListener('click', () => alert('Change PIN (UI only).'));
  $('#resetPwd')?.addEventListener('click', () => alert('Reset password (UI only).'));
  $('#logout')?.addEventListener('click', () => {
    // localStorage.removeItem('token');
    alert('Logged out (UI only). Redirecting to login…');
    location.href = 'login.php';
  });
});
