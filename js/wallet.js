// js/wallet.js
document.addEventListener('DOMContentLoaded', () => {
  // Demo data — swap for /api/wallet later
  const demo = {
    total: 2895500,
    available: 1850000,
    locked: 250000,
    savingsTotal: 795500,
    mainWallet: 1850000,
    walletDelta: 35000,
    savedMonth: 320000,
    spentMonth: 210000,
    saccos: [
      { name: 'Kampala Traders SACCO', member: 'KT-00921', balance: 350000, goal: 1000000, last: 'Jun 18', monthlySave: 120000 },
      { name: 'Mpigi Farmers SACCO',   member: 'MF-10234', balance: 285500, goal: 800000,  last: 'Jun 20', monthlySave: 90000  },
      { name: 'Youth Builders SACCO',   member: 'YB-55812', balance: 160000, goal: 500000,  last: 'Jun 16', monthlySave: 110000 }
    ]
  };

  const $  = (s, r=document) => r.querySelector(s);
  const $$ = (s, r=document) => Array.from(r.querySelectorAll(s));
  const UGX = v => `UGX ${Number(v||0).toLocaleString()}`;

  // --- header totals (replace skeletons by setting textContent)
  const setTxt = (id, val) => { const el = $(`#${id}`); if (el) el.textContent = val; };

  setTxt('total',      UGX(demo.total));
  setTxt('available',  UGX(demo.available));
  setTxt('locked',     UGX(demo.locked));
  setTxt('savings',    UGX(demo.savingsTotal));
  setTxt('mainWallet', UGX(demo.mainWallet));

  const deltaEl = $('#walletDelta');
  if (deltaEl) {
    const sign = demo.walletDelta > 0 ? '+' : '';
    deltaEl.textContent = `${sign}${UGX(demo.walletDelta)} today`;
    deltaEl.classList.toggle('text-success', demo.walletDelta >= 0);
    deltaEl.classList.toggle('text-danger', demo.walletDelta < 0);
  }

  // --- month summary + bars
  setTxt('savedMonth', UGX(demo.savedMonth));
  setTxt('spentMonth', UGX(demo.spentMonth));
  const totalMonth = Math.max(1, demo.savedMonth + demo.spentMonth);
  const savedPct = Math.round((demo.savedMonth / totalMonth) * 100);
  const spentPct = Math.round((demo.spentMonth / totalMonth) * 100);
  const savedBar = $('#savedBar');
  const spentBar = $('#spentBar');
  if (savedBar) savedBar.style.width = `${savedPct}%`;
  if (spentBar) spentBar.style.width = `${spentPct}%`;

  // --- render SACCO cards
  const wrap = $('#saccos');
  if (wrap) {
    wrap.innerHTML = '';
    demo.saccos.forEach(s => {
      const pc = Math.min(100, Math.round((Number(s.balance||0) / Math.max(1, Number(s.goal||0))) * 100));
      const col = document.createElement('div');
      col.className = 'col-12';
      col.innerHTML = `
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
          <div class="progress mt-2" role="progressbar" aria-label="Goal progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="${pc}">
            <div class="progress-bar" style="width:${pc}%"></div>
          </div>
          <div class="d-flex gap-2 mt-2">
            <button class="btn btn-sm btn-dark sacco-deposit"><i class="bi bi-arrow-up-right-circle"></i> Deposit</button>
            <button class="btn btn-sm btn-outline-dark sacco-withdraw"><i class="bi bi-arrow-down-left-circle"></i> Withdraw</button>
            <button class="btn btn-sm btn-outline-secondary sacco-activity"><i class="bi bi-clock-history"></i> Activity</button>
          </div>
        </div>`;
      wrap.appendChild(col);
    });

    // simple button actions (replace later with real flows)
    wrap.addEventListener('click', (e) => {
      const btn = e.target.closest('button');
      if (!btn) return;
      if (btn.classList.contains('sacco-deposit'))  toast('Deposit (UI only)');
      if (btn.classList.contains('sacco-withdraw')) toast('Withdraw (UI only)');
      if (btn.classList.contains('sacco-activity')) toast('Opening activity…');
    });
  }

  // optional: filter select demo
  const filterSelect = document.querySelector('select.form-select');
  filterSelect?.addEventListener('change', () => toast(`Filter: ${filterSelect.value}`));

  // tiny toast (no dependency)
  function toast(msg, ms = 1500) {
    let t = document.querySelector('.toast');
    if (!t) {
      t = document.createElement('div');
      t.className = 'toast';
      document.body.appendChild(t);
    }
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), ms);
  }
});


document.addEventListener('DOMContentLoaded', () => {
  const data = { // your existing demo data
    total: 2895500, available: 1850000, locked: 250000, savingsTotal: 795500,
    mainWallet: 1850000, walletDelta: 35000, savedMonth: 320000, spentMonth: 210000
  };

  // totals
  const totalEl = document.getElementById('total');
  const availEl = document.getElementById('available');
  const lockEl  = document.getElementById('locked');
  const saveEl  = document.getElementById('savings');

  // remove skeleton class if present
  [totalEl, availEl, lockEl, saveEl].forEach(el => el?.classList.remove('skel'));

  countUp(totalEl, data.total, { duration: 900, prefix: 'UGX ' });
  countUp(availEl, data.available, { duration: 900, prefix: 'UGX ' });
  countUp(lockEl,  data.locked,    { duration: 900, prefix: 'UGX ' });
  countUp(saveEl,  data.savingsTotal, { duration: 900, prefix: 'UGX ' });

  // rest of your existing code (bars, SACCO cards, etc.) …
});



countUp(document.getElementById('mainWallet'), data.mainWallet, { duration: 900, prefix: 'UGX ' });
countUp(document.getElementById('savedMonth'), data.savedMonth, { duration: 900, prefix: 'UGX ' });
countUp(document.getElementById('spentMonth'), data.spentMonth, { duration: 900, prefix: 'UGX ' });

