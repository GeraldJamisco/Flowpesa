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
