/* Parallax 3D: el ratón (o el dedo) mueve las variables --mx/--my (−1…1); sin interacción, la escena se balancea sola. */
(() => {
  const hero = document.querySelector('.hx-parallax'); if (!hero) return;
  if (matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  let tx = 0, ty = 0, x = 0, y = 0, last = 0, raf = 0, vis = true;
  const move = (e) => {
    const r = hero.getBoundingClientRect();
    tx = ((e.clientX - r.left) / r.width) * 2 - 1; ty = ((e.clientY - r.top) / r.height) * 2 - 1; last = performance.now();
  };
  hero.addEventListener('pointermove', move, { passive: true });
  hero.addEventListener('pointerleave', () => { last = 0; });
  new IntersectionObserver(([en]) => { vis = en.isIntersecting; if (vis && !raf) raf = requestAnimationFrame(tick); }).observe(hero);
  const tick = (t) => {
    raf = 0; if (!vis) return;
    if (!last || t - last > 2500) { tx = Math.sin(t / 3200) * .7; ty = Math.cos(t / 4100) * .5; }   // balanceo automático
    x += (tx - x) * .06; y += (ty - y) * .06;
    hero.style.setProperty('--mx', x.toFixed(3)); hero.style.setProperty('--my', y.toFixed(3));
    raf = requestAnimationFrame(tick);
  };
  raf = requestAnimationFrame(tick);
})();
