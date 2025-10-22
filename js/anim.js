// simple count-up animation
function countUp(el, endValue, {duration=900, prefix='', suffix='', decimals=0} = {}) {
  if (!el) return;
  const start = 0;
  const startTime = performance.now();
  const fmt = v => {
    const n = Number(v).toLocaleString(undefined, { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
    return `${prefix}${n}${suffix}`;
  };
  function tick(now) {
    const p = Math.min(1, (now - startTime) / duration);
    const eased = 1 - Math.pow(1 - p, 3); // easeOutCubic
    const value = start + (endValue - start) * eased;
    el.textContent = fmt(value);
    if (p < 1) requestAnimationFrame(tick);
  }
  requestAnimationFrame(tick);
}
