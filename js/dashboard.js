// Toggle the embedded Top-Up section
document.addEventListener('DOMContentLoaded', () => {
  const dashBody  = document.querySelector('.body');       // your main dashboard body
  const topupBody = document.getElementById('topup-body');
  const openBtn   = document.getElementById('openTopup');
  const closeBtn  = document.getElementById('closeTopup');

  if (openBtn && dashBody && topupBody) {
    openBtn.addEventListener('click', (e) => {
      e.preventDefault();
      dashBody.style.display = 'none';
      topupBody.style.display = 'block';
      topupBody.classList.add('show');    // small slide animation
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }
  closeBtn?.addEventListener('click', () => {
    topupBody.style.display = 'none';
    dashBody.style.display = 'block';
  });

  // tap a method (stub — route later)
  document.querySelectorAll('.method-card').forEach(card => {
    card.addEventListener('click', e => {
      e.preventDefault();
      alert(`Top Up via ${card.dataset.method} (UI only)`);
    });
  });
});



document.addEventListener('DOMContentLoaded', () => {
  // --- Count-up animations ---
  const user = { name: 'Gerald', balance: 895500, points: 9500 }; // replace with your data source
  const balEl = document.getElementById('balance');
  const ptsEl = document.getElementById('points');
  if (balEl) countUp(balEl, user.balance, { duration: 1000, prefix: 'UGX ', decimals: 2 });
  if (ptsEl) countUp(ptsEl, user.points, { duration: 800 });

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

