// js/dashboard.js (safe version)
document.addEventListener('DOMContentLoaded', () => {
  // Demo data (swap for /api/me.php later)
  const user = { name: 'Gerald', balance: 895500, points: 9500 };

  const $ = (s, r = document) => r.querySelector(s);

  const nameEl = $('#name');
  const balEl  = $('#balance');
  const ptsEl  = $('#points');

  if (nameEl) nameEl.textContent = user.name;
  if (balEl)  balEl.textContent  = `UGX ${user.balance.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
  if (ptsEl)  ptsEl.textContent  = user.points.toLocaleString();

  // Robust nav highlighter (works for dashboard.html or index.html)
  const path = location.pathname.split('/').pop() || 'index.html';
  document.querySelectorAll('.nav a').forEach(a => {
    const href = a.getAttribute('href') || '';
    const target = href.split('/').pop();
    if (target && path.endsWith(target)) {
      document.querySelectorAll('.nav a').forEach(x => x.classList.remove('active'));
      a.classList.add('active');
    }
  });
});
