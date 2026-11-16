/* Cursor propio: aro con retardo suave, punto exacto, estado «foto» (con etiqueta), estado «enlace» e imán en botones. */
(() => {
  const c = document.querySelector('.cur'); if (!c) return;
  if (!matchMedia('(pointer:fine)').matches) return;
  const root = document.documentElement;
  let x = innerWidth / 2, y = innerHeight / 2, rx = x, ry = y, mx = 0, my = 0, shown = false, raf = 0, magnet = null;
  const PHOTO = '.ccard,.ccell a,[data-lightbox] a[href],.gg-item,.gm-item,.gj-item,.gt-item,.gf-slide,.gs-slide,[data-cursor="photo"]';
  const LINK = 'a[href],button,[role="button"],input,select,textarea,label,[data-cursor="link"]';
  const MAGNET = '.hb,.mnb,.hdr-brand,[data-magnet]';

  const loop = () => {
    rx += (x - rx) * .17; ry += (y - ry) * .17;
    const tx = magnet ? magnet.x : 0, ty = magnet ? magnet.y : 0;
    c.style.setProperty('--cx', x + 'px'); c.style.setProperty('--cy', y + 'px');
    c.style.setProperty('--rx', rx + 'px'); c.style.setProperty('--ry', ry + 'px');
    if (magnet) { magnet.el.style.transform = `translate(${(tx).toFixed(1)}px,${(ty).toFixed(1)}px)`; }
    raf = Math.abs(x - rx) + Math.abs(y - ry) > .1 || magnet ? requestAnimationFrame(loop) : 0;
  };
  const kick = () => { if (!raf) raf = requestAnimationFrame(loop); };

  addEventListener('pointermove', (e) => {
    if (e.pointerType === 'touch') return;
    x = e.clientX; y = e.clientY;
    if (!shown) { shown = true; c.hidden = false; rx = x; ry = y; root.classList.add('cur-on'); }
    c.classList.remove('is-out');
    const t = e.target;
    const photo = t.closest?.(PHOTO), link = !photo && t.closest?.(LINK);
    c.classList.toggle('is-photo', !!photo); c.classList.toggle('is-link', !!link);
    // Imán: el botón se desplaza hacia el cursor unos píxeles (y vuelve al salir)
    const m = t.closest?.(MAGNET);
    if (m) { const r = m.getBoundingClientRect(); const dx = (x - (r.left + r.width / 2)) * .22, dy = (y - (r.top + r.height / 2)) * .3; magnet = { el: m, x: dx, y: dy }; m.style.transition = 'transform .25s var(--ease)'; }
    else if (magnet) { magnet.el.style.transform = ''; magnet = null; }
    kick();
  }, { passive: true });
  document.addEventListener('pointerleave', () => c.classList.add('is-out'));
  addEventListener('blur', () => c.classList.add('is-out'));
  addEventListener('pointerdown', () => c.classList.add('is-down'));
  addEventListener('pointerup', () => c.classList.remove('is-down'));
})();
