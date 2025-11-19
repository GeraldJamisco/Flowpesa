// Js/verify-id-type.js
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('idtype-form');
  if (!form) return;

  const radios = form.querySelectorAll('input[name="doc_type"]');
  const btn = document.getElementById('continue');

  const updateState = () => {
    const checked = form.querySelector('input[name="doc_type"]:checked');
    const hasSelection = !!checked;

    if (btn) {
      btn.disabled = !hasSelection;
      btn.classList.toggle('is-active', hasSelection); // if your CSS uses this
    }
  };

  radios.forEach(radio => {
    radio.addEventListener('change', updateState);
  });

  // On page load, if something was pre-selected from PHP, reflect it
  updateState();

  // Optional: prevent submit if somehow nothing is chosen
  form.addEventListener('submit', (e) => {
    const checked = form.querySelector('input[name="doc_type"]:checked');
    if (!checked) {
      e.preventDefault();
    }
  });
});
