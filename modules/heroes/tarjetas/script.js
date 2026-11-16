/* Tarjetas apiladas: avanza solo; clic en la carta de delante o arrastre/deslizar para cambiar. */
(() => {
  const box = document.querySelector('.hxt'); if (!box) return;
  const cards = [...box.querySelectorAll('.hxt-card')], bgs = [...box.querySelectorAll('.hxt-bg img')], n = cards.length;
  const ms = parseInt(box.dataset.interval, 10) || 6500; let cur = 0, timer, prev = -1;
  const paint = () => {
    cards.forEach((c, i) => {
      const d = (i - cur + n) % n;
      c.dataset.pos = d === 0 ? '0' : d === 1 ? '1' : d === 2 ? '2' : (i === prev ? 'gone' : 'hide');
    });
    bgs.forEach((b, i) => b.classList.toggle('is-active', i === cur));
  };
  const go = (k) => { prev = cur; cur = (cur + k + n) % n; paint(); start(); };
  const start = () => { clearInterval(timer); if (n > 1) timer = setInterval(() => go(1), ms); };
  paint(); if (n < 2) return;
  const stack = box.querySelector('.hxt-stack'); let x0 = null, moved = false;
  stack.addEventListener('pointerdown', e => { x0 = e.clientX; moved = false; });
  stack.addEventListener('pointermove', e => { if (x0 !== null && Math.abs(e.clientX - x0) > 8) moved = true; });
  stack.addEventListener('pointerup', e => { if (x0 === null) return; const dx = e.clientX - x0; x0 = null; if (Math.abs(dx) > 40) go(dx < 0 ? 1 : -1); else if (!moved) go(1); });
  stack.addEventListener('pointercancel', () => { x0 = null; });
  document.addEventListener('visibilitychange', () => document.hidden ? clearInterval(timer) : start());
  start();
})();
