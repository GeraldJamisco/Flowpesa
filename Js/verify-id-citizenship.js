// Route based on answer. Adjust targets to your next steps.
document.addEventListener('DOMContentLoaded', () => {
  const yes = document.getElementById('btn-yes');
  const no  = document.getElementById('btn-no');

  yes.addEventListener('click', () => {
    // citizen flow → pick ID type, e.g., National ID / Passport
    location.href = 'verify-id-consent.html';
  });

  no.addEventListener('click', () => {
    // non-citizen flow → residence permit / passport route
    location.href = 'verify-id-non-citizen.html';
  });

  // keyboard: Left/Right/Enter for accessibility on desktop
  document.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowLeft') no.focus();
    if (e.key === 'ArrowRight') yes.focus();
    if (e.key === 'Enter' && document.activeElement === no) no.click();
    if (e.key === 'Enter' && document.activeElement === yes) yes.click();
  });
});
