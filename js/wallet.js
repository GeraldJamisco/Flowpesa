// public/js/wallet.js
document.addEventListener('DOMContentLoaded', async () => {
  const $ = (s, r=document)=>r.querySelector(s);

  // convenience formatter
  const UGX = n => `UGX ${Number(n).toLocaleString(undefined, { maximumFractionDigits: 0 })}`;

  try {
    // IMPORTANT: use a RELATIVE path so it works under /Flowpesa/ subfolder
    const res = await fetch('api/wallet.php', { credentials: 'include' });
    if (!res.ok) throw new Error('unauth_or_network');
    const { ok, data, error } = await res.json();
    if (!ok) throw new Error(error || 'bad_payload');

    // Header totals
    $('#total')     && ($('#total').textContent     = UGX(data.total));
    $('#available') && ($('#available').textContent = UGX(data.available));
    $('#locked')    && ($('#locked').textContent    = UGX(data.locked));
    $('#savings')   && ($('#savings').textContent   = UGX(data.savingsTotal));

    // Wallet summary
    $('#mainWallet')  && ($('#mainWallet').textContent  = UGX(data.mainWallet));
    $('#walletDelta') && ($('#walletDelta').textContent = `${data.walletDelta>0?'+':''}${UGX(data.walletDelta)} today`);

    // Spend vs Save
    $('#savedMonth') && ($('#savedMonth').textContent = UGX(data.savedMonth));
    $('#spentMonth') && ($('#spentMonth').textContent = UGX(data.spentMonth));
    const totalMonth = Math.max(1, data.savedMonth + data.spentMonth);
    $('#savedBar') && ($('#savedBar').style.width = `${Math.round((data.savedMonth/totalMonth)*100)}%`);
    $('#spentBar') && ($('#spentBar').style.width = `${Math.round((data.spentMonth/totalMonth)*100)}%`);

    // SACCO cards
    const wrap = document.getElementById('saccos');
    if (wrap) {
      wrap.innerHTML = '';
      data.saccos.forEach(s => {
        const pc = Math.min(100, Math.round((s.balance/s.goal) * 100));
        wrap.insertAdjacentHTML('beforeend', `
          <div class="col-12">
            <div class="sacco-card">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <div class="name">${s.name}</div>
                  <div class="meta">Member: ${s.member} · Goal: ${UGX(s.goal)}</div>
                </div>
                <div class="text-end">
                  <div class="money">${UGX(s.balance)}</div>
                  <div class="text-success small">Saved this month: ${UGX(s.monthlySave)}</div>
                </div>
              </div>
              <div class="progress mt-2" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="${pc}">
                <div class="progress-bar" style="width:${pc}%"></div>
              </div>
            </div>
          </div>
        `);
      });
    }
  } catch (err) {
    console.error('Wallet load failed:', err);
    // Optional: show a toast instead of hard redirect
    // location.href = 'login.php';
  }
});
