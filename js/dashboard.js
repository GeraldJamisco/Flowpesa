// Simple Top-Up sheet toggle (no overlay)
document.addEventListener('DOMContentLoaded', () => {
  const topupBody = document.getElementById('topup-body');
  const openBtn   = document.getElementById('openTopup');
  const closeBtn  = document.getElementById('closeTopup');

  if (openBtn && topupBody) {
    openBtn.addEventListener('click', (e) => {
      e.preventDefault();
      topupBody.style.display = 'block';
      requestAnimationFrame(() => topupBody.classList.add('show'));
    });
  }  

  if (closeBtn && topupBody) {
    closeBtn.addEventListener('click', (e) => {
      e.preventDefault();
      topupBody.classList.remove('show');
      setTimeout(()=>{ topupBody.style.display = 'none'; }, 220);
    });
  }

  // Ensure real links inside sheet always navigate (defuse any stale handlers)
  document.querySelectorAll('.method-card[href]:not([href="#"])').forEach(link => {
    // Capture-phase handler: stop propagation so no other click handler intercepts
    link.addEventListener('click', (e) => {
      // allow default navigation; just prevent any other JS from hijacking
      // also let modifier/middle-click behave normally
      if (e.defaultPrevented) return;
      e.stopPropagation();
    }, true);
  });

  // Only attach stub behavior to placeholder buttons
  document.querySelectorAll('.method-card--btn').forEach(btn => {
    btn.addEventListener('click', e => {
      e.preventDefault();
      e.stopPropagation();
      alert(`Top Up via ${btn.dataset.method} (UI only)`);
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
