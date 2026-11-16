/* Menú de galerías: abrir/cerrar, Esc, clic fuera, bloqueo de scroll, foco y accesibilidad. */
(() => {
  const btn = document.getElementById('mnBtn'), panel = document.getElementById('mnPanel'); if (!btn || !panel) return;
  const root = document.documentElement, items = () => [...panel.querySelectorAll('a[href]')];
  let open = false;
  const set = (on) => {
    if (on === open) return; open = on;
    btn.setAttribute('aria-expanded', on ? 'true' : 'false');
    panel.classList.toggle('is-open', on); root.classList.toggle('mn-lock', on);
    dispatchEvent(new Event(on ? 'mn:open' : 'mn:close'));
    if (on) { panel.removeAttribute('inert'); setTimeout(() => { const a = items()[0]; if (a && open) a.focus({ preventScroll: true }); }, 350); }
    else { panel.setAttribute('inert', ''); if (panel.contains(document.activeElement)) btn.focus({ preventScroll: true }); }
  };
  btn.addEventListener('click', () => set(!open));
  panel.addEventListener('click', (e) => { if (e.target.closest('[data-mn-close]')) set(false); else if (e.target.closest('a[href]')) set(false); });
  addEventListener('keydown', (e) => {
    if (!open) return;
    if (e.key === 'Escape') { set(false); return; }
    if (e.key !== 'Tab') return;
    const f = [btn, ...items()], i = f.indexOf(document.activeElement);       // el foco da la vuelta entre el botón y los ítems
    if (e.shiftKey && i <= 0) { e.preventDefault(); f[f.length - 1].focus(); } else if (!e.shiftKey && i === f.length - 1) { e.preventDefault(); f[0].focus(); }
  });
  addEventListener('pageshow', (e) => { if (e.persisted) set(false); });
  if (/[?&]menu_open=1/.test(location.search)) setTimeout(() => set(true), 500);   // vista previa del panel de administración
})();
