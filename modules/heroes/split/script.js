/* Pantalla dividida: pase de fotos en «texto + foto»; en «dos fotos» el toque alterna el panel abierto (móvil). */
(() => {
  const el = document.querySelector('.hx-split'); if (!el) return;
  if (el.classList.contains('is-duo')) {
    const panes = [...el.querySelectorAll('.hxsp-pane')];
    panes.forEach(p => p.addEventListener('click', () => { const o = p.classList.contains('is-open'); panes.forEach(x => x.classList.remove('is-open')); if (!o) p.classList.add('is-open'); }));
    return;
  }
  const imgs = [...el.querySelectorAll('.hxsp-photo img')]; if (imgs.length < 2) return;
  const ms = parseInt(el.dataset.interval, 10) || 6500; let n = 0, t;
  const start = () => { clearInterval(t); t = setInterval(() => { imgs[n].classList.remove('is-active'); n = (n + 1) % imgs.length; imgs[n].classList.add('is-active'); }, ms); };
  document.addEventListener('visibilitychange', () => document.hidden ? clearInterval(t) : start()); start();
})();
