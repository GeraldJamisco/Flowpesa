// js/api.js
(function initFlowpesaApi(){
  // === Config ===
  const CONFIG = {
    // switch these when backend is live
    BASE_URL: '/api',       // PHP/Node endpoints (e.g., /api/me)
    MOCK_URL: '/mock',      // static JSON files for local testing
    USE_MOCK: true,         // set to false when your API is ready
    TIMEOUT_MS: 12000
  };

  function withTimeout(promise, ms = CONFIG.TIMEOUT_MS) {
    return Promise.race([
      promise,
      new Promise((_, rej) => setTimeout(() => rej(new Error('Request timeout')), ms))
    ]);
  }

  function token() { return localStorage.getItem('token') || ''; }
  function setToken(t){ t ? localStorage.setItem('token', t) : localStorage.removeItem('token'); }
  function logout(to='login.html'){ setToken(''); location.href = to; }

  async function request(path, { method='GET', headers={}, body, mock=false } = {}) {
    const isMock = CONFIG.USE_MOCK || mock || !path.startsWith('/');
    // Build URL
    const url = isMock
      ? (path.startsWith('/mock') ? path : `${CONFIG.MOCK_URL}${path.startsWith('/')?path:''}/${path}`)
      : `${CONFIG.BASE_URL}${path}`;

    const h = {
      'Content-Type': 'application/json',
      ...(token() ? { Authorization: `Bearer ${token()}` } : {}),
      ...headers
    };
    const opts = { method, headers: h };
    if (body !== undefined) opts.body = typeof body === 'string' ? body : JSON.stringify(body);

    const res = await withTimeout(fetch(url, opts));
    let data = null;
    try { data = await res.json(); } catch { /* allow no body */ }

    // Accept either plain JSON or { ok, data, error }
    if (!res.ok || (data && data.ok === false)) {
      const msg = (data && (data.error || data.message)) || `HTTP ${res.status}`;
      // handle auth expiry
      if (res.status === 401) logout(); // redirect to login
      throw new Error(msg);
    }
    return data?.data ?? data ?? null;
  }

  // Shorthand methods
  const get  = (path, o={}) => request(path, { ...o, method: 'GET' });
  const post = (path, body, o={}) => request(path, { ...o, method: 'POST', body });
  const put  = (path, body, o={}) => request(path, { ...o, method: 'PUT', body });
  const del  = (path, o={}) => request(path, { ...o, method: 'DELETE' });

  // Simple auth guard
  function guard(to='login.html'){
    if (!token()) location.href = to;
  }

  // Expose globally (non-module setup)
  window.FlowpesaAPI = { get, post, put, del, request, guard, logout, setToken, CONFIG };
})();
