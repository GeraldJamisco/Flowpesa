document.addEventListener('DOMContentLoaded', async () => {
  const $ = (s, r=document)=>r.querySelector(s);
  const wrap = $('#saccos');

  try {
    const res = await fetch('api/wallet.php', { credentials:'include' });
    if(!res.ok) throw new Error('401');
    const { ok, data } = await res.json();
    if(!ok) throw new Error('bad');

    // header balances
    $('#total').textContent     = `UGX ${data.total.toLocaleString()}`;
    $('#available').textContent = `UGX ${data.available.toLocaleString()}`;
    $('#locked').textContent    = `UGX ${data.locked.toLocaleString()}`;
    $('#savings').textContent   = `UGX ${data.savingsTotal.toLocaleString()}`;
    $('#mainWallet').textContent= `UGX ${data.mainWallet.toLocaleString()}`;
    $('#walletDelta').textContent= `+UGX ${data.walletDelta.toLocaleString()} today`;
    $('#savedMonth').textContent = `UGX ${data.savedMonth.toLocaleString()}`;
    $('#spentMonth').textContent = `UGX ${data.spentMonth.toLocaleString()}`;

    const totalMonth = data.savedMonth + data.spentMonth;
    $('#savedBar').style.width = `${(data.savedMonth/totalMonth*100).toFixed(0)}%`;
    $('#spentBar').style.width = `${(data.spentMonth/totalMonth*100).toFixed(0)}%`;

    // clear old sacco cards
    wrap.innerHTML = '';
    data.saccos.forEach(s => {
      const pc = Math.min(100, Math.round((s.balance / s.goal) * 100));
      wrap.insertAdjacentHTML('beforeend', `
        <div class="col-12">
          <div class="sacco-card">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <div class="name">${s.name}</div>
                <div class="meta">Member: ${s.member} · Goal: UGX ${s.goal.toLocaleString()}</div>
              </div>
              <div class="text-end">
                <div class="money">UGX ${s.balance.toLocaleString()}</div>
                <div class="text-success small">Saved this month: UGX ${s.monthlySave.toLocaleString()}</div>
              </div>
            </div>
            <div class="progress mt-2" role="progressbar">
              <div class="progress-bar" style="width:${pc}%"></div>
            </div>
          </div>
        </div>
      `);
    });

  } catch (err) {
    console.error('Wallet load failed', err);
    // location.href = 'login.php';
  }
});
