/* Modal legal: <dialog> nativo (foco atrapado, Esc), animación de entrada/salida, cierre al pulsar fuera, índice con marcador deslizante. */
(() => {
  const d = document.getElementById('lg'); if (!d) return;
  const s = d.querySelector('.lg-s'), nav = d.querySelector('.lg-n'), ink = d.querySelector('.lg-ink'), links = [...nav.querySelectorAll('a')];
  const secs = links.map((a) => d.querySelector(a.getAttribute('href')));
  const reduce = () => matchMedia('(prefers-reduced-motion: reduce)').matches;
  let closing = false;
  const mark = (i) => { links.forEach((a, k) => a.classList.toggle('is-on', k === i)); const a = links[i]; ink.style.setProperty('--x', a.offsetLeft + 'px'); ink.style.setProperty('--w', (a.offsetWidth - parseFloat(getComputedStyle(a).paddingRight)) + 'px'); };
  const spy = () => { let i = 0; secs.forEach((x, k) => { if (x.offsetTop - s.scrollTop <= 80) i = k; }); if (s.scrollTop + s.clientHeight >= s.scrollHeight - 4) i = secs.length - 1; mark(i); };
  s.addEventListener('scroll', spy, { passive: true });
  links.forEach((a, k) => a.addEventListener('click', (e) => { e.preventDefault(); s.scrollTo({ top: secs[k].offsetTop - 8, behavior: reduce() ? 'auto' : 'smooth' }); }));
  const open = () => {
    if (d.open) return; closing = false; d.showModal(); document.body.style.overflow = 'hidden'; s.scrollTop = 0;
    requestAnimationFrame(() => requestAnimationFrame(() => { d.classList.add('is-open'); spy(); }));
  };
  const close = () => {
    if (!d.open || closing) return; closing = true; d.classList.remove('is-open');
    const end = () => { d.close(); document.body.style.overflow = ''; closing = false; };
    reduce() ? end() : setTimeout(end, 620);
  };
  document.addEventListener('click', (e) => { if (e.target.closest('[data-legal-open]')) { e.preventDefault(); open(); } });
  d.addEventListener('cancel', (e) => { e.preventDefault(); close(); });
  d.addEventListener('click', (e) => { if (e.target === d || e.target.closest('[data-lg-close]')) close(); });
  addEventListener('resize', () => d.open && spy());
})();
