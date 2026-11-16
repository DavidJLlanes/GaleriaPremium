/* Mosaico: cada cierto tiempo una casilla al azar gira y muestra otra foto del pase. */
(() => {
  const box = document.querySelector('.hxm'); if (!box) return;
  const pool = JSON.parse(box.dataset.photos); if (pool.length < 2) return;
  const cells = [...box.querySelectorAll('.hxm-flip')];
  const ms = Math.max(900, (parseInt(box.dataset.interval, 10) || 6500) / 3.5);
  const shown = cells.map((_, i) => i % pool.length); let last = -1, timer;
  const tick = () => {
    let c; do { c = Math.floor(Math.random() * cells.length); } while (c === last && cells.length > 1); last = c;
    const free = pool.map((_, i) => i).filter(i => !shown.includes(i));                 // foto que ahora mismo no se ve
    const next = free.length ? free[Math.floor(Math.random() * free.length)] : (shown[c] + 1) % pool.length;
    const flip = cells[c], flipped = flip.classList.contains('is-flipped');
    const hidden = flip.querySelector(flipped ? '.hxm-f' : '.hxm-b'); hidden.src = pool[next].u; hidden.alt = pool[next].a;
    const swap = () => { flip.classList.toggle('is-flipped'); shown[c] = next; };
    hidden.complete && hidden.naturalWidth ? swap() : (hidden.onload = swap);
  };
  const start = () => { clearInterval(timer); timer = setInterval(tick, ms); };
  document.addEventListener('visibilitychange', () => document.hidden ? clearInterval(timer) : start());
  start();
})();
