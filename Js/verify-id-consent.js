document.addEventListener('DOMContentLoaded', () => {
  const accept = document.getElementById('accept-btn');
  const decline = document.getElementById('decline-btn');

  accept.addEventListener('click', () => {
    // Route to your “select document type” screen
    location.href = 'verify-id-type.html';
  });

  decline.addEventListener('click', () => {
    // If they decline, you can take them back or show info
    // For now, return to previous page/home.
    history.back();
  });
});
