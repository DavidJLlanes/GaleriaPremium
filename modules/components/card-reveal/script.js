/* Tarjetas de la página principal: aparición al hacer scroll (efecto elegido en Admin → Tarjetas) y parallax suave de las fotos.
 * Solo se animan transform / opacity / clip-path / filter (en la GPU). Las tarjetas que entran a la vez lo hacen en cascada. */
(() => {
  document.querySelectorAll('.coll[data-rv]').forEach((sec) => {
  const cells = [...sec.querySelectorAll('.ccell')], cards = cells.map(c => c.querySelector('.ccard'));
  const calm = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const fx = sec.dataset.rv !== 'none';
  if (calm || !('IntersectionObserver' in window)) { cards.forEach(c => c.classList.add('is-in', 'is-done')); return; }

  /* ---- Aparición ---- */
  const io = new IntersectionObserver((entries) => {
    const vis = entries.filter(e => e.isIntersecting)
      .sort((a, b) => (a.boundingClientRect.top - b.boundingClientRect.top) || (a.boundingClientRect.left - b.boundingClientRect.left));
    vis.forEach((e, i) => {
      const cell = e.target, el = cell.firstElementChild, d = Math.min(i * 110, 660);
      el.style.setProperty('--rv-d', d + 'ms'); io.unobserve(cell);
      requestAnimationFrame(() => requestAnimationFrame(() => {            // dos fotogramas: el estado inicial se pinta antes de animar
        el.classList.add('is-in'); el.dispatchEvent(new CustomEvent('card:in', { bubbles: true }));
        setTimeout(() => el.classList.add('is-done'), d + 2300);
      }));
    });
  }, { threshold: .1, rootMargin: '0px 0px -7% 0px' });
  if (fx) cells.forEach(c => io.observe(c)); else cards.forEach(c => c.classList.add('is-in', 'is-done'));

  /* ---- Parallax suave (con inercia) ---- */
  if (sec.dataset.parallax !== '1') return;
  const items = cards.map(c => ({ card: c, img: c.querySelector('.ccard-media img'), cur: 0, on: false })).filter(x => x.img);
  const vis = new IntersectionObserver((es) => es.forEach(e => { const it = items.find(x => x.card === e.target); if (it) it.on = e.isIntersecting; kick(); }), { rootMargin: '15% 0px' });
  items.forEach(x => vis.observe(x.card));
  let raf = 0;
  const tick = () => {
    let moving = false;
    for (const it of items) {
      if (!it.on) continue;
      const r = it.card.getBoundingClientRect(), p = Math.max(-1, Math.min(1, ((r.top + r.height / 2) - innerHeight / 2) / (innerHeight / 2 + r.height / 2)));
      const target = -p * r.height * .055;
      it.cur += (target - it.cur) * .12;
      if (Math.abs(target - it.cur) > .08) moving = true; else it.cur = target;
      it.img.style.translate = '0 ' + it.cur.toFixed(2) + 'px';
    }
    raf = moving ? requestAnimationFrame(tick) : 0;
  };
  const kick = () => { if (!raf) raf = requestAnimationFrame(tick); };
  addEventListener('scroll', kick, { passive: true }); addEventListener('resize', kick); kick();
});
})();
