document.addEventListener('DOMContentLoaded', () => {
  // --- Enhanced Top-Up sheet toggle with overlay ---
  const dashBody  = document.getElementById('dashboard-body') || document.querySelector('.body');
  const topupBody = document.getElementById('topup-body');
  const openBtn   = document.getElementById('openTopup') || document.querySelector('.a-btn i.bi-arrow-left-right')?.closest('.a-btn');
  const closeBtn  = document.getElementById('closeTopup');

  // create overlay once
  let overlay = document.querySelector('.sheet-overlay');
  if (!overlay) {
    overlay = document.createElement('div');
    overlay.className = 'sheet-overlay';
    document.body.appendChild(overlay);
  }

  function openTopup() {
    if (!dashBody || !topupBody) return;
    dashBody.style.display = 'none';
    topupBody.style.display = 'block';
    // trigger transition
    requestAnimationFrame(() => {
      topupBody.classList.add('show');
      overlay.classList.add('show');
    });
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  function closeTopup() {
    if (!dashBody || !topupBody) return;
    topupBody.classList.remove('show');
    overlay.classList.remove('show');
    // wait for transition end then hide
    setTimeout(() => {
      topupBody.style.display = 'none';
      dashBody.style.display = 'block';
    }, 200);
  }

  openBtn?.addEventListener('click', (e) => { e.preventDefault(); openTopup(); });
  closeBtn?.addEventListener('click', closeTopup);
  overlay?.addEventListener('click', closeTopup);

  // (optional) tap a method (stub)
  document.querySelectorAll('.method-card').forEach(card => {
    card.addEventListener('click', e => {
      e.preventDefault();
      alert(`Top Up via ${card.dataset.method} (UI only)`);
    });
  });
});



const $ = (s, r=document)=>r.querySelector(s);

async function loadDashboard(){
  try{
    // Use relative path so it works under subfolders like /Flowpesa/
    const res = await fetch('api/me.php', { credentials: 'include' });
    if(!res.ok) throw new Error('Not logged in');
    const { ok, data, error } = await res.json();
    if(!ok) throw new Error(error || 'Failed to load');

    $('#name').textContent = data.name;
    const balEl = $('#balance');
    const ptsEl = $('#points');
    if (typeof countUp === 'function') {
      countUp(balEl, data.balance, { prefix:'UGX ', decimals:2, duration:900 });
      countUp(ptsEl,  data.points,  { duration:700 });
    } else {
      balEl.textContent = `UGX ${Number(data.balance).toLocaleString(undefined,{minimumFractionDigits:2})}`;
      ptsEl.textContent  = Number(data.points).toLocaleString();
    }
  } catch (e){
    // console.warn('Dashboard load failed:', e);
    console.error(e);
    location.href = 'login.php';
  }
}
document.addEventListener('DOMContentLoaded', loadDashboard);

