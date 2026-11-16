/* Portada «Índice editorial»: filas que aparecen en cascada y portada flotante que sigue al cursor con inercia. */
(() => {
  const root = document.querySelector('.hll'); if (!root) return;
  const rows = [...root.querySelectorAll('.hll-row')], calm = matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (!calm && 'IntersectionObserver' in window) {
    root.classList.add('is-js');
    const io = new IntersectionObserver((es) => es.filter(e => e.isIntersecting).forEach((e, k) => { e.target.style.setProperty('--d', k * 90); e.target.classList.add('is-in'); io.unobserve(e.target); }), { rootMargin: '0px 0px -8% 0px' });
    rows.forEach(r => io.observe(r));
  }
  const fl = root.querySelector('.hll-float'); if (!fl || !matchMedia('(hover:hover) and (pointer:fine)').matches) return;
  const imgs = [...fl.querySelectorAll('img')], list = root.querySelector('.hll-list');
  let tx = 0, ty = 0, x = 0, y = 0, r = 0, raf = 0, on = false;
  const tick = () => {
    const dx = tx - x; x += dx * .12; y += (ty - y) * .12; r += (Math.max(-8, Math.min(8, dx * .06)) - r) * .15;
    fl.style.setProperty('--x', x.toFixed(1) + 'px'); fl.style.setProperty('--y', y.toFixed(1) + 'px'); fl.style.setProperty('--r', r.toFixed(2) + 'deg');
    raf = on || Math.abs(dx) > .3 ? requestAnimationFrame(tick) : 0;
  };
  const aim = (e) => { tx = e.clientX + 28; ty = e.clientY - fl.offsetHeight / 2; if (tx + fl.offsetWidth > innerWidth - 12) tx = e.clientX - fl.offsetWidth - 28; };
  list.addEventListener('pointermove', (e) => { aim(e); if (!raf) raf = requestAnimationFrame(tick); });
  list.addEventListener('pointerover', (e) => {
    const a = e.target.closest('a[data-i]'); if (!a) return;
    if (!on) { aim(e); x = tx; y = ty; }
    on = true; fl.classList.add('is-on'); fl.style.scale = '1';
    imgs.forEach(i => i.classList.toggle('is-on', i.dataset.i === a.dataset.i));
  });
  list.addEventListener('pointerleave', () => { on = false; fl.classList.remove('is-on'); fl.style.scale = ''; });
  addEventListener('scroll', () => { if (on && !list.matches(':hover')) { on = false; fl.classList.remove('is-on'); } }, { passive: true });
})();
