const $ = (s, r=document) => r.querySelector(s);
const UGX = v => 'UGX ' + Number(v||0).toLocaleString();

/** load current header numbers **/
(async () => {
  try {
    const r = await fetch('../api/me.php', { credentials:'include' });
    const j = await r.json();
    if (j?.ok) {
      $('#balance').textContent = UGX(j.data.balance);
      $('#points').textContent  = (j.data.points ?? 0).toLocaleString();
    }
  } catch {}
})();

/** inputs + validation **/
const amountEl = $('#amount');
const bankEl   = $('#bankName');
const refEl    = $('#txnRef');
const noteEl   = $('#note');
const proofEl  = $('#proof');
const submit   = $('#submit');
const summary  = $('#summary');
const hint     = $('#hint');

function getAmount() {
  return Number(String(amountEl.value).replace(/[^\d]/g,'')) || 0;
}
function validate() {
  const ok = getAmount() >= 1000 && bankEl.value.trim().length >= 2 && refEl.value.trim().length >= 3;
  submit.disabled = !ok;
  summary.textContent = ok
    ? `Submit bank transfer of ${UGX(getAmount())} from ${bankEl.value.trim()}`
    : 'Fill your transfer details.';
  hint.style.display = ok ? 'block' : 'none';
}
[amountEl, bankEl, refEl].forEach(el => el.addEventListener('input', validate));

/** toast **/
function toast(m){ const t=$('#toast'); t.textContent=m; t.classList.add('show'); setTimeout(()=>t.classList.remove('show'),1800); }

/** submit -> POST to /api/topup-bank.php **/
submit.addEventListener('click', async () => {
  submit.disabled = true;
  toast('Submitting…');

  const amt = getAmount();
  const form = new FormData();
  form.append('amount', String(amt));
  form.append('bank', bankEl.value.trim());
  form.append('reference', refEl.value.trim());
  form.append('note', noteEl.value.trim());
  if (proofEl.files[0]) form.append('proof', proofEl.files[0]);

  try {
    const r = await fetch('../api/topup-bank.php', {
      method: 'POST',
      credentials: 'include',
      body: form
    });
    const j = await r.json();
    if (j?.ok) {
      toast('Received. We’ll confirm soon.');
      // Optional: redirect to wallet
      // location.href = 'wallet.php';
    } else {
      toast(j?.error || 'Failed. Try again');
    }
  } catch {
    toast('Network error');
  } finally {
    submit.disabled = false;
  }
});
