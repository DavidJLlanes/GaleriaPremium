/* Linterna: sigue al cursor/dedo; si nadie la mueve, pasea sola por la foto. */
(() => {
  const hero = document.querySelector('.hx-linterna'); const box = hero?.querySelector('.hxl'); if (!box) return;
  const still = matchMedia('(prefers-reduced-motion: reduce)').matches;
  let tx = .5, ty = .5, x = .5, y = .5, last = 0, vis = true, raf = 0;
  const move = (e) => { const r = box.getBoundingClientRect(); tx = (e.clientX - r.left) / r.width; ty = (e.clientY - r.top) / r.height; last = performance.now(); };
  hero.addEventListener('pointermove', move, { passive: true });
  hero.addEventListener('pointerdown', move, { passive: true });
  hero.addEventListener('pointerleave', () => { last = 0; });
  new IntersectionObserver(([en]) => { vis = en.isIntersecting; if (vis && !raf) raf = requestAnimationFrame(tick); }).observe(hero);
  const tick = (t) => {
    raf = 0; if (!vis) return;
    if (!last || t - last > 3000) { tx = .5 + Math.sin(t / 2600) * .3; ty = .5 + Math.sin(t / 1900 + 1) * .22; }
    const k = still ? 1 : .09; x += (tx - x) * k; y += (ty - y) * k;
    box.style.setProperty('--x', (x * 100).toFixed(2) + '%'); box.style.setProperty('--y', (y * 100).toFixed(2) + '%');
    if (!still) raf = requestAnimationFrame(tick);
  };
  raf = requestAnimationFrame(tick);
})();
