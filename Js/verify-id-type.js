document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('idtype-form');
  const continueBtn = document.getElementById('continue');

  // Enable button once a choice is made
  form.addEventListener('change', () => {
    const selected = form.querySelector('input[name="doc_type"]:checked');
    continueBtn.toggleAttribute('disabled', !selected);
  });

  // Submit → route to the correct upload screen
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const chosen = form.querySelector('input[name="doc_type"]:checked')?.value;
    if (!chosen) return;

    // Route by choice (adjust paths to your flow)
    const routes = {
      national_id: 'upload-id-national.html',
      passport: 'upload-id-passport.html',
      drivers_license: 'upload-id-drivers.html',
    };
    location.href = routes[chosen] || 'verify-id-consent.html';
  });

  // Keyboard convenience on desktop
  form.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      continueBtn.click();
    }
  });
});
