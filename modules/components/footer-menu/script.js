/* Desplegable del pie: abre/cierra (clic, Esc, clic fuera), `inert` cuando está cerrado y flechas del teclado. */
(() => {
  document.querySelectorAll('[data-fm]').forEach((m) => {
    const b = m.querySelector('.fm-b'), w = m.querySelector('.fm-w'); if (!b || !w) return;
    const items = () => [...w.querySelectorAll('.fm-i')];
    const set = (on, focus) => {
      b.setAttribute('aria-expanded', on ? 'true' : 'false'); w.classList.toggle('is-open', on); w.inert = !on;
      if (on) setTimeout(() => { const r = m.getBoundingClientRect(); if (r.bottom + 90 > innerHeight) scrollBy({ top: r.bottom + 90 - innerHeight, behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' }); }, 260);
      if (!on && focus) b.focus();
    };
    b.addEventListener('click', () => set(b.getAttribute('aria-expanded') !== 'true'));
    document.addEventListener('click', (e) => { if (!m.contains(e.target)) set(false); });
    m.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') return set(false, true);
      if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
        e.preventDefault(); if (b.getAttribute('aria-expanded') !== 'true') set(true);
        const l = items(), i = l.indexOf(document.activeElement); l[(i + (e.key === 'ArrowDown' ? 1 : -1) + l.length) % l.length]?.focus();
      }
    });
    w.addEventListener('click', (e) => { if (e.target.closest('[data-legal-open]')) set(false); });
  });
})();
