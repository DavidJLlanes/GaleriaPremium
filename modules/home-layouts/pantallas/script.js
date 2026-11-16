/* Portada «Pantallas apiladas»: cada panel es sticky; mientras el siguiente lo cubre, se aleja y se oscurece (--p de 0 a 1). */
(() => {
  const root = document.querySelector('.hlp'); if (!root) return;
  const ps = [...root.querySelectorAll('.hlp-p')], calm = matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (calm || !('IntersectionObserver' in window)) { ps.forEach(p => p.classList.add('is-in')); return; }
  root.classList.add('is-js');
  const io = new IntersectionObserver((es) => es.forEach(e => { if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); } }), { threshold: .45 });
  ps.forEach(p => io.observe(p));
  let raf = 0, live = false;
  const paint = () => {
    raf = 0; const h = innerHeight;
    ps.forEach((p, i) => { const nx = ps[i + 1]; if (!nx) return; p.style.setProperty('--p', Math.max(0, Math.min(1, (h - nx.getBoundingClientRect().top) / h)).toFixed(3)); });
  };
  const kick = () => { if (live && !raf) raf = requestAnimationFrame(paint); };
  new IntersectionObserver((es) => { live = es[0].isIntersecting; kick(); }).observe(root);
  addEventListener('scroll', kick, { passive: true }); addEventListener('resize', kick);
})();
