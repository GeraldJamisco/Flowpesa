const $ = (s, r=document) => r.querySelector(s);
const UGX = v => 'UGX ' + Number(v||0).toLocaleString();

/** Load header numbers **/
(async () => {
  try {
    const r = await fetch('../api/me.php', { credentials: 'include' });
    const j = await r.json();
    if (j?.ok) {
      $('#balance').textContent = UGX(j.data.balance);
      $('#points').textContent  = (j.data.points ?? 0).toLocaleString();
    }
  } catch {}
})();

/** Amount quick add **/
const amountEl = $('#amount');
document.querySelectorAll('[data-quick]').forEach(b=>{
  b.addEventListener('click',()=>{
    const add = Number(b.dataset.quick);
    const cur = Number(String(amountEl.value).replace(/[^\d]/g,'')) || 0;
    amountEl.value = (cur+add).toLocaleString();
    validate();
  });
});

/** Select network **/
let network = null;
function pick(nw) {
  network = nw;
  $('#btnMTN').classList.toggle('active', nw==='mtn');
  $('#btnAirtel').classList.toggle('active', nw==='airtel');
  $('#tickMTN')?.classList.toggle('d-none', nw!=='mtn');
  $('#tickAirtel')?.classList.toggle('d-none', nw!=='airtel');
  validate();
}
$('#btnMTN')?.addEventListener('click',()=>pick('mtn'));
$('#btnAirtel')?.addEventListener('click',()=>pick('airtel'));

/** Inputs + validation **/
const msisdnEl = $('#msisdn');
const refEl    = $('#ref');
const confirm  = $('#confirm');
const summary  = $('#summary');
const hint     = $('#hint');

function getAmount() {
  return Number(String(amountEl.value).replace(/[^\d]/g,'')) || 0;
}
function validPhone(v) {
  // Simple check: digits >= 9 (accept +2567..., 07..., 7...)
  const digits = v.replace(/\D/g,'');
  return digits.length >= 9;
}
function validate() {
  const amt = getAmount();
  const ok = amt >= 1000 && network && validPhone(msisdnEl.value);
  confirm.disabled = !ok;
  summary.textContent = ok
    ? `Top-up ${UGX(amt)} via ${network.toUpperCase()} to ${msisdnEl.value.trim()}`
    : 'Enter amount, select network and phone.';
  hint.style.display = network ? 'block' : 'none';
}
[amountEl, msisdnEl].forEach(el => el.addEventListener('input', validate));

/** Toast **/
function toast(m){ const t=$('#toast'); t.textContent=m; t.classList.add('show'); setTimeout(()=>t.classList.remove('show'),1800); }

/** Confirm -> call API **/
confirm.addEventListener('click', async () => {
  const amt = getAmount();
  const msisdn = msisdnEl.value.trim();
  const reference = refEl.value.trim();

  confirm.disabled = true;
  toast('Sending prompt…');

  try {
    const r = await fetch('../api/topup-momo.php', {
      method: 'POST',
      credentials: 'include',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ network, msisdn, amount: amt, reference })
    });
    const j = await r.json();
    if (j?.ok) {
      // If API returns newBalance, update header
      if (j.data?.newBalance != null) {
        $('#balance').textContent = UGX(j.data.newBalance);
      }
      toast('Check your phone to approve ✔');
      // Optional: poll status… (not needed for now)
      amountEl.value = '';
      refEl.value = '';
      msisdnEl.value = '';
      pick(null); // reset
      validate();
    } else {
      toast(j?.error || 'Failed. Try again.');
    }
  } catch {
    toast('Network error.');
  } finally {
    confirm.disabled = false;
  }
});
