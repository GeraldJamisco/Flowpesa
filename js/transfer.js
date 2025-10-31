const $ = (s, r=document) => r.querySelector(s);
const UGX = v => 'UGX ' + Number(v||0).toLocaleString();

/* Load wallet header */
(async () => {
  try {
    const r = await fetch('../api/me.php', { credentials:'include' });
    const j = await r.json();
    if (j?.ok) {
      $('#balance').textContent = UGX(j.data.balance);
      $('#points').textContent  = (j.data.points ?? 0).toLocaleString();
    } else {
      location.href = 'login.php';
    }
  } catch { /* keep UI */ }
})();

/* Quick send */
function onlyDigits(s){ return (s||'').replace(/[^\d]/g,''); }
function showToast(m){ const t=$('#toast'); t.textContent=m; t.classList.add('show'); setTimeout(()=>t.classList.remove('show'),1800); }

$('#quickSend')?.addEventListener('click', async () => {
  const to = $('#quickTo').value.trim();
  const amt = Number(onlyDigits($('#quickAmt').value));
  if (!to || amt < 1000) return showToast('Enter receiver and amount ≥ 1,000');

  // Decide route: handle (@user) vs phone (+256…)
  const isHandle = to.startsWith('@');
  const payload = isHandle
    ? { kind:'handle', handle: to.slice(1), amount: amt }
    : { kind:'phone',  msisdn: to,        amount: amt };

  try {
    const r = await fetch('../api/transfer-quick.php', {
      method:'POST',
      credentials:'include',
      headers:{'Content-Type':'application/json'},
      body: JSON.stringify(payload)
    });
    const j = await r.json();
    if (j?.ok) {
      showToast('Sent ✔');
      $('#quickTo').value = ''; $('#quickAmt').value = '';
      // Optional: update balance preview
      if (j.data?.newBalance != null) $('#balance').textContent = UGX(j.data.newBalance);
    } else {
      showToast(j?.error || 'Failed');
    }
  } catch {
    showToast('Network error');
  }
});

/* Populate “Recent” (mock for now; replace with API) */
const recent = [
  { kind:'handle', label:'@legend',   meta:'Flowpesa friend' },
  { kind:'phone',  label:'+256 701…', meta:'Airtel Money'    },
  { kind:'bank',   label:'Stanbic UG',meta:'**** 3421'       },
  { kind:'card',   label:'VISA',      meta:'**** 8830'       },
  { kind:'handle', label:'@giant',    meta:'Flowpesa friend' },
];
const list = $('#recentList');
recent.slice(0,5).forEach(r=>{
  const row = document.createElement('button');
  row.className = 'btn-linkish';
  row.innerHTML = `
    <span><i class="bi ${r.kind==='handle'?'bi-at':r.kind==='phone'?'bi-sim':r.kind==='bank'?'bi-bank':'bi-credit-card-2-front'}"></i>
      <b>${r.label}</b><br><span class="text-secondary small">${r.meta}</span>
    </span>
    <i class="bi bi-chevron-right"></i>`;
  list.appendChild(row);
});
