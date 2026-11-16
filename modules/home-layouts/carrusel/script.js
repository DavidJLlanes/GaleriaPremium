/* Portada «Carrusel»: scroll horizontal nativo (táctil, trackpad, teclado) + arrastre con ratón e inercia, progreso, contador y flechas. */
(() => {
  const root = document.querySelector('.hlc'); if (!root) return;
  const tr = root.querySelector('.hlc-track'), bar = root.querySelector('.hlc-bar'), num = root.querySelector('.hlc-count b');
  const cells = [...tr.querySelectorAll('.ccell')], btns = [...root.querySelectorAll('.hlc-b')];
  const calm = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const max = () => Math.max(1, tr.scrollWidth - tr.clientWidth);
  const step = () => (cells[1] ? cells[1].offsetLeft - cells[0].offsetLeft : tr.clientWidth * .8);
  let last = tr.scrollLeft, sk = 0, raf = 0;
  const paint = () => {
    const x = tr.scrollLeft, p = x / max();
    bar.style.setProperty('--p', p.toFixed(4));
    num.textContent = String(Math.min(cells.length, Math.round(x / step()) + 1)).padStart(2, '0');
    btns[0].disabled = x < 2; btns[1].disabled = x > max() - 2;
    if (!calm) { sk += (Math.max(-3, Math.min(3, (x - last) * .12)) - sk) * .2; tr.style.setProperty('--sk', (-sk).toFixed(2)); }
    last = x;
  };
  const loop = () => { paint(); raf = Math.abs(sk) > .02 || moving ? requestAnimationFrame(loop) : (tr.style.setProperty('--sk', '0'), sk = 0, 0); };
  let moving = false, idle = 0;
  tr.addEventListener('scroll', () => { moving = true; clearTimeout(idle); idle = setTimeout(() => { moving = false; }, 90); if (!raf) raf = requestAnimationFrame(loop); }, { passive: true });
  btns.forEach(b => b.addEventListener('click', () => tr.scrollBy({ left: step() * +b.dataset.dir, behavior: calm ? 'auto' : 'smooth' })));

  // Arrastre con ratón + inercia (en táctil manda el scroll nativo)
  let down = false, sx = 0, sl = 0, v = 0, px = 0, pt = 0, moved = 0, glide = 0;
  tr.addEventListener('pointerdown', (e) => {
    if (e.pointerType !== 'mouse' || e.button !== 0) return;
    down = true; sx = px = e.clientX; sl = tr.scrollLeft; v = 0; moved = 0; pt = performance.now(); cancelAnimationFrame(glide);
  });
  addEventListener('pointermove', (e) => {
    if (!down) return;
    const dx = e.clientX - sx; moved = Math.max(moved, Math.abs(dx));
    if (moved > 5) tr.classList.add('is-drag');
    tr.scrollLeft = sl - dx;
    const now = performance.now(), dt = Math.max(1, now - pt); v = .75 * v + .25 * ((px - e.clientX) / dt * 16); px = e.clientX; pt = now;
  });
  const end = () => {
    if (!down) return; down = false;
    const fly = () => { if (Math.abs(v) < .4) { tr.classList.remove('is-drag'); return; } tr.scrollLeft += v; v *= .94; glide = requestAnimationFrame(fly); };
    moved > 5 ? fly() : tr.classList.remove('is-drag');
  };
  addEventListener('pointerup', end); addEventListener('pointercancel', end);
  tr.addEventListener('click', (e) => { if (moved > 5) { e.preventDefault(); e.stopPropagation(); moved = 0; } }, true);
  tr.addEventListener('dragstart', (e) => e.preventDefault());
  addEventListener('resize', paint); paint();
})();
