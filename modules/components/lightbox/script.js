/* Lightbox genérico: abre cualquier contenedor [data-lightbox] cuyos hijos <a href="foto"> tengan data-title. */
(() => {
  const lb = document.getElementById('lightbox');
  const box = document.querySelector('[data-lightbox]');
  if (!lb || !box) return;

  const items = [...box.querySelectorAll('a[href]')];
  const img = lb.querySelector('img'), cap = lb.querySelector('figcaption'), count = lb.querySelector('.lb-count');
  let idx = 0;

  const show = (i, animate = true) => {
    idx = (i + items.length) % items.length;
    const a = items[idx];
    const set = () => {
      img.src = a.href; cap.textContent = a.dataset.title || '';
      count.textContent = String(idx + 1).padStart(2, '0') + ' / ' + String(items.length).padStart(2, '0');
      img.classList.remove('is-swap');
    };
    if (animate) { img.classList.add('is-swap'); setTimeout(set, 180); } else set();
    [idx + 1, idx - 1].forEach(n => { new Image().src = items[(n + items.length) % items.length].href; });
  };
  const open = (i) => {
    lb.hidden = false; document.body.style.overflow = 'hidden';
    requestAnimationFrame(() => lb.classList.add('is-open')); show(i, false);
  };
  const close = () => {
    lb.classList.remove('is-open'); document.body.style.overflow = '';
    setTimeout(() => lb.hidden = true, 450);
  };

  items.forEach((a, i) => a.addEventListener('click', e => { e.preventDefault(); open(i); }));
  lb.querySelector('.lb-close').onclick = close;
  lb.querySelector('.lb-prev').onclick = () => show(idx - 1);
  lb.querySelector('.lb-next').onclick = () => show(idx + 1);
  lb.addEventListener('click', e => { if (e.target === lb) close(); });
  addEventListener('keydown', e => {
    if (lb.hidden) return;
    if (e.key === 'Escape') close();
    if (e.key === 'ArrowRight') show(idx + 1);
    if (e.key === 'ArrowLeft') show(idx - 1);
  });
  let x0 = null;
  lb.addEventListener('touchstart', e => x0 = e.touches[0].clientX, { passive: true });
  lb.addEventListener('touchend', e => {
    if (x0 === null) return;
    const dx = e.changedTouches[0].clientX - x0;
    if (Math.abs(dx) > 50) show(idx + (dx < 0 ? 1 : -1));
    x0 = null;
  });
})();
