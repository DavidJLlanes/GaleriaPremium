/* Contador de galerías: auto-avance, flechas, lista lateral, teclado, deslizar y rueda (solo mientras la portada está arriba). */
(() => {
  const el = document.querySelector('.hx-count'); if (!el) return;
  const bgs = [...el.querySelectorAll('.hxn-bgs img')], slides = [...el.querySelectorAll('.hxn-slide')], dots = [...el.querySelectorAll('.hxn-list button')], n = slides.length;
  if (n < 2) return;
  const ms = parseInt(el.dataset.interval, 10) || 6500; let cur = 0, timer, lock = false;
  const go = (k, abs = false) => {
    const nx = abs ? k : (cur + k + n) % n; if (nx === cur) return;
    [bgs, slides, dots].forEach(a => { a[cur]?.classList.remove('is-active'); a[nx]?.classList.add('is-active'); });
    slides[cur].tabIndex = -1; slides[nx].tabIndex = 0; cur = nx; start();
  };
  const start = () => { clearInterval(timer); timer = setInterval(() => go(1), ms); };
  el.querySelector('.hxn-next')?.addEventListener('click', () => go(1));
  el.querySelector('.hxn-prev')?.addEventListener('click', () => go(-1));
  dots.forEach(d => d.addEventListener('click', () => go(+d.dataset.i, true)));
  const inView = () => { const r = el.getBoundingClientRect(); return r.top > -40 && r.top < innerHeight * .4; };
  addEventListener('keydown', e => { if (!inView() || /input|textarea|select/i.test(e.target.tagName)) return; if (e.key === 'ArrowRight') go(1); if (e.key === 'ArrowLeft') go(-1); });
  el.addEventListener('wheel', e => {                         // la rueda cambia de galería solo en la portada y hasta llegar a los extremos
    if (scrollY > 5 || Math.abs(e.deltaY) < 8) return;
    const d = e.deltaY > 0 ? 1 : -1;
    if ((d > 0 && cur === n - 1) || (d < 0 && cur === 0)) return;
    e.preventDefault(); if (lock) return; lock = true; setTimeout(() => lock = false, 900); go(d);
  }, { passive: false });
  let x0 = null; el.addEventListener('touchstart', e => { x0 = e.touches[0].clientX; }, { passive: true });
  el.addEventListener('touchend', e => { if (x0 === null) return; const dx = e.changedTouches[0].clientX - x0; x0 = null; if (Math.abs(dx) > 50) go(dx < 0 ? 1 : -1); }, { passive: true });
  document.addEventListener('visibilitychange', () => document.hidden ? clearInterval(timer) : start());
  start();
})();
