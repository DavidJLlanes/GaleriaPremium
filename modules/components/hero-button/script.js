/* Botón de la portada: separación del título. Nivel 1–5 = fracción del hueco libre entre el botón y el indicador de scroll
   (0, 25, 50, 75 y 100 %): el 5 deja el botón justo encima del indicador, en cualquier móvil o pantalla. */
(() => {
  const F = [0, 0, .25, .5, .75, 1], GAP = 26;
  // Posición (sin transformaciones: animaciones de entrada, translate…) de un elemento dentro de la sección
  const topIn = (el, sec) => { let y = 0, n = el; while (n && n !== sec) { y += n.offsetTop; n = n.offsetParent; } return n === sec ? y : null; };
  const place = (b) => {
    const m = /hb-d(\d)/.exec(b.className); if (!m) return;
    const sec = b.closest('[data-hero]') || b.closest('section'); if (!sec) return;
    const hint = sec.querySelector('.scrollhint'), y = topIn(b, sec), yh = hint ? topIn(hint, sec) : null;
    if (y === null) return;
    const bottom = y + b.offsetHeight, limit = yh !== null ? yh : sec.clientHeight - 64;
    const free = Math.max(0, limit - GAP - bottom);
    const ty = Math.round(free * (F[+m[1]] || 0));
    b.style.setProperty('--hb-ty', ty + 'px');
  };
  const all = () => document.querySelectorAll('.hb[class*="hb-d"]').forEach(place);
  let t; const later = () => { clearTimeout(t); t = setTimeout(all, 120); };
  addEventListener('resize', later); addEventListener('orientationchange', later);
  addEventListener('load', () => { all(); setTimeout(all, 2200); });          // tras las animaciones de entrada del hero
  document.fonts && document.fonts.ready.then(all);
  if (document.readyState !== 'loading') all(); else document.addEventListener('DOMContentLoaded', all);
})();
