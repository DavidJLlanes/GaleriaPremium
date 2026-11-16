/* Portada «Editorial alterno»: aparición de cada fila al entrar en pantalla y parallax suave (con inercia) de las fotos. */
(() => {
  const root = document.querySelector('.hla'); if (!root) return;
  const rows = [...root.querySelectorAll('.hla-row')], calm = matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (calm || !('IntersectionObserver' in window)) return;
  root.classList.add('is-js');
  const io = new IntersectionObserver((es) => es.forEach(e => { if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); } }), { threshold: .18, rootMargin: '0px 0px -6% 0px' });
  rows.forEach(r => io.observe(r));
  const items = rows.map(r => ({ box: r.querySelector('.hla-media'), img: r.querySelector('.hla-media img'), cur: 0, on: false })).filter(x => x.img);
  let raf = 0;
  const tick = () => {
    let moving = false;
    for (const it of items) {
      if (!it.on) continue;
      const b = it.box.getBoundingClientRect(), p = Math.max(-1, Math.min(1, ((b.top + b.height / 2) - innerHeight / 2) / (innerHeight / 2 + b.height / 2)));
      const target = -p * b.height * .07; it.cur += (target - it.cur) * .1;
      if (Math.abs(target - it.cur) > .1) moving = true;
      it.img.style.setProperty('--py', it.cur.toFixed(2) + 'px');
    }
    raf = moving ? requestAnimationFrame(tick) : 0;
  };
  const kick = () => { if (!raf) raf = requestAnimationFrame(tick); };
  const vis = new IntersectionObserver((es) => es.forEach(e => { const it = items.find(x => x.box === e.target); if (it) it.on = e.isIntersecting; kick(); }), { rootMargin: '20% 0px' });
  items.forEach(x => vis.observe(x.box));
  addEventListener('scroll', kick, { passive: true }); addEventListener('resize', kick);
})();
