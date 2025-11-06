// Required fields: country + street + city + region
const required = ['country', 'street', 'city', 'region'];

const els = {
  country: document.getElementById('country'),
  street:  document.getElementById('street'),
  city:    document.getElementById('city'),
  region:  document.getElementById('region'),
  postal:  document.getElementById('postal'),
  landmark:document.getElementById('landmark'),
  btn:     document.getElementById('loc-continue'),
  err:     document.getElementById('loc-error')
};

// counters
const counters = {};
document.querySelectorAll('.counter').forEach(el => counters[el.dataset.for] = el);

// clear buttons (next to some inputs)
document.querySelectorAll('.clear-btn').forEach(btn => {
  const input = btn.previousElementSibling;
  btn.addEventListener('click', ()=>{ input.value=''; input.focus(); update(); });
});

function updateCounters(){
  Object.entries(counters).forEach(([id, el])=>{
    const inp = document.getElementById(id);
    const max = inp.getAttribute('maxlength') || 0;
    el.textContent = max ? `${inp.value.length}/${max}` : inp.value.length;
  });
}

function isValid(){
  return required.every(id => {
    const el = (id === 'country') ? els.country : document.getElementById(id);
    return (el.value || '').trim().length > 1;
  });
}

function toggleClears(){
  document.querySelectorAll('.clear-btn').forEach(btn => {
    btn.hidden = !btn.previousElementSibling.value.length;
  });
}

function update(){
  updateCounters();
  toggleClears();
  const ok = isValid();
  els.btn.disabled = !ok;
  els.btn.classList.toggle('is-active', ok);
  els.err.classList.toggle('show', !ok);
}

// listeners
document.addEventListener('input', e=>{
  if (e.target.matches('input, select')) update();
});
document.addEventListener('change', e=>{
  if (e.target.matches('select')) update();
});

els.btn.addEventListener('click', ()=>{
  update();
  if (els.btn.disabled) return;

  const payload = {
    country: els.country.value,
    street:  els.street.value.trim(),
    city:    els.city.value.trim(),
    region:  els.region.value.trim(),
    postal:  (els.postal?.value || '').trim(),
    landmark:(els.landmark?.value || '').trim()
  };
  // TODO: send to API
  console.log('Location saved:', payload);

  // next step
  location.href = 'verify-email.html';
});

// init
update();
