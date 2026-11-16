/* Slider horizontal con proporción común. Con data-fit=1 reparte el espacio sobrante entre las fotos
   (o ajusta ligeramente su tamaño si el hueco sería enorme) para que NUNCA se vea una foto cortada. */
(() => {
  const root = document.querySelector('.gs'); if (!root) return;
  const view = root.querySelector('.gs-view'), track = root.querySelector('.gs-track');
  const slides = [...track.children], N = slides.length;
  const R = parseFloat(root.dataset.ratio) || 1.5, fit = root.dataset.fit === '1', hh = parseFloat(root.dataset.h) || 66;
  const prev = root.querySelector('.gs-prev'), next = root.querySelector('.gs-next'), dots = root.querySelector('.gs-dots');
  // Separación: proporcional al ancho de la pantalla, con mínimo y máximo (móvil y escritorio quedan equilibrados)
  const GAPS = { none: [0, 0, 0], s: [6, .7, 12], m: [8, 1.1, 22], l: [12, 1.8, 36] }, lv = GAPS[root.dataset.spacing] || GAPS.m;
  let base = 0, sw = 0, g = 0, n = 1, pos = 0, maxPos = 0, vw = 0;

  const layout = () => {
    const cs = getComputedStyle(root), W = root.clientWidth - parseFloat(cs.paddingLeft) - parseFloat(cs.paddingRight);
    sw = Math.min(innerHeight * hh / 100 * R, W, 900);
    sw = Math.max(sw, Math.min(160, W));
    base = Math.round(Math.min(lv[2], Math.max(lv[0], innerWidth * lv[1] / 100))); if (!lv[2]) base = 0;
    g = base; vw = W;
    if (fit) {
      n = Math.max(1, Math.floor((W + base) / (sw + base)));
      if (N <= n) { n = N; g = N > 1 ? Math.min(Math.max(base, (W - N * sw) / (N - 1)), base * 3 + 24) : 0; vw = N * sw + (N - 1) * g; }
      else if (n === 1) { g = base; vw = sw; }              // cabe una sola: ventana del tamaño exacto de la foto, centrada
      else {
        g = (W - n * sw) / (n - 1);
        // hueco demasiado grande: probar con una foto más, algo más pequeñas, a la separación base
        if (g > Math.max(base * 3, 48)) {
          const sw2 = (W - n * base) / (n + 1);
          if (sw2 >= sw * .85) { n += 1; sw = sw2; g = base; }
        }
      }
      maxPos = Math.max(0, N - n);
    } else {
      n = Math.max(1, Math.floor((W + base) / (sw + base)));
      const total = N * sw + (N - 1) * base, over = Math.max(0, total - W);
      maxPos = over > 0 ? Math.ceil(over / (sw + base)) : 0;
    }
    track.style.setProperty('--w', sw + 'px'); track.style.setProperty('--g', g + 'px');
    view.style.width = vw + 'px';
    pos = Math.min(pos, maxPos);
    buildDots(); go(pos, true);
  };

  const step = () => sw + g;
  const offsetOf = (p) => {
    if (fit) return p * step();
    return Math.min(p * step(), Math.max(0, N * sw + (N - 1) * g - view.clientWidth));   // el último tramo se alinea al borde
  };
  const pages = () => Math.floor(maxPos / n) + (maxPos % n ? 1 : 0) + 1;
  const buildDots = () => {
    dots.innerHTML = Array.from({ length: pages() }, (_, i) => '<i></i>').join('');
    [...dots.children].forEach((d, i) => d.addEventListener('click', () => go(Math.min(i * n, maxPos))));
    prev.parentElement.hidden = maxPos === 0;
  };

  const go = (p, instant) => {
    pos = Math.max(0, Math.min(maxPos, Math.round(p)));
    if (instant) track.style.transition = 'none';
    track.style.transform = `translateX(${-offsetOf(pos)}px)`;
    if (instant) { track.offsetWidth; track.style.transition = ''; }
    prev.disabled = pos <= 0; next.disabled = pos >= maxPos;
    const on = Math.min(pages() - 1, Math.ceil(pos / n));
    [...dots.children].forEach((d, i) => d.classList.toggle('is-on', i === on));
  };

  prev.addEventListener('click', () => go(pos - n));
  next.addEventListener('click', () => go(pos + n));
  addEventListener('keydown', e => {
    const r = root.getBoundingClientRect(); if (r.bottom < 0 || r.top > innerHeight || document.getElementById('lightbox')?.hidden === false) return;
    if (e.key === 'ArrowRight') go(pos + n); if (e.key === 'ArrowLeft') go(pos - n);
  });

  // Arrastrar (ratón y dedo): sigue al puntero y al soltar salta a la foto más cercana
  let down = false, sx = 0, base0 = 0, moved = false, t0 = 0;
  view.addEventListener('pointerdown', e => { if (e.button) return; down = true; moved = false; sx = e.clientX; base0 = offsetOf(pos); t0 = performance.now(); });
  addEventListener('pointermove', e => {
    if (!down) return; const dx = e.clientX - sx;
    if (!moved && Math.abs(dx) > 6) { moved = true; track.classList.add('is-drag'); view.classList.add('is-drag'); }
    if (moved) track.style.transform = `translateX(${-(base0 - dx)}px)`;
  });
  const up = (e) => {
    if (!down) return; down = false; track.classList.remove('is-drag'); view.classList.remove('is-drag');
    if (!moved) return;
    const dx = e.clientX - sx, fast = Math.abs(dx) / Math.max(1, performance.now() - t0) > .5;
    let target = (base0 - dx) / step();
    if (fast) target = dx < 0 ? Math.ceil(pos + .01 + Math.abs(dx) / step() * .5) : Math.floor(pos - Math.abs(dx) / step() * .5);
    go(target);
  };
  addEventListener('pointerup', up); addEventListener('pointercancel', up);
  view.addEventListener('click', e => { if (moved) { e.preventDefault(); e.stopPropagation(); moved = false; } }, true);

  let rt; addEventListener('resize', () => { clearTimeout(rt); rt = setTimeout(layout, 80); });
  layout();
})();
