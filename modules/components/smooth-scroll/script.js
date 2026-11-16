/* Scroll con inercia: interpola la posición hacia un objetivo con la rueda del ratón. Solo ordenador (puntero fino) y sin «reducir movimiento».
   Respeta: zoom con ctrl+rueda, zonas con su propio scroll (menú, lightbox), teclado, barra de scroll y enlaces #ancla. */
(() => {
  const cfg = document.querySelector('.ss-cfg'); if (!cfg) return;
  if (!matchMedia('(pointer:fine)').matches || matchMedia('(prefers-reduced-motion:reduce)').matches) return;
  const L = { soft: { f: .16, k: .9 }, medium: { f: .1, k: 1 }, strong: { f: .065, k: 1.15 } }[cfg.dataset.level] || { f: .1, k: 1 };
  const root = document.documentElement, max = () => Math.max(0, root.scrollHeight - innerHeight);
  let target = scrollY, cur = scrollY, raf = 0, last = 0, own = false;
  const clamp = (v) => Math.max(0, Math.min(max(), v));
  root.classList.add('ss-on');

  const locked = () => root.classList.contains('mn-lock') || document.body.style.overflow === 'hidden' || !!document.querySelector('.lb:not([hidden])');
  const inner = (el) => {   // ¿el cursor está sobre algo con scroll propio que pueda moverse en esa dirección?
    for (; el && el !== document.body && el !== root; el = el.parentElement) {
      const s = getComputedStyle(el);
      if (/(auto|scroll)/.test(s.overflowY) && el.scrollHeight > el.clientHeight + 1) return true;
    }
    return false;
  };
  const tick = (t) => {
    const dt = Math.min(64, t - (last || t)); last = t;
    cur += (target - cur) * (1 - Math.pow(1 - L.f, dt / 16.67));
    if (Math.abs(target - cur) < .4) cur = target;
    own = true; scrollTo(0, cur); requestAnimationFrame(() => { own = false; });
    if (cur !== target) raf = requestAnimationFrame(tick); else { raf = 0; last = 0; }
  };
  const go = () => { if (!raf) raf = requestAnimationFrame(tick); };

  addEventListener('wheel', (e) => {
    if (e.ctrlKey || e.defaultPrevented || locked() || inner(e.target)) return;
    e.preventDefault();
    const d = e.deltaMode === 1 ? e.deltaY * 16 : e.deltaMode === 2 ? e.deltaY * innerHeight : e.deltaY;
    target = clamp((raf ? target : scrollY) + d * L.k); go();
  }, { passive: false });
  // Cambios que no vienen de la rueda (teclado, barra de scroll, enlaces): el objetivo se sincroniza
  addEventListener('scroll', () => { if (!own && !raf) { target = cur = scrollY; } }, { passive: true });
  addEventListener('resize', () => { target = clamp(target); });
  // Enlaces a #ancla: se animan con el mismo motor
  document.addEventListener('click', (e) => {
    const a = e.target.closest?.('a[href*="#"]'); if (!a || e.defaultPrevented || e.metaKey || e.ctrlKey) return;
    const u = new URL(a.href, location.href); if (u.pathname !== location.pathname || !u.hash || u.hash === '#') return;
    const el = document.getElementById(decodeURIComponent(u.hash.slice(1))); if (!el) return;
    e.preventDefault(); history.pushState(null, '', u.hash);
    if (!raf) cur = scrollY;
    target = clamp(el.getBoundingClientRect().top + scrollY - 40); go();
  });
})();
