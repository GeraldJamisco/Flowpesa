// Flowpesa global theme switch (light/dark)
// Usage: call initThemeToggle('#themeToggleBtn')

(function () {
  const KEY = 'fp-theme';

  function systemPrefersDark() {
    return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
  }

  function applyTheme(theme) {
    const root = document.documentElement;
    if (theme === 'dark' || theme === 'light') {
      root.setAttribute('data-theme', theme);
      document.documentElement.style.colorScheme = theme;
    } else {
      // fallback to system
      root.removeAttribute('data-theme');
      document.documentElement.style.colorScheme = systemPrefersDark() ? 'dark' : 'light';
    }
  }

  function currentTheme() {
    return localStorage.getItem(KEY) || 'system';
  }

  function toggleTheme() {
    const cur = currentTheme();
    const next = cur === 'dark' ? 'light' : 'dark';
    localStorage.setItem(KEY, next);
    applyTheme(next);
    return next;
  }

  // Public export
  window.initThemeToggle = function (selector) {
    // initial apply
    applyTheme(currentTheme());

    // keep in sync with system if set to "system"
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
      if (currentTheme() === 'system') applyTheme('system');
    });

    // wire toggle button (optional)
    if (selector) {
      const el = document.querySelector(selector);
      if (el) {
        el.addEventListener('click', () => {
          const t = toggleTheme();
          el.dataset.mode = t;
        });
        el.dataset.mode = currentTheme();
      }
    }
  };
})();
// End of app-theme.js